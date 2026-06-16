<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\KonfigurasiUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KonfigurasiUmumController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        $data['data'] = KonfigurasiUmum::query()->first();

        return view('administrator.konfigurasi_umum.index', $data);
    }

    public function update(Request $request)
    {
        try {
            setAccessibilityPermission('create');

            $validated = Validator::make($request->all(), [
                'alamat' => 'nullable',
                'telp_rumah' => 'nullable',
                'telp_pertama' => 'nullable',
                'telp_kedua' => 'nullable',
                'email' => 'nullable',
                'notaris_bersangkutan' => 'nullable',
                'ppat_bersangkutan' => 'nullable',
                'besaran_nilai_tidak_kena_pajak' => 'nullable',
                'pengecekan' => 'nullable',
                'surat_kuasa_membebankan_hak_tanggungan' => 'nullable',
                'ploting_validasi' => 'nullable',
                'harga_beli_materai' => 'nullable',
                'harga_jual_materai' => 'nullable',
            ]);

            if ($validated->fails()) {
                return $this->badRequest($validated);
            }

            $data = System::crudIdentity('update', [
                'alamat' => $request->alamat,
                'telp_rumah' => $request->telp_rumah,
                'telp_pertama' => $request->telp_pertama,
                'telp_kedua' => $request->telp_kedua,
                'email' => $request->email,
                'notaris_bersangkutan' => $request->notaris_bersangkutan,
                'ppat_bersangkutan' => $request->ppat_bersangkutan,
                'besaran_nilai_tidak_kena_pajak' => $request->besaran_nilai_tidak_kena_pajak,
                'pengecekan' => $request->pengecekan,
                'surat_kuasa_membebankan_hak_tanggungan' => $request->surat_kuasa_membebankan_hak_tanggungan,
                'ploting_validasi' => $request->ploting_validasi,
                'harga_jual_materai' => $request->harga_jual_materai,
                'harga_beli_materai' => $request->harga_beli_materai
            ]);

            if (!$data) {
                return $this->responseServer([
                    "statusCode" => 401,
                    "message" => "Gagal Mengupdate Data"
                ]);
            }

            $konfigurasi = KonfigurasiUmum::query()->find(1);

            if (!$konfigurasi) {
                throw new \Exception('Data konfigurasi tidak ditemukan');
            }

            $result = $konfigurasi->update($data);

            if (!$result) {
                throw new \Exception('Gagal menyimpan data konfigurasi');
            }

            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseServer(422, [
                'statusCode' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ]);
        } catch (\Exception $e) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
