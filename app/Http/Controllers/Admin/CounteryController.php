<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CounteryController extends Controller
{
    use ApiResponseTrait;

    public function allCounteryList()
    {

        $countery = Country::all();
        return $this->apiResponse($countery, 'Country List', true, 200);
    }


    public function createOrUpdateCountery(Request $request)
    {
        $authId = Auth::user()->id;
        if (empty($request->id)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'is_active' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->apiResponse([], $validator->errors()->first(), false, 403);
            }

            $countery = new Country();
            $countery->name = $request->name;
            $countery->is_active = $request->boolean('is_active');
            $countery->created_by = $authId;
            $countery->save();

            return $this->apiResponse([], 'Country Created Successfully', true, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'is_active' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse([], $validator->errors()->first(), false, 403);
            }

            $countery = Country::find($request->id);
            $countery->name = $request->name;
            $countery->is_active = $request->boolean('is_active');
            $countery->updated_by = $authId;
            $countery->save();


            return $this->apiResponse([], 'Country Updated Successfully', true, 200);
        }
    }


    public function show($id)
    {
        //
    }



    public function deleteCountery($id)
    {
        $countery = Country::find($id);
        $countery->delete();
        return $this->apiResponse([], 'Country Deleted Successfully', true, 200);
    }
}
