<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\FlareClient\Api;

class AuthorController extends Controller

{
    use ApiResponseTrait;

    public function allAuthorList()
    {
        $author = Author::all();
        return $this->apiResponse($author, 'Author List', true, 200);
    }



    public function createOrUpdateAuthor(Request $request)
    {
        try {
            $authId = Auth::user()->id;
            if (empty($request->id)) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact'  => "nullable|max:50",
                    'address1'  => "nullable|max:800",
                    'address2'  => "nullable|max:800",
                    'is_active'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 403);
                }

                $filename = "";
                if ($image = $request->file('photo')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }
                $author = new Author();
                $author->name = $request->name;
                $author->email = $request->email;
                $author->contact = $request->contact;
                $author->mobile = $request->mobile;
                $author->address1 = $request->address1;
                $author->address2 = $request->address2;
                $author->is_active = $request->boolean('is_active');
                $author->bio = $request->bio;
                $author->is_show = $request->boolean('is_show');
                $author->photo = $filename;
                $author->created_by = $authId;
                $author->save();

                return $this->apiResponse([], 'Author Created Successfully', true, 200);
            } else {

                //Author update

                $authId = Auth::user()->id;
                $author = Author::findOrFail($request->id);
                $imageName = "";


                if ($image = $request->file('photo')) {

                    if ($author->photo) {
                        unlink(public_path("images/" . $author->photo));
                    }
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $author->photo;
                }

                Author::where('id', $request->id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact' => $request->contact,
                    'mobile' => $request->mobile,
                    'address1' => $request->address1,
                    'address2' => $request->address2,
                    'is_active' => $request->boolean('is_active'),
                    'is_show' => $request->boolean('is_show'),
                    'bio' => $request->bio,
                    'photo' => $imageName,
                    'updated_by' => $authId,
                ]);

                return $this->apiResponse([], 'Author has been updated successfully', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], 'Something went wrong', false, 500);
        }
    }

    public function singleAuthor($id)
    {
        $singalAuthor = Author::find($id);
        return $this->apiResponse($singalAuthor, 'Single Author', true, 200);
    }


    public function deleteAuthor($id)
    {
        try {
            $Author = Author::findOrFail($id);
            if ($Author->photo) {
                unlink(public_path("images/" . $Author->photo));
            }
            $Author->delete();
            return $this->apiResponse([], 'Author has been deleted successfully', true, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], 'Something went wrong', false, 500);
        }
    }
}
