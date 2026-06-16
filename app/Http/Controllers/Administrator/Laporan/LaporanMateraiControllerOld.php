<?php

namespace App\Http\Controllers\Administrator\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\Materai;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LaporanMateraiController extends Controller
{
    public function index()
    {
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.laporan.materai.index', $data);
    }

    public function fetch(Request $request)
    {
        setAccessibilityPermission('read');

        $query = self::getMaterai();

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('materais.date', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->filter_petugas_id) {
            $query->where('petugas_id', $request->filter_petugas_id);
        }

        $query = $query->get();
        return DataTables::of($query)
            ->editColumn('petugas_name', function ($materai) {
                return $materai->petugas_name;
            })
            ->toJson();
    }

    public function getFilterPetugas()
    {
        $data = Petugas::query()->select(['id', 'nama as text'])
            ->where('status', 1)->get()->toArray();

        $data[] = ['id' => 0, 'text' => '--Pilih Petugas--'];

        $data = collect($data)->sortBy('id')->values()->all();

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function getPetugas()
    {
        $user_id = Auth::user()->id;

        $data = Petugas::query()->where('user_id', $user_id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        setAccessibilityPermission('create');

        $validated = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'materai_masuk' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'petugas_id' => 'required|exists:petugas,id',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }
        $lastID = Materai::max('id');
        $lastMateraiBefore = Materai::where('id', $lastID)->orderBy('date', 'desc')->first();
        $previousStok = $lastMateraiBefore ? $lastMateraiBefore->stok_materai : 0;

        $newStok = $previousStok + $request->materai_masuk;

        $data = System::crudIdentity('create', [
            'date' => $request->date,
            'materai_masuk' => $request->materai_masuk,
            'materai_keluar' => 0,
            'is_add_materai' =>1,
            'is_transaksi' => 0,
            'stok_materai' => $newStok,
            'keterangan' => $request->keterangan,
            'petugas_id' => $request->petugas_id,
        ]);

        $i = Materai::query()->create($data);

        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ]);
        }

        $materaiSetelah = Materai::where('date', '>', $request->date)->orderBy('date', 'asc')->get();

        $stokMateraiSebelumnya = $newStok;

        foreach ($materaiSetelah as $materai) {
            $stokMateraiBaru = $stokMateraiSebelumnya + $materai->materai_masuk - $materai->materai_keluar;
            $materai->update(['stok_materai' => $stokMateraiBaru]);
            $stokMateraiSebelumnya = $stokMateraiBaru;
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data berhasil disimpan',
        ]);
    }

    private static function getMaterai()
    {
        $queryWithoutTransaction = Materai::query()
            ->select([
                'materais.*',
                'petugas.nama as petugas_name',
                'materais.date as sort_date',
                'materais.created_at as sort_time'
            ])
            ->join('petugas', 'petugas.id', '=', 'materais.petugas_id')
            ->where('materais.status', 1)
            ->orderBy('sort_time', 'desc');

        return $queryWithoutTransaction;
    }
}
