<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    use ApiResponseTrait;

    public function allVendorList()
    {

        try {

            $vendor = Vendor::select('id', 'name', 'email', 'mobile', 'contact_person', 'contact_person_mobile', 'office_address', 'warehouse_address', 'primary_supply_products', 'is_active', 'photo')->get();
            return $this->apiResponse($vendor, 'Vendor List', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function createOrUpdateVendor(Request $request)
    {
        try {
            $authId = Auth::user()->id;

            if (empty($request->id)) {
                //vendor create

                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact_person'  => "nullable|max:100",
                    'contact_person_mobile'  => "nullable|max:30",
                    'office_address'  => "nullable|max:800",
                    'warehouse_address'  => "nullable|max:800",
                    'primary_supply_products'  => "nullable|max:15",
                    'is_active'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'

                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 409);
                }



                $imageName = "";
                if ($image = $request->file('photo')) {
                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = Null;
                }

                $vendor = new Vendor();
                $vendor->name = $request->name;
                $vendor->email = $request->email;
                $vendor->mobile = $request->mobile;
                $vendor->contact_person = $request->contact_person;
                $vendor->contact_person_mobile = $request->contact_person_mobile;
                $vendor->office_address = $request->office_address;
                $vendor->warehouse_address = $request->warehouse_address;
                $vendor->primary_supply_products = $request->primary_supply_products;
                $vendor->is_active = $request->boolean('is_active');
                $vendor->created_by = $authId;
                $vendor->photo = $imageName;
                $vendor->save();

                return $this->apiResponse([], 'Vendor Created Successfully', true, 200);
            } else {
                //vendor update
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact_person'  => "nullable|max:100",
                    'contact_person_mobile'  => "nullable|max:30",
                    'office_address'  => "nullable|max:800",
                    'warehouse_address'  => "nullable|max:800",
                    'primary_supply_products'  => "nullable|max:15",
                    'is_active'  => "required",

                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 409);
                }


                $publisher = Vendor::findOrFail($request->id);
                $imageName = "";
                if ($image = $request->file('photo')) {
                    if ($publisher->photo) {
                        unlink(public_path("images/" . $publisher->photo));
                    }

                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $publisher->photo;
                }

                Vendor::where('id', $request->id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'contact_person' => $request->contact_person,
                    'contact_person_mobile' => $request->contact_person_mobile,
                    'office_address' => $request->office_address,
                    'warehouse_address' => $request->warehouse_address,
                    'primary_supply_products' => $request->primary_supply_products,
                    'is_active' => $request->boolean('is_active'),
                    'photo' => $imageName,
                    'updated_by' => $authId,
                ]);

                return $this->apiResponse([], 'Vendor Updated Successfully', true, 200);
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

    public function deleteVendor($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            if ($vendor->photo) {
                unlink(public_path("images/" . $vendor->photo));
            }
            $vendor->delete();

            return $this->apiResponse([], 'Vendor Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
