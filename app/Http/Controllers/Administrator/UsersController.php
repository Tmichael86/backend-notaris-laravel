<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    private $fileDirUpload = 'profile';

    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.users.index', $data);
    }

    public function fetch()
    {
        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = User::leftJoin('groups', 'groups.id', '=', 'users.group_id')
            ->select('group_nama', 'users.*')
            ->where('users.status', 1)
            ->get();

        return DataTables::of($DB)
            ->editColumn('id', function (User $user) {
                return System::strEncode($user->id);
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
            'no_telp' => 'numeric',
            'group_id' => 'numeric',
            'email' => 'required|email',
            'username' => 'required|alpha_num',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
            'alamat' => 'nullable',
            'user_img' => 'mimes:png,jpg,jpeg|max:512',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'group_id' => $request->input('group_id'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'nama' => $request->input('nama'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> File Upload
        if ($request->file('user_img') != null) {
            $path = $request->file('user_img')->store(
                $this->fileDirUpload,
                'uploads'
            );

            $data['image'] = str_replace($this->fileDirUpload.'/', '', $path);
        }

        // ==> Password
        if ($request->input('password') != '') {
            $data['password'] = Hash::make($request->input('password'));
        }

        // ==> Save Data
        $i = User::create($data);
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
            'group_id' => 'numeric',
            'nama' => 'required',
            'no_telp' => 'numeric',
            'email' => 'required|email',
            'username' => 'required|alpha_num',
            'password' => 'nullable',
            'password_confirm' => 'nullable|same:password',
            'alamat' => 'nullable',
            'user_img' => 'mimes:png,jpg,jpeg|max:512',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('update', [
            'group_id' => $request->input('group_id'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'nama' => $request->input('nama'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);

        // ==> File Upload
        if ($request->file('user_img') != null) {
            $path = $request->file('user_img')->store(
                $this->fileDirUpload,
                'uploads'
            );

            $data['image'] = str_replace($this->fileDirUpload.'/', '', $path);
        }

        // ==> Password
        if ($request->input('password') != '') {
            $data['password'] = Hash::make($request->input('password'));
        }

        // ==> Save Data
        $i = User::where('id', $id)->update($data);
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

        $i = User::where('id', $id)->update(System::crudIdentity('delete'));
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
