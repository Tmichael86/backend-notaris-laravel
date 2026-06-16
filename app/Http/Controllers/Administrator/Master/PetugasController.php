<?php

namespace App\Http\Controllers\Administrator\Master;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisKelamin;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.master.petugas.index', $data);
    }

    public function fetch()
    {
        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = Petugas::where('status', 1)
            ->get();

        return DataTables::of($DB)
            ->editColumn('id', function (Petugas $petugas) {
                return System::strEncode($petugas->id);
            })
            ->addColumn('jenis_kelamin_nama', function (Petugas $petugas) {
                $jenis_kelamin = JenisKelamin::select('nama')->where('id', $petugas->jenis_kelamin)->value('nama');

                return $jenis_kelamin;
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
            'nik' => 'required',
            'user_id' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'numeric',
            'email' => 'required|email',
            'alamat' => 'nullable',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'nik' => $request->input('nik'),
            'nama' => $request->input('nama'),
            'user_id' => $request->input('user_id'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' =>  Carbon::createFromFormat('d/m/Y', $request->input('tanggal_lahir'))->format('Y-m-d'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'no_telp' => $request->input('no_telp'),
            'email' => $request->input('email'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> Save Data
        $i = Petugas::create($data);
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
            'nik' => 'required',
            'user_id' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'numeric',
            'email' => 'required|email',
            'alamat' => 'nullable',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('update', [
            'nik' => $request->input('nik'),
            'user_id' => $request->input('user_id'),
            'nama' => $request->input('nama'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' =>  Carbon::createFromFormat('d/m/Y', $request->input('tanggal_lahir'))->format('Y-m-d'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'no_telp' => $request->input('no_telp'),
            'email' => $request->input('email'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> Save Data
        $i = Petugas::where('id', $id)->update($data);
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

        $i = Petugas::where('id', $id)->update(System::crudIdentity('delete'));
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

    public function getUser(Request $request)
    {
        $selected_id = $request->input('selected');
        $data = User::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
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
