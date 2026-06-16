<?php

namespace App\Http\Repositories\Implementation;

use App\Http\Libraries\System;
use App\Http\Repositories\Abstract\TransaksiRepository;
use App\Models\Materai;
use App\Models\Transaksi;
use App\Models\TransaksiMaterai;
use App\Models\TransaksiRiwayatPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiRepositoryImpl implements TransaksiRepository
{
    public function findAllTransaction($jenis = null)
    {
        $data = Transaksi::query()->select(
            'transaksi.*',
            'pekerjaan_notaris.nama as pekerjaan_notaris',
            'pekerjaan_ppat.nama as pekerjaan_ppat',
            'pm.nama as pemohon_nama',
            'pm.no_telp',
        )
            ->leftJoin('pekerjaan_notaris', 'pekerjaan_notaris.id', '=', 'transaksi.pekerjaan_id')
            ->leftJoin('pekerjaan_ppat', 'pekerjaan_ppat.id', '=', 'transaksi.pekerjaan_id')
            ->leftJoin('pemohon as pm', 'transaksi.pemohon_id', '=', 'pm.id');

        if ($jenis != null) {
            $data->where('transaksi.jenis_pekerjaan_id', $jenis);
        }

        return $data->get();
    }

    public function findNotarisTransaction($id)
    {
        $noAkta = Transaksi::where('id', $id)->value('no_akta');
        $total_dibayar = TransaksiRiwayatPembayaran::query()
            ->where('no_transaksi', $noAkta)
            ->where('status', 1)
            ->pluck('jumlah_dibayar')
            ->toArray();

        $result = Transaksi::query()->select(
            'transaksi.*',
            DB::raw('json_build_array(' . implode(',', $total_dibayar) . ') as total_dibayar'),
            'petugas.nama as petugas_nama',
            'petugas.no_telp as petugas_notelp',
            'jkpet.nama as jenis_kelamin_petugas',
            'pemohon.nik as pemohon_nik',
            'pemohon.alamat as pemohon_alamat',
            'pemohon.nama as pemohon_nama',
            'pemohon.no_telp as pemohon_notelp',
            'jkpem.nama as jenis_kelamin_pemohon',
            'transaksi_detail.isValidate as status_proses'
        )
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('jenis_kelamin as jkpet', 'jkpet.id', '=', 'petugas.jenis_kelamin')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->join('jenis_kelamin as jkpem', 'jkpem.id', '=', 'pemohon.jenis_kelamin')
            ->leftJoin('transaksi_detail_proses as transaksi_detail', function ($db) {
                $db->on('transaksi_detail.transaksi_id', '=', 'transaksi.id')
                    ->where('transaksi_detail.status', 1);
            })
            ->leftJoin('pekerjaan_notaris', 'pekerjaan_notaris.id', '=', 'transaksi.pekerjaan_id')
            ->leftJoin('pekerjaan_ppat', 'pekerjaan_ppat.id', '=', 'transaksi.pekerjaan_id')
            ->where('transaksi.status', 1)
            ->where('transaksi.id', $id)
            ->first();
            
        if (!$result) {
            throw new \Exception("Transaksi dengan ID {$id} tidak ditemukan.");
        }
        $relasi_materai_id = TransaksiMaterai::where('transaksi_id',$id)->pluck('materai_id')->toArray();
        if($relasi_materai_id){
            $count_materai_masuk = Materai::whereIn('id',$relasi_materai_id)->where('materai_keluar',0)->sum('materai_masuk');
            $count_materai_keluar = Materai::whereIn('id',$relasi_materai_id)->sum('materai_keluar');
            $total = $count_materai_keluar - $count_materai_masuk;
            $result->materai_keluar = $total;
        }else{
            $result->materai_keluar = 0;
        }

        $result->total_dibayar = $total_dibayar;

        return $result;
    }

    // => Blok Save Notaris
    public function saveNotarisMaterai($request, $tanggal_daftar, $newStok, $keterangan)
    {
        return Materai::query()
            ->create(
                System::crudIdentity('create', [
                    'date' => $tanggal_daftar,
                    'materai_masuk' => 0,
                    'materai_keluar' => $request->input('jumlah_materai'),
                    'stok_materai' => $newStok,
                    'is_transaksi' => 1,
                    'keterangan' => $keterangan,
                    'petugas_id' => System::strDecode($request->petugas_id),
                ])
            );
    }

    public function saveNotarisTransaction($request, $data)
    {
        return Transaksi::create(System::crudIdentity('create', [
            'no_akta' => $request->input('no_akta'),

            'tanggal_daftar' => $data['tanggal_daftar'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'jatuh_tempo' => $data['jatuh_tempo'],

            'keterangan' => $request->input('keterangan'),

            'pemohon_id' => System::strDecode($request->input('pemohon_id')),
            'jenis_pekerjaan_id' => $request->input('jenis_pekerjaan_id'),
            'pekerjaan_id' => $data['pekerjaan_id'],
            'kategori_pekerjaan_id' => $data['kategori_id'],
            'status_id' => $request->input('status_id'),
            'petugas_id' => System::strDecode($request->input('petugas_id')),
            'jenis_pembayaran_id' => $request->input('jenis_pembayaran_id'),

            'biaya_layanan' => $request->input('biaya_layanan'),
            'biaya_lainnya' => $request->input('biaya_lainya'),
            'potongan_biaya' => $data['potongan'] ?: 0,

            'sub_total' => $data['subtotal'],
            'total' => $data['total'],

            'judul' => $request->input('judul'),
            'nomor_akta' => $request->input('nomor_akta'),
            'tanggal_akta' => $request->input('tanggal_akta'),
        ]));
    }

    public function saveNotarisHistoryTransaction(Request $request, $transaksi)
    {
        $dateSync = date('Y-m-d');
        $nominalBayar = $request->input('pembayaran_sekarang');

        // -- in here change
        $pembayaran_ke = TransaksiRiwayatPembayaran::where('no_transaksi', $request->no_akta)
            ->where('status', 1)
            ->max('pembayaran_ke');

        $pembayaran_ke = $pembayaran_ke ? ($pembayaran_ke + 1) : 1;

        TransaksiRiwayatPembayaran::query()
            ->create(
                System::crudIdentity('create', [
                    'no_transaksi' => $request->no_akta,
                    'tanggal_pembayaran' => $dateSync,
                    'jumlah_dibayar' => $nominalBayar,
                    'pembayaran_ke' => $pembayaran_ke,
                    'petugas_id' => System::strDecode($request->input('petugas_id')),
                    'catatan' => $request->input('catatan_pembayaran'),
                ])
            );

        // -- sync pendapatan
        System::syncLaporanPendapatan($dateSync, [
            'penghasilan' => $nominalBayar,
            'jenis_pembayaran_id' => $request->input('jenis_pembayaran_id'),
        ]);
    }
    // => Blok End Save Notaris

    // => Blok Save PPAT
    public function savePPATMaterai($request, $tanggal_daftar, $newStok, $keterangan)
    {
        return Materai::query()->create(
            System::crudIdentity('create', [
                'date' => $tanggal_daftar,
                'materai_masuk' => 0,
                'materai_keluar' => $request->input('jumlah_materai'),
                'stok_materai' => $newStok,
                'is_transaksi' => 1,
                'keterangan' => $keterangan,
                'petugas_id' => System::strDecode($request->petugas_id),
            ])
        );
    }

    public function savePPATTransaction(Request $request, $data)
    {
        $petugasId = $request->input('petugas_id');
        $pemohonId = $request->input('pemohon_id');

        $petugasId = is_numeric($petugasId) ? $petugasId : System::strDecode($petugasId);
        $pemohonId = is_numeric($pemohonId) ? $pemohonId : System::strDecode($pemohonId);

        return Transaksi::create(
            System::crudIdentity('create', [
                'no_akta' => $request->input('no_akta'),

                'tanggal_daftar' => $data['tanggal_daftar'],
                'tanggal_selesai' => $data['tanggal_selesai'],

                'pemohon_id' => $pemohonId,
                'jenis_pekerjaan_id' => $request->input('jenis_pekerjaan_id'),
                'pekerjaan_id' => $data['pekerjaan_id'],
                'kategori_pekerjaan_id' => $data['kategori_pekerjaan_id'],

                'biaya_layanan' => $data['biaya_layanan'],
                'biaya_lainnya' => $data['biaya_lainya'],

                'jenis_pajak_id' => $data['jenis_pajak'],
                'acuan_hitung_pajak' => $data['acuan_hitung_pajak'],
                'nilai_pengurang' => $data['nilai_pengurang'],
                'besaran_pajak_pihak_pertama' => $data['besaran_pajak_pihak_pertama'],
                'besaran_pajak_pihak_kedua' => $data['besaran_pajak_pihak_kedua'],
                'besaran_tidak_kena_pajak' => $data['besaran_tidak_kena_pajak'],

                'status_id' => $data['status_id'],
                'judul' => $data['judul_ppat'],
                'nomor_akta' => $data['no_akta_ppat'],
                'tanggal_akta' => $data['tgl_akta_ppat'],
                'petugas_id' => $petugasId,
                'potongan_biaya' => $data['potongan_biaya'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'jenis_pembayaran_id' => $request->input('jenis_pembayaran_id'),
                'sub_total' => $data['subtotal'],
                'total' => $data['total'],
                'keterangan' => $data['keterangan']
            ])
        );
    }

    public function savePPATHistoryTransaction(Request $request, $noAkta)
    {
        $dateSync = date('Y-m-d');
        $nominalBayar = $request->input('pembayaran_sekarang');

        // -- in here change
        $pembayaran_ke = TransaksiRiwayatPembayaran::where('no_transaksi', $noAkta)
            ->where('status', 1)
            ->max('pembayaran_ke');

        $pembayaran_ke = $pembayaran_ke ? ($pembayaran_ke + 1) : 1;

        TransaksiRiwayatPembayaran::query()
            ->create(
                System::crudIdentity('create', [
                    'no_transaksi' => $noAkta,
                    'tanggal_pembayaran' => $dateSync,
                    'jumlah_dibayar' => $nominalBayar,
                    'pembayaran_ke' => $pembayaran_ke,
                    'petugas_id' => System::strDecode($request->input('petugas_id')),
                    'catatan' => $request->input('catatan_pembayaran'),
                ])
            );

        // -- sync pendapatan
        System::syncLaporanPendapatan($dateSync, [
            'penghasilan' => $nominalBayar,
            'jenis_pembayaran_id' => $request->input('jenis_pembayaran_id'),
        ]);
    }

    public function updatePPATTransaction(Request $request, $data, $id)
    {
        $petugasId = $request->input('petugas_id');
        $pemohonId = $request->input('pemohon_id');

        $petugasId = is_numeric($petugasId) ? $petugasId : System::strDecode($petugasId);
        $pemohonId = is_numeric($pemohonId) ? $pemohonId : System::strDecode($pemohonId);

        $transaksi = Transaksi::query()
            ->findOrFail($id);

        $transaksi->update(
            System::crudIdentity('update', [
                'no_akta' => $request->input('no_akta'),
                'tanggal_daftar' => $data['tanggal_daftar'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'pemohon_id' => $pemohonId,
                'jenis_pekerjaan_id' => $request->input('jenis_pekerjaan_id'),
                'pekerjaan_id' => $data['pekerjaan_id'],
                'kategori_pekerjaan_id' => $data['kategori_pekerjaan_id'],
                'biaya_layanan' => $data['biaya_layanan'],
                'biaya_lainnya' => $data['biaya_lainya'],
                'jenis_pajak_id' => $data['jenis_pajak'],
                'acuan_hitung_pajak' => $data['acuan_hitung_pajak'],
                'nilai_pengurang' => $data['nilai_pengurang'],
                'besaran_pajak_pihak_pertama' => $data['besaran_pajak_pihak_pertama'],
                'besaran_pajak_pihak_kedua' => $data['besaran_pajak_pihak_kedua'],
                'besaran_tidak_kena_pajak' => $data['besaran_tidak_kena_pajak'],
                'status_id' => $data['status_id'],
                'petugas_id' => $petugasId,
                'potongan_biaya' => $data['potongan_biaya'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'jenis_pembayaran_id' => $request->input('jenis_pembayaran_id'),
                'sub_total' => $data['subtotal'],
                'total' => $data['total'],
                'keterangan' => $data['keterangan'],
                'judul' => $data['judul_ppat'],
                'nomor_akta' => $data['no_akta_ppat'],
                'tanggal_akta' => $data['tgl_akta_ppat'],
            ])
        );

        return $transaksi;
    }
    public function saveRelasiMateraiTransaksi($dataRelasi){
        $MateraiTransaksi = TransaksiMaterai::insert($dataRelasi);
    }
}
