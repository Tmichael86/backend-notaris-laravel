<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private $fileDirUpload = 'profile';

    public function index()
    {
        $users = User::find(session()->get('uid'));

        return view('administrator.profile.index', ['user' => $users]);
    }

    public function save(Request $request)
    {
        $this->unlockData();
        $validated = Validator::make($request->all(), [
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

        $data = [
            'group_id' => 1,
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'nama' => $request->input('nama'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

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
        $i = User::where('id', session()->get('uid'))->update($data);
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
}
