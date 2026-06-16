<?php

use App\Http\Libraries\System;

// => Set Permission Helper
if (! function_exists('setAccessibiltyPermission')) {
    function setAccessibilityPermission(string $status)
    {
        if (! System::getAccess($status)) {
            return handleResponseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }
    }

}
