<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\StrHelper;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\HargaPekerjaanNotaris;
use App\Models\HargaPekerjaanPPAT;
use App\Models\JenisPekerjaan;
use App\Models\KategoriPekerjaan;
use App\Models\PekerjaanNotaris;
use App\Models\PekerjaanPPAT;
use App\Models\Petugas;
use App\Models\ProsesPekerjaanNotaris;
use App\Models\ProsesPekerjaanPPAT;
use App\Models\Transaksi;
use App\Models\TransaksiDetailCatatan;
use App\Models\TransaksiDetailProses;
use App\Models\TransaksiRiwayatPembayaran;
use App\Models\TransaksiStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MonitoringController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.monitoring.index', $data);
    }

    public function fetch(Request $request)
    {
        setAccessibilityPermission('read');

        $tanggal_awal = $request->post('tanggal_awal');
        $tanggal_akhir = $request->post('tanggal_akhir');
        $jenis_pekerjaan_id = $request->post('jenis_pekerjaan_id');
        $pekerjaan_id = $request->post('pekerjaan_id');
        $kategori_id = $request->post('kategori_id');
        $status_id = $request->post('status_id');
        $petugas_id = $request->post('petugas_id');

        $DB = Transaksi::distinct('transaksi.no_akta')
            ->select(
                'transaksi.*',
                'petugas.nama as petugas_nama',
                'petugas.no_telp as petugas_notelp',
                'jkpet.nama as jenis_kelamin_petugas',
                'pemohon.nik as pemohon_nik',
                'pemohon.alamat as pemohon_alamat',
                'pemohon.nama as pemohon_nama',
                'pemohon.no_telp as pemohon_notelp',
                'jkpem.nama as jenis_kelamin_pemohon',
                'transaksi_status.nama as status_nama',
            )
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('jenis_kelamin as jkpet', 'jkpet.id', '=', 'petugas.jenis_kelamin')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->join('jenis_kelamin as jkpem', 'jkpem.id', '=', 'pemohon.jenis_kelamin')
            ->join('transaksi_status', 'transaksi_status.id', '=', 'transaksi.status_id')
            ->where('transaksi.status', 1);


        $filteredDB = $DB->where(function ($query) use ($tanggal_awal, $tanggal_akhir, $jenis_pekerjaan_id, $pekerjaan_id, $kategori_id, $status_id, $petugas_id) {
            if ($tanggal_awal != '') {
                $query->whereDate('tanggal_daftar', '>=', $tanggal_awal);
            }
            if ($tanggal_akhir != '') {
                $query->whereDate('tanggal_daftar', '<=', $tanggal_akhir);
            }
            if ($jenis_pekerjaan_id != 0) {
                $query->where('jenis_pekerjaan_id', $jenis_pekerjaan_id);
            }
            if ($pekerjaan_id != 0) {
                $query->where('pekerjaan_id', $pekerjaan_id);
            }
            if ($kategori_id != 0) {
                $query->where('kategori_pekerjaan_id', $kategori_id);
            }
            if ($status_id != 0) {
                $query->where('status_id', $status_id);
            }
            if ($petugas_id != 0) {
                $query->where('petugas_id', $petugas_id);
            }
        })->get();

        return DataTables::of($filteredDB)
            ->editColumn('id', function (Transaksi $transaksi) {
                return System::strEncode($transaksi->id);
            })
            ->editColumn('petugas_id', function (Transaksi $transaksi) {
                return System::strEncode($transaksi->petugas_id);
            })
            ->editColumn('pemohon_id', function (Transaksi $transaksi) {
                return System::strEncode($transaksi->pemohon_id);
            })
            ->editColumn('total', function (Transaksi $transaksi) {
                // -- in here total ppat
                $nominal = self::getTotalBiaya($transaksi->no_akta);
                return StrHelper::format_rupiah($nominal);

                // return StrHelper::format_rupiah($transaksi->total);
            })
            ->addColumn('pekerjaan_nama', function ($val) {
                if ($val->jenis_pekerjaan_id == 1) {
                    $data = PekerjaanNotaris::select('nama')
                        ->where('id', $val->pekerjaan_id)
                        ->value('nama');

                    return $data;
                } else {
                    // -- in here pekerjaan
                    return self::getPekerjaanPpat($val->no_akta);

                    // $data = PekerjaanPPAT::select('nama')
                    //     ->where('id', $val->pekerjaan_id)
                    //     ->value('nama');

                    // return $data;
                }
            })
            ->addColumn('estimasi_waktu', function ($val) {
                if ($val->jenis_pekerjaan_id == 1) {
                    $data = HargaPekerjaanNotaris::select('estimasi_waktu')
                        ->where('kategori_pekerjaan_id', $val->kategori_pekerjaan_id)
                        ->value('estimasi_waktu');

                    return $data;
                } else {
                    $data = HargaPekerjaanPPAT::select('estimasi_waktu')
                        ->where('kategori_pekerjaan_id', $val->kategori_pekerjaan_id)
                        ->value('estimasi_waktu');

                    return $data;
                }
            })
            ->addColumn('proses_nama', function ($val) {
                if ($val->jenis_pekerjaan_id == 1) {
                    $data = ProsesPekerjaanNotaris::select('nama')
                        ->join('transaksi_detail_proses as tdp', 'tdp.prosesId', '=', second: 'pekerjaan_notaris_proses.id')
                        ->where('transaksi_id', $val->id)
                        ->where('tdp.status', 1)
                        ->orderBy('tdp.prosesId', 'DESC')
                        ->value('nama');
                    return $data ?? '-';
                } else {
                    return self::getNamaProsesPekerjaanPPAT($val->no_akta);
                }
            })
            ->addColumn('jenis_pekerjaan_nama', function ($val) {
                $data = JenisPekerjaan::select('nama')
                    ->where('id', $val->jenis_pekerjaan_id)
                    ->value('nama');

                return $data;
            })
            ->addColumn('kategori_nama', function ($val) {
                $data = kategoriPekerjaan::select('nama')
                    ->where('id', $val->kategori_pekerjaan_id)
                    ->value('nama');

                return $data;
            })
            ->addColumn('total_piutang', function (Transaksi $transaksi) {
                // -- in here sisa tagihan
                $jumlahDibayar = self::getTotalRiwayatBayar($transaksi->no_akta);
                $totalTagihan = self::getTotalBiaya($transaksi->no_akta);
                $sisaTagihan = $totalTagihan - $jumlahDibayar;

                return StrHelper::format_rupiah($sisaTagihan < 0 ? 0 : $sisaTagihan);
            })
            ->addColumn('bg', function (Transaksi $transaksi) {
                return ($transaksi->tanggal_selesai < date('Y-m-d H:i:s') && $transaksi->status_id != 3) ? '1' : '0';
            })
            ->toJson();
    }

    // public function getTableProsesNotaris(Request $request)
    // {
    //     $transaksi_id = System::strDecode($request->input('transaksi_id'));
    //     setAccessibilityPermission('read');

    //     $DB = TransaksiDetailProses::query()
    //         ->select([
    //             'transaksi_detail_proses.prosesId as proses_id',
    //             'transaksi_detail_proses.prosesNama as proses_nama',
    //             'transaksi_detail_proses.isValidate',
    //             'transaksi_detail_proses.created_at',
    //             'p.nama as petugas_nama'
    //         ])
    //         ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail_proses.transaksi_id')
    //         ->join('petugas as p', 'p.id', '=', 'transaksi.petugas_id')
    //         ->where([
    //             ['transaksi_detail_proses.transaksi_id', $transaksi_id],
    //             ['transaksi_detail_proses.jenis_pekerjaan_id', 1],
    //             ['transaksi_detail_proses.status', 1]
    //         ])
    //         ->get();

    //     return DataTables::of($DB)->toJson();
    // }

    public function getTableProsesNotaris(Request $request)
    {
        $transaksi_id = System::strDecode($request->input('transaksi_id'));
        $jenis_pekerjaan_id = 1; // ID jenis pekerjaan untuk notaris
        setAccessibilityPermission('read');

        // Ambil pekerjaanNama dari transaksi_detail_proses berdasarkan transaksi_id
        $pekerjaanNama = DB::table('transaksi_detail_proses')
            ->where('transaksi_id', $transaksi_id)
            ->value('pekerjaanNama');

        if (!$pekerjaanNama) {
            return response()->json(['data' => []]);
        }

        // Cari pekerjaan_notaris_id dari tabel pekerjaan_notaris
        $pekerjaanNotarisId = DB::table('pekerjaan_notaris')
            ->where('nama', $pekerjaanNama)
            ->value('id');

        // Ambil semua proses dari pekerjaan_notaris_proses
        $allProses = DB::table('pekerjaan_notaris_proses')
            ->select([
                'pekerjaan_notaris_proses.id as proses_id',
                'pekerjaan_notaris_proses.nama as proses_nama',
                DB::raw('null as isValidate'),
                DB::raw('null as created_at'),
                DB::raw('null as petugas_nama'),
            ])
            ->when($pekerjaanNotarisId, function ($query) use ($pekerjaanNotarisId) {
                return $query->where('pekerjaan_notaris_proses.pekerjaan_notaris_id', $pekerjaanNotarisId);
            })
            ->where('pekerjaan_notaris_proses.status', 1);

        // Ambil data dari transaksi_detail_proses
        $detailProses = DB::table('transaksi_detail_proses')
            ->select([
                'transaksi_detail_proses.prosesId as proses_id',
                'transaksi_detail_proses.prosesNama as proses_nama',
                'transaksi_detail_proses.isValidate',
                'transaksi_detail_proses.created_at',
                'p.nama as petugas_nama',
            ])
            ->leftJoin('petugas as p', 'p.id', '=', 'transaksi_detail_proses.created_by')
            ->where('transaksi_detail_proses.transaksi_id', $transaksi_id)
            ->where('transaksi_detail_proses.status', 1);

        // Gabungkan hasil kedua query menggunakan UNION ALL
        $query = $allProses->unionAll($detailProses);

        // Hapus duplikasi berdasarkan proses_id
        $data = DB::table(DB::raw("({$query->toSql()}) as subquery"))
            ->mergeBindings($query)
            ->select([
                'proses_id',
                'proses_nama',
                DB::raw('MAX(isValidate) as isValidate'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(petugas_nama) as petugas_nama'),
            ])
            ->groupBy('proses_id', 'proses_nama');

        // Eksekusi query dan gunakan DataTables untuk respons
        $datatable = DataTables::of($data)->toJson();

        return $datatable;
    }

    // public function getTableProsesPPAT(Request $request)
    // {

    //     setAccessibilityPermission('read');

    //     $transaksi_id = $request->input("transaksi_id");

    //     $DB = TransaksiDetailProses::query()
    //         ->where('transaksi_id', '=', $transaksi_id)
    //         ->select([
    //             'transaksi_detail_proses.prosesId as proses_id',
    //             'transaksi_detail_proses.prosesNama as proses_nama',
    //             'transaksi_detail_proses.isValidate',
    //             'transaksi_detail_proses.created_at',
    //             'p.nama as petugas_nama',
    //             'ppp.nama as pekerjaan_nama',
    //             'transaksi_detail_proses.id'
    //         ])
    //         ->join('petugas as p', 'p.user_id', '=', 'transaksi_detail_proses.created_by')
    //         ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail_proses.transaksi_id')
    //         ->leftJoin('pekerjaan_ppat_proses as ppp', 'ppp.id', '=', 'transaksi.pekerjaan_id')
    //         ->where('transaksi_detail_proses.status', 1)
    //         ->get();

    //     // die;
    //     return DataTables::of($DB)->toJson();
    // }

    public function getTableProsesPPAT(Request $request)
    {
        setAccessibilityPermission('read');

        $transaksi_id = $request->input("transaksi_id");

        // Ambil pekerjaanNama dari transaksi_detail_proses berdasarkan transaksi_id
        $pekerjaanNama = DB::table('transaksi_detail_proses')
            ->where('transaksi_id', $transaksi_id)
            ->value('pekerjaanNama');

        if (!$pekerjaanNama) {
            return response()->json(['data' => []]);
        }

        // Cari pekerjaan_ppat_id dari tabel pekerjaan_ppat
        $pekerjaanPpatId = DB::table('pekerjaan_ppat')
            ->where('nama', $pekerjaanNama)
            ->value('id');

        // Ambil semua proses dari pekerjaan_ppat_proses
        $allProses = DB::table('pekerjaan_ppat_proses')
            ->select([
                'pekerjaan_ppat_proses.id as proses_id',
                'pekerjaan_ppat_proses.nama as proses_nama',
                DB::raw('null as isValidate'),
                DB::raw('null as created_at'),
                DB::raw('null as petugas_nama'),
            ])
            ->when($pekerjaanPpatId, function ($query) use ($pekerjaanPpatId) {
                return $query->where('pekerjaan_ppat_proses.pekerjaan_ppat_id', $pekerjaanPpatId);
            })
            ->where('pekerjaan_ppat_proses.status', 1);

        // Ambil data dari transaksi_detail_proses
        $detailProses = DB::table('transaksi_detail_proses')
            ->select([
                'transaksi_detail_proses.prosesId as proses_id',
                'transaksi_detail_proses.prosesNama as proses_nama',
                'transaksi_detail_proses.isValidate',
                'transaksi_detail_proses.created_at',
                'p.nama as petugas_nama',
            ])
            ->leftJoin('petugas as p', 'p.id', '=', 'transaksi_detail_proses.created_by')
            ->where('transaksi_detail_proses.transaksi_id', $transaksi_id)
            ->where('transaksi_detail_proses.status', 1);

        // Gabungkan hasil kedua query menggunakan UNION ALL
        $query = $allProses->unionAll($detailProses);

        // Hapus duplikasi berdasarkan proses_id
        $data = DB::table(DB::raw("({$query->toSql()}) as subquery"))
            ->mergeBindings($query)
            ->select([
                'proses_id',
                'proses_nama',
                DB::raw('MAX(isValidate) as isValidate'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(petugas_nama) as petugas_nama'),
            ])
            ->groupBy('proses_id', 'proses_nama');

        // Eksekusi query dan gunakan DataTables untuk respons
        $datatable = DataTables::of($data)->toJson();

        return $datatable;
    }

    public function getTableListPPAT(Request $request)
    {
        setAccessibilityPermission('read');
        $DB = Transaksi::query()
            ->select([
                'transaksi.*',
                'pp.nama as pekerjaan_nama',
                'pk.nama as kategori',
                'pnh.estimasi_waktu'
            ])
            ->where('no_akta', $request->no_transaksi)
            ->join('pekerjaan_ppat as pp', 'pp.id', '=', 'transaksi.pekerjaan_id')
            ->join('pekerjaan_kategori as pk', 'pk.id', '=', 'transaksi.kategori_pekerjaan_id')
            ->leftJoin('pekerjaan_ppat_harga as pnh', function ($join) {
                $join->on('pnh.kategori_pekerjaan_id', '=', 'transaksi.kategori_pekerjaan_id')
                    ->on('pnh.pekerjaan_ppat_id', '=', 'transaksi.pekerjaan_id');
            })
            ->get();

        return DataTables::of($DB)->toJson();
    }

    public function getDetailCatatan(Request $request)
    {
        $transaksi_id = System::strDecode($request->input('transaksi_id'));
        $proses_id = $request->input('proses_id');

        $transaksi = Transaksi::where('id', $transaksi_id)
            ->where('status', 1)
            ->first();

        $prosesDetail = TransaksiDetailProses::where('transaksi_id', $transaksi_id)
            ->where('prosesId', $proses_id)
            ->where('status', 1)
            ->first();

        if (!$prosesDetail) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Process detail not found'
            ]);
        }

        $atributIds = json_decode($prosesDetail->atribut, true);

        $atributDetails = [];
        if (!empty($atributIds)) {
            $atributs = DB::table('pekerjaan_notaris_atributs')
                ->whereIn('id', array_keys($atributIds))
                ->where('status', 1)
                ->get();

            foreach ($atributs as $atribut) {
                $atributDetails[] = [
                    'id' => $atribut->id,
                    'nama' => $atribut->atribut,
                    'nilai' => $atribut->nilai ?? '-',
                    'status' => $atributIds[$atribut->id] ?? 0
                ];
            }
        }

        $data = [
            'waktu_pengerjaan' => Carbon::parse($prosesDetail->waktu_pengerjaan)->translatedFormat('l j F Y H:i'),
            'proses' => [
                'id' => $prosesDetail->prosesId,
                'nama' => $prosesDetail->prosesNama,
                'jenis_pekerjaan_id' => $prosesDetail->jenis_pekerjaan_id,
                'atribut' => $atributDetails,
                'pekerjaanNama' => $prosesDetail->pekerjaanNama,
                'kategoriNama' => $prosesDetail->kategoriNama,
                'catatan' => $prosesDetail->catatan
            ]
        ];

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function getDetailCatatanPPAT(Request $request)
    {
        $transaksi_id = ($request->input('transaksi_id'));
        $proses_id = $request->input('proses_id');

        $transaksi = Transaksi::where('id', $transaksi_id)
            ->where('status', 1)
            ->first();

        $prosesDetail = TransaksiDetailProses::where('transaksi_id', $transaksi_id)
            ->where('prosesId', $proses_id)
            ->where('status', 1)
            ->first();

        if (!$prosesDetail) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Process detail not found'
            ]);
        }

        $atributIds = json_decode($prosesDetail->atribut, true);

        $atributDetails = [];
        if (!empty($atributIds)) {
            $atributs = DB::table('pekerjaan_notaris_atributs')
                ->whereIn('id', array_keys($atributIds))
                ->where('status', 1)
                ->get();

            foreach ($atributs as $atribut) {
                $atributDetails[] = [
                    'id' => $atribut->id,
                    'nama' => $atribut->atribut,
                    'nilai' => $atribut->nilai ?? '-',
                    'status' => $atributIds[$atribut->id] ?? 0
                ];
            }
        }

        $data = [
            'waktu_pengerjaan' => Carbon::parse($prosesDetail->waktu_pengerjaan)->translatedFormat('l j F Y H:i'),
            'proses' => [
                'id' => $prosesDetail->prosesId,
                'nama' => $prosesDetail->prosesNama,
                'jenis_pekerjaan_id' => $prosesDetail->jenis_pekerjaan_id,
                'atribut' => $atributDetails,
                'pekerjaanNama' => $prosesDetail->pekerjaanNama,
                'kategoriNama' => $prosesDetail->kategoriNama,
                'catatan' => $prosesDetail->catatan
            ]
        ];

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function getJenisPekerjaan(Request $request)
    {
        $data = JenisPekerjaan::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Jenis Pekerjaan--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getPekerjaan(Request $request)
    {
        $jenis_pekerjaan = $request->input('jenis_pekerjaan');
        if ($jenis_pekerjaan == 1) {
            $data = PekerjaanNotaris::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Pekerjaan--'];

            $data = collect($data)->sortBy('id')->values()->all();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } else {
            $data = PekerjaanPPAT::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Pekerjaan--'];

            $data = collect($data)->sortBy('id')->values()->all();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        }
    }
    public function getKategoriPekerjaan(Request $request)
    {
        $data = kategoriPekerjaan::select(['id', 'nama as text'])
            ->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Kategori Pekerjaan--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getStatus(Request $request)
    {
        $data = TransaksiStatus::select(['id', 'nama as text'])
            ->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Status--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getPetugas(Request $request)
    {
        $data = Petugas::select(['id', 'nama as text'])
            ->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Petugas--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
    public function getPajak($code)
    {
        $data = Transaksi::query()
            ->join('jenis_pajak', 'jenis_pajak.id', '=', 'transaksi.jenis_pajak_id')
            ->where('transaksi.id', $code)
            ->where('transaksi.status', '=', 1)
            ->first();

        return $this->responseServer(200, [
            "statusCode" => 200,
            "data" => $data
        ]);
    }
    private static function getPekerjaanPpat($code)
    {
        $data = Transaksi::join('pekerjaan_ppat as pk', 'transaksi.pekerjaan_id', '=', 'pk.id')
            ->where('no_akta', $code)
            ->select([
                'pk.nama as pekerjaan'
            ])
            ->get()
            ->toArray();

        return collect($data)->map(function ($val) {
            return $val['pekerjaan'];
        })->join(', ');
    }

    private static function getTotalBiaya($code)
    {
        $total = Transaksi::where('no_akta', $code)
            ->select([
                DB::raw('SUM(total) as total'),
                DB::raw("SUM(besaran_pajak_pihak_pertama) as pajak_1"),
                DB::raw("SUM(besaran_pajak_pihak_kedua) as pajak_2"),
            ])
            ->groupBy('no_akta')
            ->first();

        return $total->total + ($total->pajak_1 + $total->pajak_2);
    }

    private static function getTotalRiwayatBayar($code)
    {
        $total = TransaksiRiwayatPembayaran::where('no_transaksi', $code)
            ->where('status', 1)
            ->pluck('jumlah_dibayar')
            ->toArray();

        return array_sum($total);
    }

    private static function getNamaProsesPekerjaanPPAT($code)
    {
        $name = Transaksi::where('no_akta', $code)
            ->leftJoin('pekerjaan_ppat_proses as ppp', 'ppp.id', '=', 'transaksi.pekerjaan_id')
            ->where('transaksi.status', 1)
            ->pluck('ppp.nama')
            ->first();

        return $name;
    }
}
