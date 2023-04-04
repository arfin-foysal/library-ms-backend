<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\FlareClient\Api;

class AuthorController extends Controller

{
    use ApiResponseTrait;
    public function allAuthorList()
    {


        $author = Author::all();
        return response()->json([
            'status' => true,
            'message' => 'Author List',
            'data' => $author
        ], 200);
    }

    public function createOrUpdateAuthor(Request $request)
    {
        try {
            $authId = Auth::user()->id;
            if (empty($request->id)) {

                $request->validate([
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact'  => "nullable|max:50",
                    'address1'  => "nullable|max:800",
                    'address2'  => "nullable|max:800",
                    'status'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
                ]);

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
                $author->status = $request->status;
                $author->bio = $request->bio;
                $author->is_show = $request->is_show;
                $author->photo = $filename;
                $author->created_by = $authId;
                $author->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Author has been added successfully',
                    'data' => []
                ], 200);
            } else {
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
                    'status' => $request->status,
                    'bio' => $request->bio,
                    'is_show' => $request->is_show,
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
        return response()->json([
            'status' => true,
            'message' => 'Single Author',
            'data' => $singalAuthor
        ], 200);
    }


    public function deleteAuthor($id)
    {
        try {
            $Author = Author::findOrFail($id);
            if ($Author->photo) {
                unlink(public_path("images/" . $Author->photo));
            }
            $Author->delete();

            return response()->json([
                'status' => true,
                'message' => 'Author Delate Successfully.',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
