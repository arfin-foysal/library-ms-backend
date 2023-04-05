<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ThirdSubCategory;
use Illuminate\Http\Request;

class ThirdSubCategoryController extends Controller
{

    use ApiResponseTrait;


    public function allThirdSubCategoryList()
    {

        $thirdSubCategory = ThirdSubCategory::get();
        return $this->apiResponse($thirdSubCategory, 'All Third Sub Category List', true, 200);
    }


    public function createOrUpdateThirdSubCategory(Request $request)
    {
        try {



            if (empty($request->id)) {
                //third sub category create
                $request->validate([
                    'name' => 'required|max:100',
                    'description' => 'nullable|max:200',
                    'status'  => "required",
                    'sub_category_id'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable'
                ]);

                $imageName = "";
                if ($image = $request->file('photo')) {
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = Null;
                }

                $thirdSubCategory = new ThirdSubCategory();
                $thirdSubCategory->name = $request->name;
                $thirdSubCategory->sub_category_id = $request->sub_category_id;
                $thirdSubCategory->description = $request->description;
                $thirdSubCategory->status = $request->status;
                $thirdSubCategory->photo = $imageName;
                $thirdSubCategory->save();

                return $this->apiResponse([], 'Third Sub Category Created Successfully', true, 200);
            } else {
                //third sub category update
                $request->validate([
                    'name' => 'required|max:100',
                    'description' => 'nullable|max:200',
                    'status'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable'
                ]);

                $subCategory = ThirdSubCategory::findOrFail($request->id);
                $imageName = "";
                if ($image = $request->file('photo')) {

                    if ($subCategory->photo) {
                        unlink(public_path("images/" . $subCategory->photo));
                    }
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $subCategory->photo;
                }
                $thirdSubCategory = ThirdSubCategory::find($request->id);
                $thirdSubCategory->name = $request->name;
                $thirdSubCategory->sub_category_id = $request->sub_category_id;
                $thirdSubCategory->description = $request->description;
                $thirdSubCategory->status = $request->status;
                $thirdSubCategory->photo = $imageName;
                $thirdSubCategory->save();
                return $this->apiResponse([], 'Third Sub Category Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function ThirdSubCategorybySubCategoryId(Request $request)
    {
        try {
            $subCategoryId = $request->sub_category_id;


            $thirdSubCategory = ThirdSubCategory::where('sub_category_id', $subCategoryId)->get();

            return $this->apiResponse($thirdSubCategory, 'Third Sub Category List By Sub Category', true, 200);
        } catch (\Throwable $th) {
            //throw $th;

            return $this->apiResponse([], $th->getMessage() , false, 500);
        }
    }





    public function deleteThirdSubCategory($id)
    {
        try {
            $thirdSubCategory = ThirdSubCategory::findOrFail($id);
            if ($thirdSubCategory->photo) {
                unlink(public_path("images/" . $thirdSubCategory->photo));
            }
            $thirdSubCategory->delete();
            return $this->apiResponse([], 'Third Sub Category Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
