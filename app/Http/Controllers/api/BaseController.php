<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function responseTemplate($success = true, $message = null, $errors = null, $data = null) {
        $response = [
            "success" => $success,
            "message" => $message,
            "errors" => $errors
        ];
        if ($data != null) 
            $response += $data;
        
        return $response;
    }
}
