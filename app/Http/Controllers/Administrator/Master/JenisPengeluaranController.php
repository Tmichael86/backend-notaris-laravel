<?php

namespace App\Http\Controllers\Administrator\Master;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisPengeluaranController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.master.jenis_pengeluaran.index', $data);
    }

    public function fetch()
    {
        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = JenisPengeluaran::where('status', 1)
            ->get();

        return DataTables::of($DB)
            ->editColumn('id', function (JenisPengeluaran $jenis) {
                return System::strEncode($jenis->id);
            })
            ->toArray();
    }

    public function store(Request $request)
    {
        if (! System::getAccess('create')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'nama' => $request->input('nama'),
        ]);

        // ==> Save Data
        $i = JenisPengeluaran::create($data);
        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil disimpan',
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! System::getAccess('update')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('update', [
            'nama' => $request->input('nama'),
        ]);

        // ==> Save Data
        $i = JenisPengeluaran::where('id', $id)->update($data);
        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat mengupdate data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        if (! System::getAccess('delete')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $i = JenisPengeluaran::where('id', $id)->update(System::crudIdentity('delete'));
        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat hapus data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil dihapus',
        ]);
    }
}
