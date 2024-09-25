<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserHasBusiness;
use App\Models\UserHasPermission;
use App\Models\UserHasRole;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use App\Services\PHPMailerService;

class AuthController extends BaseController
{
    protected $mailer;

    public function __construct(PHPMailerService $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validetor = Validator::make($request->all(), [
            'role' => 'required|string|in:user',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:12',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validetor->fails())
            return response()->json([
                "success" => false,
                "message" => "Credential validation error",
                "errors" => $validetor->errors()
            ], 422);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        // $token = JWTAuth::fromUser($user);

        $role = Role::where("role", "=", $request->role)->first();

        $userHasRole = UserHasRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        $permissions = $role->getPermissions();

        $permissionsRecord = $permissions->map(function ($perm) use ($user) {
            return [
                'id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'permission_id' => $perm->id,
            ];
        })->toArray();

        try {
            UserHasPermission::insert($permissionsRecord);

            $this->mailer->sendVerificationEmail($user);

            $data = ["user" => $user, "role" => $role->role];
            return response()->json($this->responseTemplate(true, "account created successfully, please check you email.", null, $data), 201);
        } catch (QueryException $e) {
            return response()->json([
                "success" => false, 
                "message" => "failed to create user", 
                "errors" => [
                        [
                            "message" => "permissions can't be set for the user",
                        ],
                    ] 
                ], 500);
        }

    }

        /**
     * Register a new user.
     */
    public function registerBusiness(Request $request)
    {
        $validetor = Validator::make($request->all(), [
            'role' => 'required|string|in:business',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:12',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',

            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'industry_type' => 'nullable|string|max:255',
        ]);
        if ($validetor->fails())
            return response()->json([
                "success" => false,
                "message" => "Credential validation error",
                "errors" => $validetor->errors()
            ], 422);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        $role = Role::where("role", "=", $request->role)->first();

        $userHasRole = UserHasRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        $permissions = $role->getPermissions();

        $permissionsRecord = $permissions->map(function ($perm) use ($user) {
            return [
                'id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'permission_id' => $perm->id,
            ];
        })->toArray();

        try {
            UserHasPermission::insert($permissionsRecord);
        } catch (QueryException $e) {
            return response()->json([
                "success" => false, 
                "message" => "failed to create user", 
                "errors" => [
                        [
                            "message" => "permissions can't be set for the user",
                        ],
                    ] 
                ], 500);
        }
        
        $businessData = $request->only('business_name','business_registration_number','bussines_phone_number','primary_contact_person','business_email','website','industry_type','shipping_volume','preferred_shipping_method');
        $businessData += array("manager_id" => $user->id);
        
        try {
            UserHasBusiness::create($businessData);
            return response()->json([
                "success" => true,
                "message" => "account created successfully",
                "errors" => null,
                "user" => $user,
                "role" => $role->role,
                "token" => $token
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                "success" => false, 
                "message" => "failed to create user", 
                "errors" => [
                        [
                            "message" => "business informations can't be set for the user",
                        ],
                    ] 
                ], 500);
        }
    }


    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'invalid credentials',
                    'errors' => [
                        [
                            "message" => "wrong email or password",
                        ]
                    ]
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => "couldn't create token",
                'errors' => [
                    [
                        "message" => "Something may have gone wrong, please try again.",
                    ]
                ]
            ], 500);
        }
        $user = JWTAuth::user();
        if (!$user->hasVerifiedEmail() || $user->status == "pending")
            return response()->json([
                'success' => false,
                'message' => 'can\'t login',
                'errors' => [
                    [
                        "message" => "account is not activated",
                    ]
                ]
            ], 400);
        else if ($user->status == "suspended")
            return response()->json([
                'success' => false,
                'message' => 'can\'t login',
                'errors' => [
                    [
                        "message" => "account is suspended",
                    ]
                ]
            ], 400);

        return $this->respondWithToken($token, JWTAuth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    // public function logout()
    // {
    //     auth('api')->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh(), JWTAuth::user());
    }

    /**
     * Get the authenticated User.
     */
    public function userProfile()
    {
        return response()->json([
            "success" => true,
            "message" => null,
            "errors" => null,
            "user" => JWTAuth::user()
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'logged in successfully',
            'errors' => null,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'role' => $user->role()->role,
            'user' => $user
        ]);
    }
}
