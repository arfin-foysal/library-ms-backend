<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
    use ApiResponseTrait;
    public function allUserList()
    {
        
        $user = User::where('id', '!=' , 1)->get();
        return $this->apiResponse($user, 'All User List', true, 200);

    }

   
    public function createOrUpdateUser(Request $request)
    {
        try {

            if(empty($request->id)){
                //user create
                $request->validate([
                    'name' => 'required|max:100',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'status'  => "required",
                    'user_role'  => "required",
                    'phone'=> 'required|numeric',
                    'profile_photo_path' => 'image|mimes:jpeg,jpg,png,gif|nullable',
           
                
                ]);

                $filename = "";
                if ($image = $request->file('profile_photo_path')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = bcrypt($request->password);
                $user->status = $request->status;
                $user->user_role = $request->user_role;
                $user->profile_photo_path = $filename;
                $user->save();

                return $this->apiResponse([], 'User Created Successfully', true, 200);
            }else{

                //user update
                $request->validate([
                    'name' => 'required|max:100',
                    'email' => 'required|email|unique:users,email,'.$request->id,
                    'status'  => "required",
                    'user_role'  => "required",
                    'phone'=> 'required|numeric',
                    'profile_photo_path' => 'image|mimes:jpeg,jpg,png,gif|nullable',
           
                
                ]);

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
                $user->status = $request->status;
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
            if ($user->photo) {
                unlink(public_path("images/" . $user->profile_photo_path));
            }
            $user->delete();

            return $this->apiResponse([], 'User Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
