<?php

namespace App\Http\Controllers\Administrator\Master;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisKelamin;
use App\Models\Pemohon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PemohonController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.master.pemohon.index', $data);
    }

    public function fetch()
    {
        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'you are not authorized to access this page',
            ]);
        }

        $DB = Pemohon::where('status', 1)
            ->get();

        return DataTables::of($DB)
            ->editColumn('id', function (Pemohon $pemohon) {
                return System::strEncode($pemohon->id);
            })
            ->addColumn('jenis_kelamin_nama', function (Pemohon $pemohon) {
                return JenisKelamin::select('nama')->where('id', $pemohon->jenis_kelamin)->value('nama');
            })
            ->toJson();
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
            'nik' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'numeric',
            'alamat' => 'nullable',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'nama' => $request->input('nama'),
            'nik' => $request->input('nik'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> Save Data
        $i = Pemohon::create($data);
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
            'nik' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'numeric',
            'alamat' => 'nullable',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('update', [
            'nama' => $request->input('nama'),
            'nik' => $request->input('nik'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> Save Data
        $i = Pemohon::where('id', $id)->update($data);
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

        $i = Pemohon::where('id', $id)->update(System::crudIdentity('delete'));
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

    public function getJenisKelamin(Request $request)
    {
        $selected_id = $request->input('selected');
        $data = JenisKelamin::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--pilih--'];
        if ($selected_id != '') {
            foreach ($data as $key => $value) {
                if ($value['id'] == $selected_id) {
                    $data[$key]['selected'] = true;
                }
            }
        }
        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
