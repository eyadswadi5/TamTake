<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\api\BaseController;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyEmailController extends BaseController
{
    public function verify($token)
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $user = User::findOrFail($payload->get("sub"));

            if ($user->email === $payload->get("email")) {
                if ($user->hasVerifiedEmail())
                    return response()->json($this->responseTemplate(false, "Your email is already verified."), 400);
    
                $user->email_verified_at = now();
                $user->status = "activated";
                $user->save();
    
                return response()->json($this->responseTemplate(true, "your account activated successfully"), 200);
            } else {
                return response()->json($this->responseTemplate(false, "invalid verification link or email mismatch"), 400);
            }
        } catch (TokenExpiredException $e) {
            return response()->json($this->responseTemplate(false, 'Verification link expired'), 400);
        } catch (TokenInvalidException $e) {
            return response()->json($this->responseTemplate(false, 'Invalid token'), 400);
        } catch (Exception $e) {
            return response()->json($this->responseTemplate(false, 'Something went wrong'), 500);
        }

    }

    public function resend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|uuid|exists:users,id",
        ]);
        if ($validator->fails())
            return response()->json($this->responseTemplate(false, "failed to generate verification link",[["message" => "can't find user",]]));

        $user = User::find($request->id);

        if ($user->hasVerifiedEmail()) {
            return response()->json($this->responseTemplate(false, "Your email is already verified."), 400);
        }

        app('App\Services\PHPMailerService')->sendVerificationEmail($user);

        return response()->json($this->responseTemplate(true, "A fresh verification link has been sent to your email address."), 200);
    }
}
