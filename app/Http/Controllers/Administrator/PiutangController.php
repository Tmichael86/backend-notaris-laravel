<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\StrHelper;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisPekerjaan;
use App\Models\KategoriPekerjaan;
use App\Models\PekerjaanNotaris;
use App\Models\PekerjaanPPAT;
use App\Models\Transaksi;
use App\Models\TransaksiRiwayatPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PiutangController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.piutang.index', $data);
    }

    public function fetch(Request $request)
    {
        $tanggal_awal = $request->post('tanggal_awal');
        $tanggal_akhir = $request->post('tanggal_akhir');
        $jenis_pekerjaan_id = $request->post('jenis_pekerjaan_id');

        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = Transaksi::distinct('transaksi.no_akta','transaksi.created_at')
            ->select(
                'transaksi.*',
                'petugas.nama as petugas_nama',
                'petugas.no_telp as petugas_notelp',
                'jkpet.nama as jenis_kelamin_petugas',
                'pemohon.nik as pemohon_nik',
                'pemohon.alamat as pemohon_alamat',
                'pemohon.nama as pemohon_nama',
                'pemohon.no_telp as pemohon_notelp',
                'jkpem.nama as jenis_kelamin_pemohon'
            )
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('jenis_kelamin as jkpet', 'jkpet.id', '=', 'petugas.jenis_kelamin')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->join('jenis_kelamin as jkpem', 'jkpem.id', '=', 'pemohon.jenis_kelamin')
            ->orderBy('transaksi.created_at','DESC')
            ->where('transaksi.status', 1);

        $filteredDB = $DB->where(function ($query) use ($tanggal_awal, $tanggal_akhir, $jenis_pekerjaan_id) {
            if ($tanggal_awal != '') {
                $query->whereDate('jatuh_tempo', '>=', $tanggal_awal);
            }
            if ($tanggal_akhir != '') {
                $query->whereDate('jatuh_tempo', '<=', $tanggal_akhir);
            }
            if ($jenis_pekerjaan_id != 0) {
                $query->where('jenis_pekerjaan_id', $jenis_pekerjaan_id);
            }
        })->get();

        $filteredDB = $filteredDB->filter(function ($transaksi) {
            $jumlahPiutang = System::getTotalTransaksiByCode($transaksi->no_akta);
            $jumlahBayar = TransaksiRiwayatPembayaran::where('no_transaksi', $transaksi->no_akta)
                ->where('status', 1)
                ->pluck('jumlah_dibayar')
                ->toArray();

            $totalBayar = array_sum($jumlahBayar);
            $totalPiutang = $jumlahPiutang - $totalBayar;

            return $totalPiutang > 0;
        });

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
                $jumlahPiutang = System::getTotalTransaksiByCode($transaksi->no_akta);
                return StrHelper::format_rupiah($jumlahPiutang);
            })
            ->addColumn('pekerjaan_nama', function ($val) {
                if ($val->jenis_pekerjaan_id == 1) {
                    $data = PekerjaanNotaris::select('nama')
                        ->where('id', $val->pekerjaan_id)
                        ->value('nama');

                    return $data;
                } else {
                    return self::getPekerjaanPpat($val->no_akta);
                }
            })
            ->addColumn('jenis_pekerjaan_nama', function ($val) {
                $data = JenisPekerjaan::select('nama')
                    ->where('id', $val->jenis_pekerjaan_id)
                    ->value('nama');

                return $data;
            })
            ->addColumn('kategori_nama', function ($val) {
                $data = Transaksi::join('pekerjaan_kategori as pk', 'transaksi.kategori_pekerjaan_id', '=', 'pk.id')
                    ->where('transaksi.no_akta', $val->no_akta)
                    ->pluck('pk.nama')
                    ->toArray();

                return collect($data)->join(', ');
            })
            ->addColumn('total_piutang', function (Transaksi $transaksi) {
                $totalTagihan = System::getTotalTransaksiByCode($transaksi->no_akta);
                $jumlahBayar = TransaksiRiwayatPembayaran::where('no_transaksi', $transaksi->no_akta)
                    ->where('status', 1)
                    ->sum('jumlah_dibayar');

                return StrHelper::format_rupiah($totalTagihan - $jumlahBayar);
            })
            ->toJson();
    }

    public function jenispekerjaan(Request $request)
    {

        $jenis_pekerjaan = $request->input('jenis_pekerjaan');

        $data = JenisPekerjaan::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--pilih--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getTotalPiutang(Request $request)
    {
        $tanggal_awal = $request->post('tanggal_awal');
        $tanggal_akhir = $request->post('tanggal_akhir');
        $jenis_pekerjaan_id = $request->post('jenis_pekerjaan_id');

        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = Transaksi::where('transaksi.status', 1)
            ->where(function ($query) use ($tanggal_awal, $tanggal_akhir, $jenis_pekerjaan_id) {
                if ($tanggal_awal != '') {
                    $query->whereDate('jatuh_tempo', '>=', $tanggal_awal);
                }
                if ($tanggal_akhir != '') {
                    $query->whereDate('jatuh_tempo', '<=', $tanggal_akhir);
                }
                if ($jenis_pekerjaan_id != 0) {
                    $query->where('jenis_pekerjaan_id', $jenis_pekerjaan_id);
                }
            })
            ->select([
                'no_akta',
                DB::raw('SUM(total) as total'),
                DB::raw('SUM(besaran_pajak_pihak_pertama + besaran_pajak_pihak_kedua) as pajak')
            ])
            ->groupBy('no_akta')
            ->get();

        $totalPiutang = $DB->map(function ($transaksi) {
            $jumlahBayar = TransaksiRiwayatPembayaran::where('no_transaksi', $transaksi->no_akta)
                ->where('status', 1)
                ->pluck('jumlah_dibayar')
                ->sum();

            return max(0, ($transaksi->total + $transaksi->pajak) - $jumlahBayar);
        })->sum();

        return response()->json([
            'totalPiutang' => StrHelper::format_rupiah($totalPiutang),
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
}
