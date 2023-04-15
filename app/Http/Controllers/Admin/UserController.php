<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
    use ApiResponseTrait;
    public function allUserList()
    {

        $user = User::where('id', '!=', 1)
            ->where('id', '!=', auth()->user()->id)
            ->get();
        return $this->apiResponse($user, 'All User List', true, 200);
    }


    public function createOrUpdateUser(Request $request)
    {
        try {

            if (empty($request->id)) {
                //user create
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'is_active'  => "required",
                    'user_role'  => "required",
                    'phone' => 'required',
                    'profile_photo_path' => 'image|mimes:jpeg,jpg,png,gif|nullable'


                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors(), false, 422);
                }



                $user = User::findOrFail($request->id);
                $imageName = "";
                if ($image = $request->file('profile_photo_path')) {
                    if ($user->profile_photo_path) {
                        unlink(public_path("images/" . $user->profile_photo_path));
                    }
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $user->profile_photo_path;
                }



                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = bcrypt($request->password);
                $user->is_active = $request->boolean('is_active');
                $user->user_role = $request->user_role;
                $user->profile_photo_path = $imageName;
                $user->save();

                return $this->apiResponse([], 'User Created Successfully', true, 200);
            } else {

                //user update
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $request->id,
                    'is_active'  => "required",
                    'user_role'  => "required",
                    'phone' => 'required',


                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors(), false, 409);
                }

                $filename = "";
                if ($image = $request->file('profile_photo_path')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }

                $user = User::find($request->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->is_active = $request->boolean('is_active');
                $user->user_role = $request->user_role;
                $user->profile_photo_path = $filename;
                $user->save();

                return $this->apiResponse([], 'User Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function show($id)
    {
        //
    }



    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->profile_photo_path) {
                unlink(public_path("images/" . $user->profile_photo_path));
            }
            $user->delete();

            return $this->apiResponse([], 'User Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function passwordReset(request $request)
    {

        try {

            $reset = User::where('id', '=', $request->id)->first();
            $reset->password = bcrypt($request->password);
            $reset->save();
            
            return $this->apiResponse([], 'Password Reset Successfully', true, 200);


        } catch (\Throwable $th) {

            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
