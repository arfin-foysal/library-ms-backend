<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    use ApiResponseTrait;
    public function allLanguage()
    {
        $language=Language::all();
        return $this->apiResponse($language,'Language List',true,200);
    }


    public function createOrUpdateLanguage(Request $request)
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

            $language = new Language();
            $language->name = $request->name;
            $language->is_active = $request->boolean('is_active');
            $language->created_by = $authId;
            $language->save();

            return $this->apiResponse([], 'Language Created Successfully', true, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'is_active' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->apiResponse([], $validator->errors()->first(), false, 403);
            }

            $language = Language::find($request->id);
            $language->name = $request->name;
            $language->is_active = $request->boolean('is_active');
            $language->updated_by = $authId;
            $language->save();


            return $this->apiResponse([], 'Language Updated Successfully', true, 200);
        }
    }

    public function show($id)
    {
        //
    }


    public function deleteLanguage($id)
    {
        $language = Language::find($id);
        $language->delete();
        return $this->apiResponse([], 'Language Deleted Successfully', true, 200);
    }
}
