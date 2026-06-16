<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index()
    {   
        return view('administrator.login.index');
    }

    public function authentication(Request $request)
    {

        $username = $request->input('username');
        $password = $request->input('password');
        $captchaRes = $request->input('g-recaptcha-response');
        
        $routeType = $request->route()->getPrefix() == 'api' ? 'api' : 'web';

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);

        $dataAuth[$isEmail ? 'email' : 'username'] = $username;
        $dataAuth['password'] = $password;
        $dataAuth['status'] = 1;

        // reCaptcha Validation
        // if ($routeType == "web") {
        //     $result = $this->reCaptchaValidation($captchaRes);
        //     if ($result['error'] == true) return $this->responseServer(403, [
        //         'statusCode' => 403,
        //         'message' => $result['message']
        //     ]);
        // }

        $token = Auth::guard($routeType)->attempt($dataAuth);
        if (! $token) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Username atau Password salah, Mohon coba lagi..',
            ]);
        }

        // Session
        // DB::table('sessions')->where('user_id', Auth::id())->delete();
        Session::put('uid', Auth::id());

        if (Auth::check()) {
            DB::beginTransaction();
            try {
                DB::table('sessions')->where('user_id', Auth::id())->lockForUpdate()->get();

                $session = DB::table('sessions')
                    ->where('user_id', Auth::id())
                    ->orderBy('last_activity', 'asc')
                    ->first();

                $now = now()->timestamp;
                $timeout = 120 * 60;
                if ($session) {
                    if (($now - $session->last_activity) > $timeout) {
                        DB::table('sessions')->where('user_id', Auth::id())->delete();
                    }elseif($session->id !== session()->getId()){
                        DB::commit();
                        Auth::logout();
                        return response()->json([
                            'statusCode' => 403,
                            'message' => 'Akun ini sedang digunakan di perangkat lain.',
                        ], 403);
                    }
                }

                DB::table('sessions')->where('user_id', Auth::id())->delete();
                session()->regenerate();
                Session::put('uid', Auth::id());

                DB::commit();

                return $this->responseServer(200, [
                    'statusCode' => 200,
                    'message' => 'Login berhasil dilakukan',
                    'data' => ['token' => $token],
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'statusCode' => 500,
                    'message' => 'Terjadi kesalahan server.',
                ], 500);
            }
        }
    }

    public function logout()
    {
        $this->unlockData();
        // Remove Session
        Session::flush();
        Auth::logout();

        return redirect()->to(route('login'));
    }
}
