<?php

namespace App\Http\Controllers\Administrator\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Libraries\StrHelper;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisPembayaran;
use App\Models\Pendapatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanPendapatanController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.laporan.pendapatan.index', $data);
    }

    // public function fetch(Request $request)
    // {
    //     $tglAwal = $request->input('tanggal_awal', now()->startOfMonth()->toDateString());
    //     $tglAkhir = $request->input('tanggal_akhir', now()->endOfMonth()->toDateString());

    //     $pendapatan = Pendapatan::whereBetween('tanggal', [$tglAwal, $tglAkhir])
    //         ->orderBy('tanggal', 'DESC');

    //     // -- total
    //     $totalPenghasilan = $pendapatan->sum('penghasilan');
    //     $totalPengeluaran = $pendapatan->sum('pengeluaran');
    //     $totalPendapatan = $pendapatan->sum('pendapatan');

    //     return DataTables::of($pendapatan)
    //         ->addIndexColumn()
    //         ->editColumn('tanggal', function ($item) {
    //             return Carbon::parse($item->tanggal)
    //                 ->format('d/m/Y');
    //         })
    //         ->with([
    //             'total_penghasilan' => StrHelper::format_rupiah($totalPenghasilan),
    //             'total_pengeluaran' => StrHelper::format_rupiah($totalPengeluaran),
    //             'total_pendapatan' => StrHelper::format_rupiah($totalPendapatan),
    //         ])
    //         ->make(true);
    // }

    public function fetch(Request $request)
    {
        $tglAwal = $request->input('tanggal_awal', now()->startOfMonth()->toDateString());
        $tglAkhir = $request->input('tanggal_akhir', now()->endOfMonth()->toDateString());
        $jenisPembayaranId = $request->input('jenis_pembayaran');

        // Ambil data berdasarkan rentang tanggal
        $pendapatan = Pendapatan::with('JenisPembayaran')
            ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
            ->orderBy('tanggal', 'DESC');

        // Tambahkan filter jenis pembayaran jika dipilih
        if (!empty($jenisPembayaranId)) {
            if ($jenisPembayaranId == 2) {
                $pendapatan->whereIn('jenis_pembayaran_id', [3, 4, 5, 6]);
            } else {
                $pendapatan->where('jenis_pembayaran_id', $jenisPembayaranId);
            }
        }

        // Mengelompokkan data berdasarkan tanggal
        $pendapatanData = $pendapatan->get()->groupBy(function ($item) {
            return Carbon::parse($item->tanggal)->format('d/m/Y'); // Kelompokkan berdasarkan tanggal (d/m/Y)
        });

        // -- total keseluruhan atau berdasarkan filter
        $totalPenghasilan = $pendapatan->sum('penghasilan');
        $totalPengeluaran = $pendapatan->sum('pengeluaran');
        $totalPendapatan = $pendapatan->sum('pendapatan');

        // Siapkan data untuk DataTables
        $data = [];
        foreach ($pendapatanData as $tanggal => $items) {
            $totalTanggalPenghasilan = $items->sum('penghasilan');
            $totalTanggalPengeluaran = $items->sum('pengeluaran');
            $totalTanggalPendapatan = $items->sum('pendapatan');
            $totalTanggalSaldo = $items->sum('saldo');

            $data[] = [
                'tanggal' => $tanggal,
                'items' => $items,
                'total_penghasilan' => ($totalTanggalPenghasilan),
                'total_pengeluaran' => ($totalTanggalPengeluaran),
                'total_pendapatan' => ($totalTanggalPendapatan),
                'total_saldo' => ($totalTanggalSaldo),
            ];
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($item) {
                return $item['tanggal']; // Tanggal sudah diformat sebelumnya
            })
            ->editColumn('jenis_pembayaran', function ($item) {
                return $item['items']->pluck('JenisPembayaran.nama')->unique()->implode(', ') ?? '-';
            })
            ->with([
                'total_penghasilan' => StrHelper::format_rupiah($totalPenghasilan),
                'total_pengeluaran' => StrHelper::format_rupiah($totalPengeluaran),
                'total_pendapatan' => StrHelper::format_rupiah($totalPendapatan),
            ])
            ->make(true);
    }

    public function jenisPembayaran()
    {
        $jenisPembayaran = JenisPembayaran::all(['id', 'nama']); // Sesuaikan kolom
        return response()->json(['data' => $jenisPembayaran]);
    }
}
