<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\withCookie;
use App\Http\Traits\ApiResponseTrait;
use PHPUnit\Framework\MockObject\Api;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function createUser(Request $request)
    {




        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'username' => 'required|min:4|unique:users,username',
                    // 'is_active' => 'required',
                    'phone' => 'required',
                    // 'user_role' => 'required',
                    'password' => [
                        'required',
                        'min:6',
                        // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                    ],

                ]
            );



            if ($validateUser->fails()) {
                return $this->apiResponse([], $validateUser->errors()->first(), false, 403);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password)

            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'data' => []
            ], 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }





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
                return $this->apiResponse([], $validateUser->errors()->first(), false, 403);
            }


            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->apiResponse([], 'Invalid Credentials', false, 403);
            }
            $user = User::where('email', $request->email)->first();

            //only general_user can login
            if ($user->user_role !== 'admin') {
               
                return $this->apiResponse([], 'Invalid Credentials', false, 403);
            }




            if ($user->is_active !== true) {
               return $this->apiResponse([], 'Your account is not active', false, 403);
            }

            $response_user = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_role' => $user->user_role,
                'image' => $user->image,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];



            $data = [
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' =>  $response_user
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }



    public function clientLogin(Request $request)
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
                return $this->apiResponse([], $validateUser->errors()->first(), false, 403);
            }





            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->apiResponse([], 'Invalid Credentials', false, 403);
            }
            $user = User::where('email', $request->email)->first();



            if ($user->is_active !== true) {
                return $this->apiResponse([], 'Your account is not active', false, 403);
            }

            $response_user = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_role' => $user->user_role,
                'image' => $user->image,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];



            $data = [
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' =>  $response_user
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
