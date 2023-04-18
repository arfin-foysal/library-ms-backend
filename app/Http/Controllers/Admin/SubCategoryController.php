<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    use ApiResponseTrait;
    public function allSubCategoryList()
    {
        $subCategory = SubCategory::
        leftJoin("categories","sub_categories.category_id","=","categories.id")
        ->select("sub_categories.*","categories.name as category_name")
        ->get();
        return $this->apiResponse($subCategory, 'Sub Category List', true, 200);
    }


    public function createOrUpdateSubCategory(Request $request)

    {

        try {

            if (empty($request->id)) {
                //sub category create
                $validator = Validator::make($request->all(),[
                    'name' => 'required|max:100',
                    'description' => 'nullable|max:200',
                    'is_active'  => "required",
                    'category_id'  => "required",
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

                $subCategory = new SubCategory();
                $subCategory->name = $request->name;
                $subCategory->category_id = $request->category_id;
                $subCategory->description = $request->description;
                $subCategory->is_active = $request->boolean('is_active');
                $subCategory->photo = $imageName;
                $subCategory->save();

                return $this->apiResponse([], 'Sub Category Created Successfully', true, 200);
            } else {
                //sub category update
                // $validator = Validator::make($request->all(),[
                //     'name' => 'required|max:100',
                //     'description' => 'nullable|max:200',
                //     'is_active'  => "required",
                //     'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable'
                // ]);

                // if ($validator->fails()) {
                //     return $this->apiResponse([], $validator->errors()->first(), false, 403);
                // }

                $subCategory = SubCategory::findOrFail($request->id);
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

                $subCategory->name = $request->name;
                $subCategory->category_id = $request->category_id;
                $subCategory->description = $request->description;
                $subCategory->is_active = $request->boolean('is_active');
                $subCategory->photo = $imageName;
                $subCategory->save();



                return $this->apiResponse([], 'Sub Category Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function subCategorybyCategoryId(Request $request)
    {

        try {

            $subCategory = SubCategory::where('category_id', $request->category_id)->leftJoin("categories","sub_categories.category_id","=","categories.id")->select("sub_categories.*","categories.name as category_name")->get();

            
            return $this->apiResponse($subCategory, 'Sub Category List', true, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }

    }




    public function deleteSubCategory($id)
    {
            try {
                $subCategory = SubCategory::findOrFail($id);
                if ($subCategory->photo) {
                    unlink(public_path("images/" . $subCategory->photo));
                }
                $subCategory->delete();

                return $this->apiResponse([], 'Sub Category Deleted Successfully', true, 200);
            } catch (\Throwable $th) {
                return $this->apiResponse([], $th->getMessage(), false, 500);
            }
     
    }
}
