<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function allCategoryList()
    {
        $category = Category::all();
        return $this->apiResponse($category, 'Category List', true, 200);
    }


    public function createOrUpdateCategory(Request $request)
    {

        try {

            if (empty($request->id)) {
                //category create

                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'description' => 'nullable|max:200',
                    'is_active'  => "required",
                    'is_show'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable'

                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 403);
                }

                $imageName = "";
                if ($image = $request->file('photo')) {
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = Null;
                }

                $category = new Category();
                $category->name = $request->name;
                $category->description = $request->description;
                $category->is_active = $request->boolean('is_active');
                $category->is_show =$request->boolean('is_show');
                $category->photo = $imageName;
                $category->save();


                return $this->apiResponse([], 'Category Created Successfully', true, 200);
            } else {

                //category update

                $category = Category::findOrFail($request->id);
                $imageName = "";
                if ($image = $request->file('photo')) {
                    
                    if ($category->photo) {
                        unlink(public_path("images/" . $category->photo));
                    }

                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $category->photo;
                }

                Category::where('id', $request->id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_active' => $request->boolean('is_active'),
                    'is_show' => $request->boolean('is_show'),
                    'photo' => $imageName,

                ]);
                return $this->apiResponse([], 'Category Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }



    public function singleCategory($id)
    {
        try {
            $category = Category::find($id);
            return $this->apiResponse($category, 'Single Category', true, 200);
        } catch (\Throwable $th) {

            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }





    public function deleteCategory($id)
    {

        try {
            $category = Category::findOrFail($id);

            if ($category->photo) {
                unlink(public_path("images/" . $category->photo));
            }
            
            $category->delete();

            return $this->apiResponse([], 'Category Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
