<?php

namespace App\Http\Services\Implementation;

use App\Http\Libraries\System;
use App\Http\Repositories\Abstract\TransaksiRepository;
use App\Http\Services\Abstract\TransaksiService;
use App\Models\AtributPekerjaanNotaris;
use App\Models\AtributPekerjaanPPAT;
use App\Models\HargaPekerjaanNotaris;
use App\Models\HargaPekerjaanPPAT;
use App\Models\KategoriPekerjaan;
use App\Models\Materai;
use App\Models\PekerjaanNotaris;
use App\Models\PekerjaanPPAT;
use App\Models\ProsesPekerjaanNotaris;
use App\Models\ProsesPekerjaanPPAT;
use App\Models\Transaksi;
use App\Models\TransaksiMaterai;
use App\Models\TransaksiCetakSerahTerima;
use App\Models\TransaksiDetailCatatan;
use App\Models\TransaksiDetailProses;
use App\Models\TransaksiDetailProsesNotaris;
use App\Models\TransaksiRiwayatPembayaran;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiServiceImpl implements TransaksiService
{
    protected TransaksiRepository $transaksiRepository;

    // => Start Construct Repository For Depedency Injection
    public function __construct(TransaksiRepository $transaksiRepository)
    {
        $this->transaksiRepository = $transaksiRepository;
    }
    // => End Construct Repository For Depedency Injection

    public function getAllTransaction($jenisId = null)
    {
        $DB = $this->transaksiRepository->findAllTransaction($jenisId);
        $groupedTransactions = $DB->groupBy('no_akta')
            ->map(function ($group) {
                $firstTransaction = $group->first();
                $firstTransaction->pekerjaan_ppat = $group->pluck('pekerjaan_ppat')
                    ->unique()
                    ->implode(', ');

                return $firstTransaction;
            })->values();

        return DataTables::of($groupedTransactions)
            ->editColumn('id', function (Transaksi $transaksi) {
                return System::strEncode($transaksi->id);
            })
            ->editColumn('pekerjan_nama', function (Transaksi $transaksi) {
                if ($transaksi->jenis_pekerjaan_id == 1) {
                    return $transaksi->pekerjaan_notaris;
                } elseif ($transaksi->jenis_pekerjaan_id == 2) {
                    return $transaksi->pekerjaan_ppat;
                } else {
                    return '';
                }
            })
            ->toJson();
    }

    public function getNotarisTransaction($id)
    {

        return $this->transaksiRepository->findNotarisTransaction($id);
    }

    public function getPPATTransaction($no_akta)
    {
        return DB::transaction(function () use ($no_akta) {
            // Ambil data transaksi yang sesuai dengan kondisi
            $transaksi = Transaksi::query()
                ->where('transaksi.status', 1)
                ->where('transaksi.no_akta', $no_akta)
                ->where(function ($query) {
                    $query->whereNull('locked_by')  // Tidak terkunci
                          ->orWhere('locked_by', Auth::id()); // Atau dikunci oleh user yang sama
                })
                ->lockForUpdate() // Kunci untuk mencegah pengeditan bersamaan
                ->get(); // Ambil koleksi data transaksi
    
            if ($transaksi->isEmpty()) {
                // Lempar error jika tidak ada data yang bisa diedit
                throw new \Exception("Transaksi dengan no_akta {$no_akta} sedang diedit oleh pengguna lain.");
            }
    
            // Iterasi setiap item dalam koleksi untuk mengunci data
            foreach ($transaksi as $item) {
                $item->locked_by = Auth::id(); // ID pengguna yang sedang mengedit
                $item->locked_at = now(); // Waktu penguncian
                $item->save(); // Simpan perubahan
            }
    
            // Ambil data tambahan setelah transaksi dikunci
            $results = Transaksi::query()
                ->where('no_akta', $no_akta)
                ->select(
                    'transaksi.*',
                    'petugas.nama as petugas_nama',
                    'petugas.no_telp as petugas_notelp',
                    'jk_petugas.nama as jenis_kelamin_petugas',
                    'pemohon.nik as pemohon_nik',
                    'pemohon.alamat as pemohon_alamat',
                    'pemohon.nama as pemohon_nama',
                    'pemohon.no_telp as pemohon_notelp',
                    'jk_pemohon.nama as jenis_kelamin_pemohon',
                    'pekerjaan_ppat.nama as pekerjaan_nama',
                    'pekerjaan_kategori.id as kategori_id',
                    'pekerjaan_harga.estimasi_waktu as estimasi_waktu'
                )
                ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
                ->join('jenis_kelamin as jk_petugas', 'jk_petugas.id', '=', 'petugas.jenis_kelamin')
                ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
                ->join('jenis_kelamin as jk_pemohon', 'jk_pemohon.id', '=', 'pemohon.jenis_kelamin')
                ->leftJoin('pekerjaan_ppat_harga as pekerjaan_harga', function ($join) {
                    $join->on('pekerjaan_harga.pekerjaan_ppat_id', '=', 'transaksi.pekerjaan_id')
                        ->on('pekerjaan_harga.kategori_pekerjaan_id', '=', 'transaksi.kategori_pekerjaan_id');
                })
                ->leftJoin('pekerjaan_ppat', 'pekerjaan_ppat.id', '=', 'transaksi.pekerjaan_id')
                ->leftJoin('pekerjaan_kategori', 'pekerjaan_kategori.id', '=', 'transaksi.kategori_pekerjaan_id')
                ->where('transaksi.jenis_pekerjaan_id', '=', 2)
                ->get();
    
            // Proses data tambahan untuk materai
            return $results->map(function ($item) {
                $relasi_materai_id = TransaksiMaterai::where('transaksi_id', $item->id)->pluck('materai_id')->toArray();
                if ($relasi_materai_id) {
                    $count_materai_masuk = Materai::whereIn('id', $relasi_materai_id)->where('materai_keluar', 0)->sum('materai_masuk');
                    $count_materai_keluar = Materai::whereIn('id', $relasi_materai_id)->sum('materai_keluar');
                    $total = $count_materai_keluar - $count_materai_masuk;
                    $item->materai_keluar = $total;
                } else {
                    $item->materai_keluar = 0;
                }
                return (object)$item->toArray();
            })->values()->all();
        });
    }

    public function findRiwayatPPAT($no_transaksi)
    {
        $result = TransaksiRiwayatPembayaran::query()
            ->where('no_transaksi', $no_transaksi)
            ->where('status', '1')
            ->get();

        return $result;
    }

    public function findRiwayatPembayaran($transaksi_id)
    {
        $noAkta = Transaksi::where('id', $transaksi_id)->value('no_akta');
        $DB = TransaksiRiwayatPembayaran::where('no_transaksi', $noAkta)
            ->where('status', 1)
            ->get();

        return DataTables::of($DB)
            ->editColumn('id', function (TransaksiRiwayatPembayaran $trp) {
                return System::strEncode($trp->id);
            })
            ->editColumn('jumlah_dibayar', function (TransaksiRiwayatPembayaran $trp) {
                return $trp->jumlah_dibayar;
            })
            ->toJson();
    }

    public function storeNotaris(Request $request, array $date, Materai $materai)
    {
        // => Get Pekerjaan & Kategori From Request
        $pekerjaanId = $request->input('pekerjaan_id');
        $kategoriId = $request->input('kategori_pekerjaan_id');
        $noAktaNotaris = $request->input('nomor_akta');
        $nominalBayar = $request->input('pembayaran_sekarang');

        // => Find Pekerjaan
        $pekerjaanNotaris = PekerjaanNotaris::query()->find($pekerjaanId);
        $kategoriNotaris = KategoriPekerjaan::query()->find($kategoriId);

        // => Validation Pekerjaan Dan Kategori
        if (!$pekerjaanNotaris || !$kategoriNotaris) {
            throw new \Exception('Data pekerjaan dan kategori tidak di temukan');
        }

        // $noAktaIsUnique = $this->checkNomorAktaIsUnique([$noAktaNotaris]);
        // if (!$noAktaIsUnique) {
        //     throw new Exception("Nomor Akta telah digunakan");
        // }

        // Menggabungkan Untuk Laporan Materai
        $pekerjaan = $pekerjaanNotaris->nama;
        $kategori = $kategoriNotaris->nama;

        try {
            DB::beginTransaction();

            // => Update Stok Materai
            $keterangan = "{$request->input('no_akta')} - $pekerjaan - $kategori";
            $lastID = Materai::max('id');
            $materaiSave = Materai::where('id', $lastID)->lockForUpdate()->first();

            $oldStok = $materaiSave->stok_materai;
            $newStok = $oldStok - $request->input('jumlah_materai');
            if ($newStok < 0) {
                throw new Exception("Materai tidak mencukupi !");
            }

            // => Save Materai
            if($request->input('jumlah_materai') > 0){
                $materaiSave = $this->transaksiRepository->saveNotarisMaterai($request, $date['tanggal_daftar'], $newStok, $keterangan);
            }

            // => Melakukan Perhitungan Biaya Layanan
            $biaya_layanan = $request->input('biaya_layanan');
            $biaya_lainnya = $request->input('biaya_lainya');
            $potongan = $request->input('potongan_harga');

            $sub_total = $biaya_layanan + $biaya_lainnya;
            $total = $sub_total - $potongan;

            // => Passing Data Ke Repository
            $passDataToRepository = [
                'tanggal_daftar' => $date['tanggal_daftar'],
                'tanggal_selesai' => $date['tanggal_selesai'],
                'pekerjaan_id' => $pekerjaanId,
                'kategori_id' => $kategoriId,
                'potongan' => $potongan,
                'jatuh_tempo' => $date['jatuh_tempo'],
                'subtotal' => $sub_total,
                'total' => $total,
            ];

            $transaksiId = $this->transaksiRepository->saveNotarisTransaction($request, $passDataToRepository);
            if($request->input('jumlah_materai') > 0){
            $dataRelasi =[
                'materai_id'=>$materaiSave->id,
                'transaksi_id'=> $transaksiId['id'],
                'status'=>1,
            ];
            $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
            }
            $cookie_data = Cookie::get('X-PROSES-NOTARIS');
            $prosesNotaris = json_decode($cookie_data, true);

            if ($prosesNotaris != null) {
                foreach ($prosesNotaris as $proses) {
                    TransaksiDetailProses::create(System::crudIdentity('create', [
                        'transaksi_id' => $transaksiId['id'],
                        'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                        'prosesId' => $proses['prosesId'],
                        'pekerjaanNama' => $proses['pekerjaanNama'],
                        'kategoriNama' => $proses['kategoriNama'],
                        'prosesNama' => $proses['prosesNama'],
                        'atribut' => json_encode($proses['atribut']),
                        'catatan' => $proses['catatan'],
                        'isValidate' => $proses['isValidate'],
                    ]));
                }
            }

            // -- insert history pembayaran
            if ($nominalBayar > 0) {
                $this->transaksiRepository->saveNotarisHistoryTransaction($request, $transaksiId);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception('Error insert notaris :'.$th->getMessage());
        }
    }

    public function storePPAT(Request $request, array $date, Materai $materai)
    {
        $getHitungPajakCookie = Cookie::get('X-HITUNG-PAJAK');
        $cookie_pajak = json_decode($getHitungPajakCookie, true);

        $cookie_data = Cookie::get('X-PROSES-PPAT');
        $prosesPPATCookie = json_decode($cookie_data, true);

        $nominalBayar = $request->input('pembayaran_sekarang');
        $pekerjaanIds = $request->input('pekerjaan_id.pekerjaan_ppat_id', []);
        $kategoriIds = $request->input('kategori_pekerjaan_id.kategori_pekerjaan_ppat_id', []);

        $pekerjaanDuplicate = System::checkArrayDuplicate($pekerjaanIds);
        $kategoriDuplicate = System::checkArrayDuplicate($kategoriIds);

        if ($pekerjaanDuplicate && $kategoriDuplicate) {
            throw new Exception("Pekerjaan dan kategori tidak boleh sama !");
        }

        $pekerjaanNames = PekerjaanPPAT::whereIn('id', $pekerjaanIds)
            ->pluck('nama')
            ->join(', ');

        $kategoriNames = KategoriPekerjaan::whereIn('id', $kategoriIds)
            ->pluck('nama')
            ->join(', ');

        $biayaLayanans = $request->input('biaya_layanan.biaya_layanan_ppat', []);
        $biayaLainnya = $request->input('biaya_lainya.biaya_lainya_ppat', []);
        $potongan = (int) $request->input('potongan_harga', 0);
        $jumlahMaterai = (int) $request->input('jumlah_materai', 0);




        $statusIds = $request->input('transaksi_status.status_id', []);
        $judulPpats = $request->input('transaksi_status.judul', []);
        $noAktaPpats = $request->input('transaksi_status.no_akta', []);
        $tglAktaPpats = $request->input('transaksi_status.tgl_akta', []);

        // $noAktaPpatDuplicate = System::checkArrayDuplicate($noAktaPpats);
        // if ($noAktaPpatDuplicate) {
        //     throw new Exception("Nomor Akta PPAT tidak boleh sama");
        // }

        // $noAktaIsUnique = $this->checkNomorAktaIsUnique($noAktaPpats);
        // if (!$noAktaIsUnique) {
        //     throw new Exception("Nomor Akta telah digunakan");
        // }

        try {
            DB::beginTransaction();
            $lastID = Materai::max('id');
            $materaiSave = Materai::where('id', $lastID)->lockForUpdate()->first();

            $oldStok = $materaiSave->stok_materai;
            $newStok = $oldStok - $jumlahMaterai;
            if ($newStok < 0) {
                throw new Exception("Materai tidak mencukupi !");
            }

            $noAkta = $request->input('no_akta');
            $keterangan = "{$noAkta} - $pekerjaanNames - $kategoriNames";

            // -- insert materai
            if($jumlahMaterai > 0){
            $materaiSave = $this->transaksiRepository->savePPATMaterai($request, $date['tanggal_daftar'],$newStok, $keterangan);
            }// -- insert transaksi data
            foreach ($pekerjaanIds as $index => $pekerjaanId) {
                $kategoriId = $kategoriIds[$index] ?? null;
                $biayaLayanan = (int) $biayaLayanans[$index];
                $biayaLain = (int) ($biayaLainnya[$index] ?? 0);

                $statusId = $statusIds[$index] ?: 1;
                $judulPpat = $judulPpats[$index];
                $noAktaPpat = $noAktaPpats[$index];
                $tglAktaPpat = $tglAktaPpats[$index];

                $pekerjaanPPAT = PekerjaanPPAT::query()->find($pekerjaanId);
                $kategoriPPAT = KategoriPekerjaan::query()->find($kategoriId);

                if (!$pekerjaanPPAT || !$kategoriPPAT) {
                    throw new Exception("Data pekerjaan dan kategori tidak di temukan !");
                }

                $cookiePajak = $cookie_pajak[$index] ?? [];
                $acuan_nilai_pajak = isset($cookiePajak['acuanNilaiPajak']) ? (int) $cookiePajak['acuanNilaiPajak'] : null;
                $jenisPajak = $cookiePajak['jenis_pajak'] ?? null;
                $nilai_pengurang = isset($cookiePajak['nilaiPengurang']) ? (int) $cookiePajak['nilaiPengurang'] : null;
                $pajakPihakPertama = isset($cookiePajak['pihak_pertama']) ? (int) $cookiePajak['pihak_pertama'] : null;
                $pajakPihakKedua = isset($cookiePajak['pihak_kedua']) ? (int) $cookiePajak['pihak_kedua'] : null;
                $besaran_tidak_kena_pajak = isset($cookiePajak['nilai_tidak_kena_pajak']) ? (int) $cookiePajak['besaran_tidak_kena_pajak'] : null;

                $sub_total = $biayaLayanan + $biayaLain;
                $potonganPerItem = (int) floor($potongan / count($pekerjaanIds));
                $total = $sub_total - $potonganPerItem;

                $passToRepository = [
                    'tanggal_daftar' => $date['tanggal_daftar'],
                    'tanggal_selesai' => $date['tanggal_selesai'],
                    'pekerjaan_id' => $pekerjaanId,
                    'kategori_pekerjaan_id' => $kategoriId,
                    'jenis_pajak' => $jenisPajak,
                    'acuan_hitung_pajak' => $acuan_nilai_pajak,
                    'nilai_pengurang' => $nilai_pengurang,
                    'besaran_pajak_pihak_pertama' => $pajakPihakPertama,
                    'besaran_pajak_pihak_kedua' => $pajakPihakKedua,
                    'besaran_tidak_kena_pajak' => $besaran_tidak_kena_pajak,
                    'biaya_layanan' => $biayaLayanan,
                    'biaya_lainya' => $biayaLain,
                    'materai_id' => $materaiSave->id,
                    'potongan_biaya' => $potonganPerItem,
                    'jatuh_tempo' => $date['jatuh_tempo'],
                    'subtotal' => $sub_total,
                    'total' => $total,
                    'keterangan' => $request->input('keterangan'),
                    'status_id' => $statusId,
                    'judul_ppat' => $judulPpat,
                    'no_akta_ppat' => $noAktaPpat,
                    'tgl_akta_ppat' => $tglAktaPpat,
                ];

            $transaksi = $this->transaksiRepository->savePPATTransaction($request, $passToRepository);
            if($jumlahMaterai > 0){
                $dataRelasi =[
                    'materai_id'=>$materaiSave->id,
                    'transaksi_id'=> $transaksi['id'],
                    'status'=>1,
                ];
    
                $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
            }


                $prosesPPAT = collect($prosesPPATCookie)->filter(function ($val) use ($transaksi) {
                    return $val['pekerjaanId'] == $transaksi->pekerjaan_id &&
                        $val['kategoriPekerjaanId'] == $transaksi->kategori_pekerjaan_id;
                })->toArray();

                if ($prosesPPAT != null) {
                    foreach ($prosesPPAT as $proses) {
                        TransaksiDetailProses::create(System::crudIdentity('create', [
                            'transaksi_id' => $transaksi['id'],
                            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                            'prosesId' => $proses['prosesId'],
                            'pekerjaanNama' => $proses['pekerjaanNama'],
                            'kategoriNama' => $proses['kategoriNama'],
                            'prosesNama' => $proses['prosesNama'],
                            'atribut' => json_encode($proses['atribut']),
                            'catatan' => $proses['catatan'],
                            'isValidate' => $proses['isValidate'],
                        ]));
                    }
                }
            }

            // -- insert history pembayaran
            if ($nominalBayar > 0) {
                $this->transaksiRepository->savePPATHistoryTransaction($request, $noAkta);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception("Error insert ppat !");
        }
    }

    public function updateNotaris(Request $request, $date, $materai, $id)
    {
        $data = $request->all();
        $nominalBayar = $request->input('pembayaran_sekarang');
        $jumlahMaterai = $data['jumlah_materai'];

        if (!$materai) {
            throw new Exception('Data materai tidak ditemukan');
        }


        // $noAktaIsUnique = $this->checkNomorAktaIsUnique([$data['nomor_akta']], [$id]);
        // if (!$noAktaIsUnique) {
        //     throw new Exception("Nomor Akta telah digunakan");
        // }

        $petugasId = is_numeric($data['petugas_id']) ? $data['petugas_id'] : System::strDecode($data['petugas_id']);
        $pemohonId = is_numeric($data['pemohon_id']) ? $data['pemohon_id'] : System::strDecode($data['pemohon_id']);

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::findOrFail($id);

            if (isset($data['jumlah_materai']) && $data['jumlah_materai'] > 0) {
                $pekerjaanNotaris = PekerjaanNotaris::findOrFail($data['pekerjaan_id']);
                $kategoriNotaris = KategoriPekerjaan::findOrFail($data['kategori_pekerjaan_id']);

                $keterangan = sprintf(
                    '%s - %s - %s',
                    $data['no_akta'],
                    $pekerjaanNotaris->nama,
                    $kategoriNotaris->nama
                );

                $relasi_materai_id = TransaksiMaterai::where('transaksi_id',$transaksi->id)->pluck('materai_id')
                ->toArray();
                $materai_masuk = Materai::whereIn('id',$relasi_materai_id)->sum('materai_masuk');
                $materai_keluar = Materai::whereIn('id',$relasi_materai_id)->sum('materai_keluar');
                $lastID = Materai::max('id');
                $stok_materai_lama = Materai::where('id',$lastID)->lockForUpdate()->first();
                $selisih_input =($materai_keluar - $materai_masuk) - $jumlahMaterai;

                if ($selisih_input > 0) {
                    $stok = $stok_materai_lama->stok_materai + $selisih_input;
                    if ($stok < 0) {
                        throw new Exception('Maaf, stok materai tidak cukup');
                    }
                    $newMaterai =Materai::query()
                    ->create(
                        System::crudIdentity('create', [
                            'date' => $date['tanggal_daftar'],
                            'materai_masuk' => $selisih_input,
                            'materai_keluar' => 0,
                            'stok_materai' => $stok,
                            'is_transaksi' => 1,
                            'keterangan' => $keterangan."-perubahan",
                            'petugas_id' => $petugasId,
                        ])
                );
                $dataRelasi =[
                    'materai_id'=>$newMaterai->id,
                    'transaksi_id'=> $transaksi->id,
                    'status'=>1,
                ];
                $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
                } elseif ($selisih_input <= 0) {
                    if ($selisih_input > $materai->stok_materai) {
                        throw new Exception('Maaf, stok materai tidak mencukupi');
                    }
                    $selisih_input = abs($selisih_input);
                    $stok = $stok_materai_lama->stok_materai - $selisih_input;
                    if ($stok < 0) {
                        throw new Exception('Maaf, stok materai tidak cukup');
                    }
                    $newMaterai =Materai::query()
                    ->create(
                        System::crudIdentity('create', [
                            'date' => $date['tanggal_daftar'],
                            'materai_masuk' => 0,
                            'materai_keluar' => $selisih_input,
                            'stok_materai' => $stok,
                            'is_transaksi' => 1,
                            'keterangan' => $keterangan."-perubahan",
                            'petugas_id' => $petugasId,
                        ])
                );
                $dataRelasi =[
                    'materai_id'=>$newMaterai->id,
                    'transaksi_id'=> $transaksi->id,
                    'status'=>1,
                ];
                $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
                }
            }


            $biayaLayanan = $data['biaya_layanan'];
            $biayaLainnya = $data['biaya_lainya'] ?? 0;
            $potongan = $data['potongan_harga'];
            $subTotal = $biayaLayanan + $biayaLainnya;
            $total = $subTotal - $potongan;

            $transaksi->update(System::crudIdentity('update', [
                'no_akta' => $request->input('no_akta'),
                'tanggal_daftar' => $date['tanggal_daftar'],
                'tanggal_selesai' => $date['tanggal_selesai'],
                'pemohon_id' => $pemohonId,
                'jenis_pekerjaan_id' => $data['jenis_pekerjaan_id'],
                'pekerjaan_id' => $data['pekerjaan_id'],
                'kategori_pekerjaan_id' => $data['kategori_pekerjaan_id'],
                'biaya_layanan' => $biayaLayanan,
                'biaya_lainnya' => $biayaLainnya,
                'status_id' => $data['status_id'],
                'petugas_id' => $petugasId,
                'potongan_biaya' => $potongan,
                'jatuh_tempo' => $date['jatuh_tempo'],
                'jenis_pembayaran_id' => $data['jenis_pembayaran_id'],
                'sub_total' => $subTotal,
                'total' => $total,
                'keterangan' => $request->input('keterangan'),
                'judul' => $request->input('judul'),
                'nomor_akta' => $request->input('nomor_akta'),
                'tanggal_akta' => $request->input('tanggal_akta'),
            ]));

            $cookie_data = Cookie::get('X-PROSES-NOTARIS');
            $prosesNotaris = json_decode($cookie_data, true);

            if ($prosesNotaris != null) {
                foreach ($prosesNotaris as $proses) {
                    $getId = @$proses['prosesId'] ?: @$proses['proses_id'];
                    $proses_id = TransaksiDetailProses::where('prosesId', $getId)
                        ->where('transaksi_id', $id)
                        ->value('id');

                    if (!$proses_id) {
                        TransaksiDetailProses::create(System::crudIdentity('create', [
                            'transaksi_id' => $id,
                            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                            'prosesId' => $getId,
                            'pekerjaanNama' => $proses['pekerjaanNama'],
                            'kategoriNama' => $proses['kategoriNama'],
                            'prosesNama' => $proses['prosesNama'],
                            'atribut' => json_encode($proses['atribut']),
                            'catatan' => $proses['catatan'],
                            'isValidate' => $proses['isValidate'],
                        ]));
                    } else {
                        TransaksiDetailProses::where('id', $proses_id)
                            ->update(System::crudIdentity('update', [
                                'transaksi_id' => $id,
                                'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                                'prosesId' => $getId,
                                'pekerjaanNama' => $proses['pekerjaanNama'],
                                'kategoriNama' => $proses['kategoriNama'],
                                'prosesNama' => $proses['prosesNama'],
                                'atribut' => json_encode($proses['atribut']),
                                'catatan' => $proses['catatan'],
                                'isValidate' => $proses['isValidate'],
                            ]));
                    }
                }
            }

            // -- insert history pembayaran
            if ($nominalBayar > 0) {
                $this->transaksiRepository->saveNotarisHistoryTransaction($request, $id);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            // if (config('app.debug')) {
            //     echo "Error update notaris: " . $e->getMessage() . 
            //         " in file " . $e->getFile() . 
            //         " on line " . $e->getLine();
            // }
            throw new Exception("Error update notaris: " . $e->getMessage());
        }
    }

    public function updatePPAT(Request $request, $date, $materai, $no_akta)
    {
        /**
         *  - get transaksi by no akta
         *  - filtering and delete transaksi
         *  - update transaksi by index
         *  - unset data updated from request and reindexing
         *  - insert new data transaksi
         */

        $getHitungPajakCookie = Cookie::get('X-HITUNG-PAJAK');
        $cookie_pajak = json_decode($getHitungPajakCookie, true);

        $transaksi = Transaksi::where('no_akta', $no_akta)->get();
        if ($transaksi->isEmpty()) {
            throw new \Exception('Transaksi dengan no_akta tersebut tidak ditemukan.');
        }

        $nominalBayar = $request->input('pembayaran_sekarang');
        $pekerjaanIds = $request->input('pekerjaan_id.pekerjaan_ppat_id', []);
        $kategoriIds = $request->input('kategori_pekerjaan_id.kategori_pekerjaan_ppat_id', []);

        $pekerjaanDuplicate = System::checkArrayDuplicate($pekerjaanIds);
        $kategoriDuplicate = System::checkArrayDuplicate($kategoriIds);

        if ($pekerjaanDuplicate && $kategoriDuplicate) {
            throw new Exception("Pekerjaan dan kategori tidak boleh sama !");
        }

        $biayaLayanans = $request->input('biaya_layanan.biaya_layanan_ppat', []);
        $biayaLainnya = $request->input('biaya_lainya.biaya_lainya_ppat', []);
        $transaksiPpatIds = $request->input('transaksi_ppat_id', []);

        $statusIds = $request->input('transaksi_status.status_id', []);
        $judulPpats = $request->input('transaksi_status.judul', []);
        $noAktaPpats = $request->input('transaksi_status.no_akta', []);
        $tglAktaPpats = $request->input('transaksi_status.tgl_akta', []);

        $noAktaPpatDuplicate = System::checkArrayDuplicate($noAktaPpats);
        // if ($noAktaPpatDuplicate) {
        //     throw new Exception("Nomor Akta PPAT tidak boleh sama");
        // }

        // $noAktaIsUnique = $this->checkNomorAktaIsUnique($noAktaPpats, $transaksiPpatIds);
        // if (!$noAktaIsUnique) {
        //     throw new Exception("Nomor Akta telah digunakan");
        // }

        $potongan = $request->input('potongan_harga', 0);
        $jumlahMaterai = $request->input('jumlah_materai', 0);
        $keteranganTransaksi = $request->input('keterangan');

        $cookie_data = Cookie::get('X-PROSES-PPAT');
        $prosesPPATCookie = json_decode($cookie_data, true);

        $pekerjaanNames = PekerjaanPPAT::whereIn('id', $pekerjaanIds)
            ->pluck('nama')
            ->join(', ');

        $kategoriNames = KategoriPekerjaan::whereIn('id', $kategoriIds)
            ->pluck('nama')
            ->join(', ');

        $keterangan = "{$no_akta} - $pekerjaanNames - $kategoriNames";

        $petugasId = $request->input('petugas_id');
        $petugasId = is_numeric($petugasId) ? $petugasId : System::strDecode($petugasId);

        try {
            DB::beginTransaction();

            // -- update materai
            $relasi_materai_id = TransaksiMaterai::where('transaksi_id',$transaksi[0]->id)->pluck('materai_id')
                ->toArray();
                $materai_masuk = Materai::whereIn('id',$relasi_materai_id)->sum('materai_masuk');
                $materai_keluar = Materai::whereIn('id',$relasi_materai_id)->sum('materai_keluar');
                $lastID = Materai::max('id');
                $stok_materai_lama = Materai::where('id',$lastID)->lockForUpdate()->first();
                $selisih_input =($materai_keluar - $materai_masuk) - $jumlahMaterai;

                if ($selisih_input > 0) {
                    $stok = $stok_materai_lama->stok_materai + $selisih_input;
                    if ($stok < 0) {
                        throw new Exception('Maaf, stok materai tidak cukup');
                    }
                    $newMaterai =Materai::query()
                    ->create(
                        System::crudIdentity('create', [
                            'date' => $date['tanggal_daftar'],
                            'materai_masuk' => $selisih_input,
                            'materai_keluar' => 0,
                            'stok_materai' => $stok,
                            'is_transaksi' => 1,
                            'keterangan' => $keterangan."-perubahan",
                            'petugas_id' => $petugasId,
                        ])
                    );

                $dataRelasi =[
                    'materai_id'=>$newMaterai->id,
                    'transaksi_id'=> $transaksi[0]->id,
                    'status'=>1,
                ];

                $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
                } elseif ($selisih_input <= 0) {
                    if ($selisih_input > $materai->stok_materai) {
                        throw new Exception('Maaf, stok materai tidak mencukupi');
                    }
                    $selisih_input = abs($selisih_input);
                    $stok = $stok_materai_lama->stok_materai - $selisih_input;
                    if ($stok < 0) {
                        throw new Exception('Maaf, stok materai tidak cukup');
                    }

                    $newMaterai =Materai::query()
                    ->create(
                        System::crudIdentity('create', [
                            'date' => $date['tanggal_daftar'],
                            'materai_masuk' => 0,
                            'materai_keluar' => $selisih_input,
                            'stok_materai' => $stok,
                            'is_transaksi' => 1,
                            'keterangan' => $keterangan."-perubahan",
                            'petugas_id' => $petugasId,
                        ])
                    );

                $dataRelasi =[
                    'materai_id'=>$newMaterai->id,
                    'transaksi_id'=> $transaksi[0]->id,
                    'status'=>1,
                ];
                $this->transaksiRepository->saveRelasiMateraiTransaksi($dataRelasi);
            }

            // -- potongan biaya / jumlah rows
            $potonganBiaya = (int) floor($potongan / count($pekerjaanIds));
            $transaksiPpatIds = collect($transaksiPpatIds)
                ->filter(function ($item) {
                    return !($item == null || $item == 0);
                })
                ->toArray();
            // -- delete transaksi
            $transaksi = $transaksi->filter(function ($item) use ($transaksiPpatIds) {
                if (in_array($item->id, $transaksiPpatIds)) return true;
                /**
                 * - transaksi
                 * - transaksi_detail_proses
                 * - transaksi_cetak_serah_terima
                 */

                Transaksi::where('id', $item->id)
                    ->delete();

                TransaksiDetailProses::where('transaksi_id', $item->id)
                    ->delete();

                TransaksiCetakSerahTerima::where('no_transaksi', $item->no_akta)
                    ->delete();

                return false;
            })->values();

            // -- update transaksi
            foreach ($transaksi as $index => $singleTransaksi) {
                $pekerjaanId = $pekerjaanIds[$index] ?? null;
                $kategoriId = $kategoriIds[$index] ?? null;
                $biayaLayanan = $biayaLayanans[$index] ?? 0;
                $biayaLain = $biayaLainnya[$index] ?? 0;
                $cookiePajak = $cookie_pajak[$index] ?? [];

                $statusId = $statusIds[$index] ?: 1;
                $judulPpat = $judulPpats[$index];
                $noAktaPpat = $noAktaPpats[$index];
                $tglAktaPpat = $tglAktaPpats[$index];

                // -- unsetter insert
                unset(
                    $pekerjaanIds[$index],
                    $kategoriIds[$index],
                    $biayaLayanans[$index],
                    $biayaLainnya[$index],
                    $cookie_pajak[$index],
                    $statusIds[$index],
                    $judulPpats[$index],
                    $noAktaPpats[$index],
                    $tglAktaPpats[$index]
                );

                $acuan_nilai_pajak = @$cookiePajak['acuanNilaiPajak'] ?? null;
                $jenisPajak = @$cookiePajak['jenis_pajak'] ?? null;
                $nilai_pengurang = @$cookiePajak['nilaiPengurang'] ?? null;
                $pajakPihakPertama = @$cookiePajak['pihak_pertama'] ?? null;
                $pajakPihakKedua = @$cookiePajak['pihak_kedua'] ?? null;
                $besaranTidakKenaPajak = @$cookiePajak['besaran_tidak_kena_pajak'] ?? null;

                $sub_total = $biayaLayanan + $biayaLain;
                $total = $sub_total - $potonganBiaya;  // Distribusi potongan

                $passToRepository = [
                    'tanggal_daftar' => $date['tanggal_daftar'],
                    'tanggal_selesai' => $date['tanggal_selesai'],
                    'pekerjaan_id' => $pekerjaanId,
                    'kategori_pekerjaan_id' => $kategoriId,
                    'jenis_pajak' => $jenisPajak,
                    'acuan_hitung_pajak' => $acuan_nilai_pajak,
                    'nilai_pengurang' => $nilai_pengurang,
                    'besaran_pajak_pihak_pertama' => $pajakPihakPertama,
                    'besaran_pajak_pihak_kedua' => $pajakPihakKedua,
                    'besaran_tidak_kena_pajak' => $jenisPajak != 5 ? null : $besaranTidakKenaPajak,
                    'biaya_layanan' => $biayaLayanan,
                    'biaya_lainya' => $biayaLain,
                    'potongan_biaya' => $potonganBiaya,
                    'jatuh_tempo' => $date['jatuh_tempo'],
                    'subtotal' => $sub_total,
                    'total' => $total,
                    'keterangan' => $keteranganTransaksi,
                    'status_id' => $statusId,
                    'judul_ppat' => $judulPpat,
                    'no_akta_ppat' => $noAktaPpat,
                    'tgl_akta_ppat' => $tglAktaPpat,
                ];

                $this->transaksiRepository->updatePPATTransaction($request, $passToRepository, $singleTransaksi->id);

                $prosesPPAT = collect($prosesPPATCookie)->filter(function ($val) use ($singleTransaksi) {
                    return $val['pekerjaanId'] == $singleTransaksi->pekerjaan_id &&
                        $val['kategoriPekerjaanId'] == $singleTransaksi->kategori_pekerjaan_id;
                })->toArray();

                if ($prosesPPAT != null) {
                    foreach ($prosesPPAT as $proses) {
                        $prosesId = @$proses['prosesId'] ?: @$proses['proses_id'];
                        $detProsesId = TransaksiDetailProses::where('prosesId', $prosesId)
                            ->where('transaksi_id', $singleTransaksi->id)
                            ->value('id');

                        if (!$detProsesId) {
                            TransaksiDetailProses::create(System::crudIdentity('create', [
                                'transaksi_id' => $singleTransaksi->id,
                                'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                                'prosesId' => $prosesId,
                                'pekerjaanNama' => $proses['pekerjaanNama'],
                                'kategoriNama' => $proses['kategoriNama'],
                                'prosesNama' => $proses['prosesNama'],
                                'atribut' => json_encode($proses['atribut']),
                                'catatan' => $proses['catatan'],
                                'isValidate' => $proses['isValidate'],
                            ]));
                        } else {
                            TransaksiDetailProses::where('id', $detProsesId)->update(
                                System::crudIdentity('update', [
                                    'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                                    'prosesId' => $prosesId,
                                    'pekerjaanNama' => $proses['pekerjaanNama'],
                                    'kategoriNama' => $proses['kategoriNama'],
                                    'prosesNama' => $proses['prosesNama'],
                                    'atribut' => json_encode($proses['atribut']),
                                    'catatan' => $proses['catatan'],
                                    'isValidate' => $proses['isValidate'],
                                ])
                            );
                        }
                    }
                }
            }

            // -- reindexing key
            $pekerjaanIds = array_values($pekerjaanIds);
            $kategoriIds = array_values($kategoriIds);
            $biayaLayanans = array_values($biayaLayanans);
            $biayaLainnya = array_values($biayaLainnya);
            $statusIds = array_values($statusIds);
            $judulPpats = array_values($judulPpats);
            $noAktaPpats = array_values($noAktaPpats);
            $tglAktaPpats = array_values($tglAktaPpats);
            $cookie_pajak = array_values($cookie_pajak ?? []);

            // -- insert transaksi
            foreach ($pekerjaanIds as $key => $pekerjaanId) {
                $kategoriId = @$kategoriIds[$key] ?? null;
                $biayaLayanan = @$biayaLayanans[$key] ?? 0;
                $biayaLain = @$biayaLainnya[$key] ?? 0;

                $statusId = @$statusIds[$index] ?: 1;
                $judulPpat = @$judulPpats[$index];
                $noAktaPpat = @$noAktaPpats[$index];
                $tglAktaPpat = @$tglAktaPpats[$index];

                $pekerjaanPPAT = PekerjaanPPAT::query()->find($pekerjaanId);
                $kategoriPPAT = KategoriPekerjaan::query()->find($kategoriId);

                if (!$pekerjaanPPAT || !$kategoriPPAT) {
                    throw new Exception("Data pekerjaan dan kategori tidak di temukan !");
                }

                $cookiePajak = @$cookie_pajak[$key] ?? [];
                $acuan_nilai_pajak = @$cookiePajak['acuanNilaiPajak'] ?? null;
                $jenisPajak = @$cookiePajak['jenis_pajak'] ?? null;
                $nilai_pengurang = @$cookiePajak['nilaiPengurang'] ?? null;
                $pajakPihakPertama = @$cookiePajak['pihak_pertama'] ?? null;
                $pajakPihakKedua = @$cookiePajak['pihak_kedua'] ?? null;
                $besaran_tidak_kena_pajak = @$cookiePajak['besaran_tidak_kena_pajak'] ?? null;

                $sub_total = $biayaLayanan + $biayaLain;
                $total = $sub_total - $potonganBiaya;

                $passToRepository = [
                    'tanggal_daftar' => $date['tanggal_daftar'],
                    'tanggal_selesai' => $date['tanggal_selesai'],
                    'pekerjaan_id' => $pekerjaanId,
                    'kategori_pekerjaan_id' => $kategoriId,
                    'jenis_pajak' => $jenisPajak,
                    'acuan_hitung_pajak' => $acuan_nilai_pajak,
                    'nilai_pengurang' => $nilai_pengurang,
                    'besaran_pajak_pihak_pertama' => $pajakPihakPertama,
                    'besaran_pajak_pihak_kedua' => $pajakPihakKedua,
                    'besaran_tidak_kena_pajak' => $besaran_tidak_kena_pajak,
                    'biaya_layanan' => $biayaLayanan,
                    'biaya_lainya' => $biayaLain,
                    'potongan_biaya' => $potonganBiaya,
                    'jatuh_tempo' => $date['jatuh_tempo'],
                    'subtotal' => $sub_total,
                    'total' => $total,
                    'keterangan' => $request->input('keterangan'),
                    'status_id' => $statusId,
                    'judul_ppat' => $judulPpat,
                    'no_akta_ppat' => $noAktaPpat,
                    'tgl_akta_ppat' => $tglAktaPpat,
                ];

                $transaksi = $this->transaksiRepository->savePPATTransaction($request, $passToRepository);

                $prosesPPAT = collect($prosesPPATCookie)->filter(function ($val) use ($transaksi) {
                    return $val['pekerjaanId'] == $transaksi->pekerjaan_id &&
                        $val['kategoriPekerjaanId'] == $transaksi->kategori_pekerjaan_id;
                })->toArray();

                if ($prosesPPAT != null) {
                    foreach ($prosesPPAT as $proses) {
                        TransaksiDetailProses::create(System::crudIdentity('create', [
                            'transaksi_id' => $transaksi['id'],
                            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
                            'prosesId' => $proses['prosesId'],
                            'pekerjaanNama' => $proses['pekerjaanNama'],
                            'kategoriNama' => $proses['kategoriNama'],
                            'prosesNama' => $proses['prosesNama'],
                            'atribut' => json_encode($proses['atribut']),
                            'catatan' => $proses['catatan'],
                            'isValidate' => $proses['isValidate'],
                        ]));
                    }
                }
            }

            // -- insert history transaksi
            if ($nominalBayar > 0) {
                $this->transaksiRepository->savePPATHistoryTransaction($request, $no_akta);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (config('app.debug')) {
                echo "Error update notaris: " . $e->getMessage() . 
                    " in file " . $e->getFile() . 
                    " on line " . $e->getLine();
            }
            throw new Exception("Error update ppat :". $e->getMessage());
        }
    }

    public function getDetailPembayaran($transaksi_id)
    {
        $noAkta = Transaksi::where('id', $transaksi_id)->value('no_akta');
        $jumlahBayar = TransaksiRiwayatPembayaran::where('no_transaksi', $noAkta)
            ->where('status', 1)
            ->pluck('jumlah_dibayar')
            ->toArray();

        $totalBayar = array_sum($jumlahBayar);
        $totalTagihan = Transaksi::where('no_akta', $noAkta)
            ->where('status', 1)
            ->sum('total');

        $sisaHutang = $totalTagihan - $totalBayar;

        $data['no_akta'] = $noAkta;
        $data['total_bayar'] = $totalBayar;
        $data['sisa_hutang'] = $sisaHutang;

        return $data;
    }

    public function getPekerjaan($selected_id, $jenis_pekerjaan)
    {

        if ($jenis_pekerjaan == '1') {
            $data = PekerjaanNotaris::query()->select(columns: ['id', 'nama as text'])->where('status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Pekerjaan--'];
            if ($selected_id != '') {
                foreach ($data as $key => $value) {
                    if ($value['id'] == $selected_id) {
                        $data[$key]['selected'] = true;
                    }
                }
            }
            $data = collect($data)->sortBy('id')->values()->all();
        } else {
            $data = PekerjaanPPAT::query()->select(columns: ['id', 'nama as text'])->where('status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Pekerjaan--'];
            if ($selected_id != '') {
                foreach ($data as $key => $value) {
                    if ($value['id'] == $selected_id) {
                        $data[$key]['selected'] = true;
                    }
                }
            }
            $data = collect($data)->sortBy('id')->values()->all();
        }

        return $data;
    }

    public function getKategori($selected_id, $jenis_pekerjaan, $pekerjaan_id) {}

    public function fetchNotarsiProses($pekerjaan_id, $detailProses)
    {
        $DB = ProsesPekerjaanNotaris::where('pekerjaan_notaris_id', $pekerjaan_id)->where('status', 1)->get();

        $datatable = DataTables::of($DB)
            ->editColumn('id', function (ProsesPekerjaanNotaris $proses) {
                return $proses->id;
            });

        if ($detailProses) {
            $datatable->addColumn('status_proses', function (ProsesPekerjaanNotaris $proses) use ($detailProses, $pekerjaan_id) {
                $matchingDetail = array_filter($detailProses, function ($detail) use ($proses, $pekerjaan_id) {
                    return $detail['proses_id'] == $proses->id &&
                        $detail['jenis_pekerjaan_id'] == 1;
                    // $detail['pekerjaan_id'] == $pekerjaan_id;
                });

                if (! empty($matchingDetail)) {
                    $firstMatch = reset($matchingDetail);
                    return $firstMatch['isValidate'];
                }
            });
        }

        return $datatable;
    }

    public function fetchPPATProses($pekerjaan_id, $kategoriId, $detailProses)
    {
        $DB = ProsesPekerjaanPPAT::where('pekerjaan_ppat_id', $pekerjaan_id)
            ->where('status', 1)
            ->get();

        $datatable = DataTables::of($DB)
            ->editColumn('id', function (ProsesPekerjaanPPAT $proses) {
                return $proses->id;
            });

        // Mendapatkan Data Dari Cookie Untuk Proses Detail Proses
        if ($detailProses) {
            $datatable->addColumn('status_proses', function (ProsesPekerjaanPPAT $proses) use ($detailProses, $kategoriId) {
                $matchingDetail = array_filter($detailProses, function ($detail) use ($proses, $kategoriId) {
                    return $detail['proses_id'] == $proses->id
                        && $detail['pekerjaanId'] == $proses->pekerjaan_ppat_id
                        && $detail['kategoriPekerjaanId'] == $kategoriId
                        && $detail['jenis_pekerjaan_id'] == 2;
                });

                if (!empty($matchingDetail)) {
                    $firstMatch = reset($matchingDetail);

                    return $firstMatch['isValidate'];
                }
            });
        }
        return $datatable;
    }

    public function getDetailKategori($jenis_pekerjaan, $pekerjaan_id, $kategori_id)
    {
        if ($jenis_pekerjaan == '1') {
            $data = HargaPekerjaanNotaris::where('pekerjaan_notaris_id', $pekerjaan_id)->where('kategori_pekerjaan_id', $kategori_id)->first();

            return $data;
        } else {
            $data = HargaPekerjaanPPAT::where('pekerjaan_ppat_id', $pekerjaan_id)->where('kategori_pekerjaan_id', $kategori_id)->first();

            return $data;
        }
    }

    public function getDetailProses(array $setDataToService)
    {
        $id = $setDataToService['id'];
        $jenis_pekerjaan = $setDataToService['jenis_pekerjaan'];
        $pekerjaan_id = $setDataToService['pekerjaan_id'];
        $kategori_id = $setDataToService['kategori_id'];
        $detailProses = $setDataToService['detailProses'] ?? [];
        $listDetailProses = $setDataToService['listDetailProses'] ?? [];

        // Ambil data berdasarkan jenis pekerjaan
        if ($jenis_pekerjaan == '1') {
            $proses = ProsesPekerjaanNotaris::query()->find($id);
            $pekerjaan = PekerjaanNotaris::query()->find($pekerjaan_id);
            $kategori = KategoriPekerjaan::query()->find($kategori_id);
            $atribut = AtributPekerjaanNotaris::query()
                ->where('proses_pekerjaan_notaris_id', $proses->id)
                ->get() ?? collect();
        } else {
            $proses = ProsesPekerjaanPPAT::query()->find($id);
            $pekerjaan = PekerjaanPPAT::query()->find($pekerjaan_id);
            $kategori = KategoriPekerjaan::query()->find($kategori_id);
            $atribut = AtributPekerjaanPPAT::query()
                ->where('proses_pekerjaan_ppat_id', $proses->id)
                ->get() ?? collect();
        }

        // Validasi jika data tidak ditemukan
        if (! $proses || ! $pekerjaan || ! $kategori) {
            return handleResponseServer([
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        // => Data Untuk Response
        $data = [
            'proses' => $proses,
            'pekerjaan' => $pekerjaan,
            'kategori' => $kategori,
            'atribut' => $atribut,
            'list_validasi' => json_decode($setDataToService['list_detail'] ?? '[]', true),
        ];

        // Cek dan ambil catatan dan status validasi jika ada detail proses
        if ($detailProses && $listDetailProses) {
            $matchingDetail = array_filter($detailProses, function ($detail) use ($id, $pekerjaan_id, $jenis_pekerjaan) {
                return $detail['proses_id'] == $id &&
                    $detail['jenis_pekerjaan_id'] == $jenis_pekerjaan &&
                    $detail['pekerjaan_id'] == $pekerjaan_id;
            });

            if (! empty($matchingDetail)) {
                $firstMatch = reset($matchingDetail);
                $matchingListDetail = array_filter($listDetailProses, function ($listDetail) use ($id, $pekerjaan_id, $jenis_pekerjaan) {
                    return $listDetail['proses_id'] == $id &&
                        $listDetail['jenis_pekerjaan_id'] == $jenis_pekerjaan &&
                        $listDetail['pekerjaan_id'] == $pekerjaan_id;
                });

                if (! empty($matchingListDetail)) {
                    $firstListMatch = reset($matchingListDetail);
                    $data['catatan'] = $firstListMatch['catatan'];
                    $data['status_validasi'] = $firstListMatch['status_validasi'];
                }
            }
        }

        return $data;
    }

    private function checkNomorAktaIsUnique($arr = [], $exceptId = [])
    {
        $exist = Transaksi::whereIn('nomor_akta', $arr)
            ->whereNotIn('id', $exceptId)
            ->exists();

        return !$exist;
    }
}
