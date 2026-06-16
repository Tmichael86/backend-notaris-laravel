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
use App\Models\Petugas;
use App\Models\Transaksi;
use App\Models\TransaksiRiwayatPembayaran;
use App\Models\TransaksiStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenghasilanController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.penghasilan.index', $data);
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

        $DB = Transaksi::distinct('transaksi_riwayat_pembayaran.created_at')
            ->select([
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
                'transaksi_riwayat_pembayaran.jumlah_dibayar as jumlah_dibayar',
                'transaksi_riwayat_pembayaran.created_at as tanggal_pembayaran',
            ])
            ->join('transaksi_riwayat_pembayaran', 'transaksi_riwayat_pembayaran.no_transaksi', '=', 'transaksi.no_akta')
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('jenis_kelamin as jkpet', 'jkpet.id', '=', 'petugas.jenis_kelamin')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->join('jenis_kelamin as jkpem', 'jkpem.id', '=', 'pemohon.jenis_kelamin')
            ->join('transaksi_status', 'transaksi_status.id', '=', 'transaksi.status_id')
            ->orderBy('transaksi_riwayat_pembayaran.created_at', 'DESC')
            ->where('transaksi.status', 1);

        $filteredDB = $DB->where(function ($query) use ($tanggal_awal, $tanggal_akhir, $jenis_pekerjaan_id, $pekerjaan_id, $kategori_id, $status_id, $petugas_id) {
            if ($tanggal_awal != '') {
                $query->whereDate('transaksi_riwayat_pembayaran.created_at', '>=', $tanggal_awal);
            }
            if ($tanggal_akhir != '') {
                $query->whereDate('transaksi_riwayat_pembayaran.created_at', '<=', $tanggal_akhir);
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
            ->editColumn('jumlah_dibayar', function (Transaksi $transaksi) {
                return StrHelper::format_rupiah($transaksi->jumlah_dibayar);
            })
            ->editColumn('total', function (Transaksi $transaksi) {
                // -- in here total ppat
                $nominal = self::getTotalBiaya($transaksi->no_akta);
                return StrHelper::format_rupiah($nominal);
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
                $jumlahDibayar = self::getTotalRiwayatBayar($transaksi->no_akta, $transaksi->tanggal_pembayaran);
                $totalTagihan = self::getTotalBiaya($transaksi->no_akta);
                $sisaTagihan = $totalTagihan - $jumlahDibayar;

                return StrHelper::format_rupiah($sisaTagihan < 0 ? 0 : $sisaTagihan);
            })
            ->toJson();
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

    private static function getTotalRiwayatBayar($code, $tanggalBayar)
    {
        $total = TransaksiRiwayatPembayaran::where('no_transaksi', $code)
            ->where('created_at', '<=', $tanggalBayar)
            ->where('status', 1)
            ->pluck('jumlah_dibayar')
            ->toArray();

        return array_sum($total);
    }
}
