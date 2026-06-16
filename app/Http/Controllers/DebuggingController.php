<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebuggingController extends Controller
{
    /*===========================
    *   Parsing data from JWT
    * ===========================
    *
    * $request->get('key')
    */

    public function index(Request $request)
    {
        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Debugging successful',
        ]);
    }
}
