<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ResetPasswordController extends BaseController
{
    public function sendResetLink(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
        ]);

        try {
            $user = User::where("email", "=", $request->email)->first();
            app("App\Services\PHPMailerService")->sendResetPasswordEmail($user);
            return response()->json($this->responseTemplate(true, "check you email for reset password link."));
        } catch (QueryException $e) {
            return response()->json($this->responseTemplate(false, "failed to sent reset password link.", [["mesage"=>"database error occurred"]]));
        } catch (Exception $e) {
            return response()->json($this->responseTemplate(false, "failed to sent reset password link.", [["mesage"=>"unknown error occurred"]]));
        }

    }

    public function resetPassword(String $token, Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
            "password" => "required|string",
        ]);

        $checkToken = DB::table("password_reset_tokens")
        ->where("email", "=", $request->email)
        ->where("token", "=", $token)
        ->first();
        if(!$checkToken)
            return response()->json($this->responseTemplate(false, "failed to reset password", [["message" => "invalid reset link"]]));
        
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $user = User::findOrFail($payload->get("sub"));
            if ($user->email != $payload->get("email"))
                return response()
                    ->json($this->responseTemplate(false, "failed to reset password", [["message"=>"invalid link or mismatch email"]]));
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            return response()->json($this->responseTemplate(true, "password changed successfully"));
        } catch (QueryException $e) {
            return response()->json($this->responseTemplate(false, "failed to reset password", [["message"=>"database error occurred"]]));
        } catch (TokenExpiredException $e) {
            return response()->json($this->responseTemplate(false, "failed to reset password", [["message"=>"token is expired"]]));
        } catch (TokenInvalidException $e) {
            return response()->json($this->responseTemplate(false, "failed to reset password", [["message"=>"token is invalid"]]));
        } catch (Exception $e) {
            return response()->json($this->responseTemplate(false, "failed to reset password", [["message"=>"unknown error occurred"]]));
        }
    }
}