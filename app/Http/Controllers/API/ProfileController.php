<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private $fileDirUpload = 'profile';

    public function show(Request $request)
    {
        $id = $request->get('uid');
        $data = User::where('id', $id)->first();

        if (! $data) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'User profile not found',
            ]);
        }

        // URL Images
        $data->image = $data->image ? asset('/uploads/profile/'.$data->image) : null;

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Fetching data successful',
            'data' => $data,
        ]);
    }

    public function save(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required',
            'no_telp' => 'numeric',
            'email' => 'required|email',
            'username' => 'required|alpha_num',
            'password' => 'nullable',
            'alamat' => 'nullable',
            'images' => 'mimes:png,jpg,jpeg|max:1024',
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
        if ($request->file('images') != null) {
            $path = $request->file('images')->store(
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
        $i = User::where('id', $request->get('uid'))->update($data);
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
