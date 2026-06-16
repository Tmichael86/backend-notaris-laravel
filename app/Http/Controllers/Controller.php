<?php

namespace App\Http\Controllers;

use App\Http\Libraries\System;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Transaksi;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseServer($statusCode, $data = [])
    {
        /*
        *   Informasi Status Code Server
        *
        *   200 => Success (Digunakan ketika sukses mengakses halaman)
        *   400 => Bad Request (Digunakan ketika validasi form tidak valid)
        *   401 => Unauthorized  (Digunakan ketika user tidak login)
        *   403 => Forbidden (Digunakan ketika token tidak valid)
        *   500 => Internal Server Error (Digunakan ketika terjadi kesalahan di sisi server)
        */

        return response()->json($data, $statusCode, ['Content-type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    protected function badRequest($validator)
    {
        $getErrors = $validator->errors()->messages();
        $indexArr = array_keys((array) $getErrors);

        $messageError = [];
        foreach ($indexArr as $key) {
            $messageData = $validator->errors()->get($key);
            $messageError[$key] = @$messageData[0];
        }

        return response()->json([
            'statusCode' => 400,
            'message' => 'Mohon isi form dengan benar !',
            'data' => [
                'error' => $messageError,
            ],
        ], 400, ['Content-type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    protected function decodeId($str, $except = '')
    {
        if ($str == $except) {
            return 0;
        }

        $id = System::strDecode($str);
        if (! $id) {
            return false;
        }

        return $id;
    }

    public function unlockData()
    {
        $userID = auth()->id();
        Transaksi::where('locked_by', $userID)->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    protected function reCaptchaValidation($captchaRes)
    {
        $urlVal = "https://www.google.com/recaptcha/api/siteverify";
        $secretKey = env('G_CAPTCHA_SECRET_KEY');

        if ($captchaRes == "") return [
            'error' => true,
            'message' => 'Centang reCaptcha terlebih dahulu'
        ];

        try {
            $response = file_get_contents("$urlVal?secret=$secretKey&response=$captchaRes");
            $jsonDec = json_decode($response);

            if (@$jsonDec->success != true) return [
                'error' => true,
                'message' => 'reCaptcha is not valid'
            ];

            return [
                'error' => false,
                'message' => 'Validation successful'
            ];
        } catch (\Throwable $th) {
            return [
                'error' => true,
                'message' => 'Validation reCaptcha error'
            ];
        }
    }
}
