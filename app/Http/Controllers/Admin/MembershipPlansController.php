<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MembershipPlansController extends Controller
{
    use ApiResponseTrait;
    public function allMembershipList()
    {
        
        $membership = MembershipPlan::all();
        return $this->apiResponse($membership, 'Membership List', true, 200);   
    }


    public function createOrUpdateMembership(Request $request)
    {
        $authid = Auth::user()->id;
        try {
            if (empty($request->id)) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:membership_plans,name,NULL,id,deleted_at,NULL|max:200',
                    'description'  => "nullable",
                    'term_policy'  => "nullable",
                    'fee_amount'  => "required|digits_between:1,9",
                    'valid_duration'  => "required|digits_between:0,3",
                    'status'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
                ]);
                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 409);
                }

                $filename = "";
                if ($image = $request->file('photo')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/'), $filename);
                } else {
                    $filename = Null;
                }

                $membership = new MembershipPlan();
                $membership->name = $request->name;
                $membership->description = $request->description;
                $membership->term_policy = $request->term_policy;
                $membership->fee_amount = $request->fee_amount;
                $membership->valid_duration = $request->valid_duration;
                $membership->status = $request->status;
                $membership->photo = $filename;
                $membership->created_by = $authid;
                $membership->save();

                return $this->apiResponse([], 'Membership Plan Created Successfully', true, 200);
            } else {

                
                $membership = MembershipPlan::findOrFail($request->id);
                $imageName = "";
                if ($image = $request->file('photo')) {
                    if ($membership->photo) {
                        unlink(public_path("images/" . $membership->photo));
                    }

                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $membership->photo;
                }

                MembershipPlan::where('id', $request->id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'term_policy' => $request->term_policy,
                    'fee_amount' => $request->fee_amount,
                    'valid_duration' => $request->valid_duration,
                    'status' => $request->status,
                    'photo' => $imageName,
                    'updated_by' => $authid,
                ]);
                
                return $this->apiResponse([], 'Membership Plan Updated Successfully', true, 200);


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




    public function deleteMembership($id)
    {
        try {
            $membership = MembershipPlan::findOrFail($id);
            if ($membership->photo) {
                unlink(public_path("images/" . $membership->photo));
            }
            $membership->delete();

            return $this->apiResponse([], 'Membership Plan Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
