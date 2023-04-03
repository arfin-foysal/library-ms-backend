<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allAuthorList()
    {
        $author=Author::all();
        return response()->json([
            'status'=>true,
            'message'=>'Author List',
            'data'=>$author
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createAuthor(Request $request)
    {
        
        // $authId = Auth::user()->id;
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



        $author= new Author();
        $author->name=$request->name;
        $author->email=$request->email;
        $author->contact=$request->contact;
        $author->mobile=$request->mobile;
        $author->address1=$request->address1;
        $author->address2=$request->address2;
        $author->status=$request->status;
        $author->bio=$request->bio;
        $author->is_show=$request->is_show;
        $author->photo=$filename;
        $author->created_by=1;
        $author->save();


        return response()->json([
            'status'=>true,
            'message'=>'Author has been added successfully',
            'data'=>[]
        ],200);



        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function singleAuthor($id)
    {
       $singalAuthor=Author::find($id);
       return response()->json([
           'status'=>true,
           'message'=>'Single Author',
           'data'=>$singalAuthor
       ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
