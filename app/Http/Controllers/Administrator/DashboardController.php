<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $month = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nov",
        "Des"
    ];

    public function index()
    {
        $this->unlockData();
        $tahunData = Transaksi::distinct('tanggal_daftar')
            ->where('status', 1)
            ->select([
                DB::raw("DATE_PART('year', tanggal_daftar) as tanggal_daftar")
            ])
            ->pluck('tanggal_daftar')
            ->toArray();

        $tahunSekarang = date('Y');
        if (!in_array($tahunSekarang, $tahunData)) {
            $tahunData[] = $tahunSekarang;
        }

        $data['tahun'] = $tahunData;
        return view('administrator.dashboard.index', $data);
    }

    public function dataChart(Request $request)
    {
        $tahun = $request->input('tahun');
        $tahun = $tahun ?: date('Y');

        // -- get data card
        $pemohonCount = Pemohon::where('status', 1)
            ->count();

        $transaksi = self::gettingTransaksi($tahun)
            ->where('status', 1)
            ->get();

        $transaksiTotal = $transaksi->count();
        $transaksiPending = $transaksi->filter(function ($item) {
            //-- notaris
            if ($item->jenis_perjaan_id == 1) {
                return $item->status_id != 3;
            }

            // -- ppat
            $exist = Transaksi::where('no_akta', $item->no_akta)
                ->where('status_id', '!=', 3)
                ->where('status', 1)
                ->exists();

            return $exist;
        })->count();

        $transaksiSelesai = $transaksi->filter(function ($item) {
            //-- notaris
            if ($item->jenis_perjaan_id == 1) {
                return $item->status_id == 3;
            }

            // -- ppat
            $exist = Transaksi::where('no_akta', $item->no_akta)
                ->where('status_id', '!=', 3)
                ->where('status', 1)
                ->exists();

            return !$exist;
        })->count();

        $chartBar = $this->chartBar($tahun);
        $chartLine1 = $this->chartLine1($tahun);
        $chartLine2 = $this->chartLine2($tahun);

        return $this->responseServer(200, [
            'card_data' => [
                'pemohon' => $pemohonCount,
                'transaksi_total' => $transaksiTotal,
                'transaksi_pending' => $transaksiPending,
                'transaksi_selesai' => $transaksiSelesai,
            ],
            'chart_pie' => [
                [
                    'name' => 'Transaksi',
                    'value' => $transaksiTotal
                ],
                [
                    'name' => 'Pemohon',
                    'value' => $pemohonCount
                ]
            ],
            'chart_bar' => $chartBar,
            'chart_line_1' => $chartLine1,
            'chart_line_2' => $chartLine2,
        ]);
    }

    private function chartBar($tahun)
    {
        // -- data chart transaksi
        $transaksiPendingArr = [];
        $transaksiSelesaiArr = [];

        foreach ($this->month as $key => $val) {
            $month = $key + 1;
            $month = $month >= 10 ? $month : "0$month";

            $transaksi = self::gettingTransaksi($tahun)
                ->where('tanggal_daftar', 'LIKE', "%$tahun-$month%")
                ->where('status', 1)
                ->get();

            $transaksiPending = $transaksi->filter(function ($item) {
                //-- notaris
                if ($item->jenis_perjaan_id == 1) {
                    return $item->status_id != 3;
                }

                // -- ppat
                $exist = Transaksi::where('no_akta', $item->no_akta)
                    ->where('status_id', '!=', 3)
                    ->where('status', 1)
                    ->exists();

                return $exist;
            })->count();

            $transaksiSelesai = $transaksi->filter(function ($item) {
                //-- notaris
                if ($item->jenis_perjaan_id == 1) {
                    return $item->status_id == 3;
                }

                // -- ppat
                $exist = Transaksi::where('no_akta', $item->no_akta)
                    ->where('status_id', '!=', 3)
                    ->where('status', 1)
                    ->exists();

                return !$exist;
            })->count();

            $transaksiPendingArr[] = $transaksiPending;
            $transaksiSelesaiArr[] = $transaksiSelesai;
        }

        return  [
            'label' => $this->month,
            'data' => [
                'pending' => $transaksiPendingArr,
                'selesai' => $transaksiSelesaiArr,
            ],
        ];
    }

    private function chartLine1($tahun)
    {
        // -- data line transaksi
        $transaksi = [];
        foreach ($this->month as $key => $val) {
            $month = $key + 1;
            $month = $month >= 10 ? $month : "0$month";

            $transaksi[] = self::gettingTransaksi($tahun)
                ->where('tanggal_daftar', 'LIKE', "%$tahun-$month%")
                ->where('status', 1)
                ->count();
        }

        return [
            'value' => $transaksi,
        ];
    }

    private function chartLine2($tahun)
    {
        // -- data line pemohon
        $pemohon = [];
        foreach ($this->month as $key => $val) {
            $month = $key + 1;
            $month = $month >= 10 ? $month : "0$month";

            $pemohon[] = Pemohon::where('created_at', 'LIKE', "%$tahun-$month%")
                ->where('status', 1)
                ->count();
        }

        return [
            'value' => $pemohon,
        ];
    }

    static private function gettingTransaksi($tahun)
    {
        return Transaksi::query()
            ->distinct('transaksi.no_akta')
            ->where(DB::raw("DATE_PART('year', tanggal_daftar)"), $tahun);
    }
}
