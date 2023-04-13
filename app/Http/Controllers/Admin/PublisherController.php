<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    use ApiResponseTrait;
    public function allPublisharList()
    {
        $publishar = Publisher::all();
        return $this->apiResponse($publishar, 'Publisher List', true, 200);
    
    }


    public function createOrUpdatePublishar(Request $request)
    {

        $authid = Auth::user()->id;
        try {
            if (empty($request->id)) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact'  => "nullable|max:50",
                    'address1'  => "nullable|max:800",
                    'address2'  => "nullable|max:800",
                    'establish'  => "nullable|max:15",
                    'is_active'  => "required",
                    'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 422);
                }

                $filename = "";
                if ($image = $request->file('photo')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }

                $publisher = new Publisher();
                $publisher->name = $request->name;
                $publisher->email = $request->email;
                $publisher->contact = $request->contact;
                $publisher->mobile = $request->mobile;
                $publisher->address1 = $request->address1;
                $publisher->address2 = $request->address2;
                $publisher->establish = $request->establish;
                $publisher->is_active = $request->boolean('is_active');
                $publisher->bio = $request->bio;
                $publisher->photo = $filename;
                $publisher->created_by = $authid;
                $publisher->save();

                return $this->apiResponse([], 'Publisher Created Successfully', true, 200);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email'  => "nullable|max:80",
                    'mobile'  => "nullable:max:15",
                    'contact'  => "nullable|max:50",
                    'address1'  => "nullable|max:800",
                    'address2'  => "nullable|max:800",
                    'establish'  => "nullable|max:15",
                    'is_active'  => "required",
        
                ]);
                if ($validator->fails()) {
                    return $this->apiResponse([], $validator->errors()->first(), false, 422);
                }

                $publisher = Publisher::findOrFail($request->id);
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

                Publisher::where('id', $request->id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact' => $request->contact,
                    'mobile' => $request->mobile,
                    'address1' => $request->address1,
                    'address2' => $request->address2,
                    'establish' => $request->establish,
                    'is_active' => $request->boolean('is_active'),
                    'bio' => $request->bio,
                    'photo' => $imageName,
                    'updated_by' => $authid,
                ]);

                return $this->apiResponse([], 'Publisher Updated Successfully', true, 200);
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




    public function deletePublishar($id)
    {
       
        try {
            $publishar = Publisher::findOrFail($id);
            if ($publishar->photo) {
                unlink(public_path("images/" . $publishar->photo));
            }
            $publishar->delete();

            return $this->apiResponse([], 'Publisher Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
