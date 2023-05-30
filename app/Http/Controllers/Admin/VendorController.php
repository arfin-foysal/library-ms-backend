<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemReceive;
use App\Models\Vendor;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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


    public function vendorPaymentList()
    {

        try {

            $vendorPayment = VendorPayment::leftJoin('vendors', 'vendor_payments.vendor_id', '=', 'vendors.id')
                ->leftjoin('item_receives', 'vendor_payments.item_receive_id', '=', 'item_receives.id')
                ->select(
                    'vendor_payments.*',
                    'vendors.name as vendor_name',
                    'item_receives.invoice_no as invoice_no',
                    'item_receives.payment_status as payment_status',
                )
                ->get();


            return $this->apiResponse($vendorPayment, 'Vendor Payment List', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function vendorPaymentUpdate(Request $request)
    {
        
     
        try {

            DB::beginTransaction();

              $vendorPayment=VendorPayment::findOrFail($request->id);
                $vendorPayment->paid_amount=$request->paid_amount;
                $vendorPayment->due_amount=$request->due_amount;
                $vendorPayment->payment_through=$request->payment_through;
                $vendorPayment->comments=$request->comments;
                $vendorPayment->updated_by=Auth::user()->id;
                $vendorPayment->save();

        

                

                    $payableAmount = $vendorPayment->payable_amount;
                    $dueAmount = $payableAmount - $vendorPayment->paid_amount;
                    
                    $paymentStatus = "";

                    if ($dueAmount == 0) {
                        $paymentStatus = "Paid";
                    } else {
                        $paymentStatus = "Due";
                    }

                    ItemReceive::where('id', $request->item_receive_id)->update([
                        'payment_status' => $paymentStatus,
                        'updated_by' => Auth::user()->id,
                        'paid_amount'=>$vendorPayment->paid_amount,
                        'due_amount'=>$vendorPayment->due_amount,
                        
                    ]);
                

            
            DB::commit();
            return $this->apiResponse($vendorPayment, 'Vendor Payment Updated Successfully', 
            
            
            
            true, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
