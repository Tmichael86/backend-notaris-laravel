<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        if ($routeType == "web") {
            $result = $this->reCaptchaValidation($captchaRes);
            if ($result['error'] == true) return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => $result['message']
            ]);
        }

        $token = Auth::guard($routeType)->attempt($dataAuth);
        if (! $token) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Username atau Password salah, Mohon coba lagi..',
            ]);
        }

        // Session
        Session::put('uid', Auth::id());

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Login berhasil dilakukan',
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function logout()
    {
        // Remove Session
        $this->unlockData();
        Session::flush();
        Auth::logout();

        return redirect()->to(route('login'));
    }
}
