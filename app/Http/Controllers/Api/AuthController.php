<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\withCookie;

class AuthController extends Controller
{
    /**
     * Create User 
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    // 'username' => 'required|min:4|unique:users,username',
                    'is_active' => 'required',
                    'phone' => 'required',
                    'user_role' => 'required',
                    'password' => [
                        'required',
                        'min:6',
                        // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                    ],

                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_role' => $request->user_role,
                'phone' => $request->phone,
                'is_active' => $request->is_active,
                'password' => Hash::make($request->password)

            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'data' => []
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User and return token
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                    'data' => []
                ], 401);
            }
            $user = User::where('email', $request->email)->first();




            if ($user->is_active !== true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active. Please contact with admin.',
                    'data' => []
                ], 401);
            }

            $response_user = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_role' => $user->user_role,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];



            $data = [
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' =>  $response_user
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
