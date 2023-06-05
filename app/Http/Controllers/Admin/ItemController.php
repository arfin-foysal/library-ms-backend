<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class ItemController extends Controller
{
    use ApiResponseTrait;
    public function allItemList()
    {
        $items = item::leftJoin('publishers', 'items.publisher_id', '=', 'publishers.id')
            ->leftJoin('languages', 'items.language_id', '=', 'languages.id')
            ->leftJoin('countries', 'items.country_id', '=', 'countries.id')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->leftJoin('sub_categories', 'items.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('third_sub_categories', 'items.third_category_id', '=', 'third_sub_categories.id')
            ->select(
                'items.*',
                'publishers.name as publisherName',
                'languages.name as languageName',
                'countries.name as countryName',
                'categories.name as categoryName',
                'sub_categories.name as subCategoryName',
                'third_sub_categories.name as thirdCategoryName',
            )
            ->get();

        foreach ($items as $item) {
            $item->authors = ItemAuthor::where('item_id', $item->id)
                ->select('authors.name as name', 'authors.id as id')
                ->leftJoin('authors', 'authors.id', '=', 'item_authors.author_id')->get();
        }


        return $this->apiResponse($items, 'Item List', true, 200);
    }


    public function createOrUpdateItem(Request $request)
    {

        // return $this->apiResponse($request->all(), 'Item Created Successfully', true, 200);


        if (empty($request->id)) {

            $validator = Validator::make($request->all(), [
                'title' => 'required|max:200|unique:items,title,NULL,id,deleted_at,NULL',
                'isbn'  => "nullable|max:100",
                'photo'  => "nullable|mimes:jpeg,jpg,png|max:10000",
                'edition'  => "nullable:max:100",
                'number_of_page'  => "nullable|max:50",
                'summary'  => "nullable|max:255",
                'video_url'  => "nullable|max:255",
                'brochure'  => "nullable|mimes:pdf|max:10000",
                'publisher_id'  => "nullable|exists:publishers,id",
                'language_id'  => "nullable|exists:languages,id",
                'country_id'  => "nullable|exists:countries,id",
                'category_id'  => "nullable|exists:categories,id",
                'sub_category_id'  => "nullable|exists:sub_categories,id",
                'third_category_id'  => "nullable|exists:third_sub_categories,id",
                'item_type'  => "required|in:physical,virtual",
                'is_active'  => "required",
                'is_free'  => "required",


            ]);

            if ($validator->fails()) {
                return $this->apiResponse([], $validator->errors()->first(), false, 409);
            }
            $filename = "";
            if ($image = $request->file('photo')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }

            $brochure = "";
            if ($image = $request->file('brochure')) {
                $brochure = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file/'), $brochure);
            } else {
                $brochure = Null;
            }
            try {

                DB::transaction(function () use ($request, $filename, $brochure) {
                    $item = new Item();
                    $item->title = $request->title;
                    $item->isbn = $request->isbn;
                    $item->photo = $filename;
                    $item->edition = $request->edition;
                    $item->number_of_page = $request->number_of_page;
                    $item->summary = $request->summary;
                    $item->video_url = $request->video_url;
                    $item->brochure = $brochure;
                    $item->publisher_id = $request->publisher_id;
                    $item->language_id = $request->language_id;
                    $item->country_id = $request->country_id;
                    $item->category_id = $request->category_id;
                    $item->sub_category_id = $request->sub_category_id;
                    $item->third_category_id = $request->third_category_id;
                    $item->publish_status = $request->publish_status;
                    $item->created_by = auth()->user()->id;
                    $item->is_active = $request->boolean('is_active');
                    $item->is_show = $request->boolean('is_show');
                    $item->item_type = $request->item_type;
                    $item->is_free = $request->is_free;
                    $item->publish_date = $request->publish_date;
                    $item->save();

                    $authorArr = json_decode($request->author_id);


                    foreach ($authorArr as $key => $value) {
                        $itemAuthor = new ItemAuthor();
                        $itemAuthor->item_id = $item->id;
                        $itemAuthor->author_id = $value;
                        $itemAuthor->save();
                    }
                });

                DB::commit();
                return $this->apiResponse($request->all(), 'Item Created Successfully', true, 200);
            } catch (\Throwable $th) {
                DB::rollback();

                return $this->apiResponse([], $th->getMessage(), false, 500);
            }
        } else {

            // ItemAuthor::where('item_id',$itemId)->delete();
            // $validator = Validator::make($request->all(), [
            //     'title' => 'required|max:200|unique:items,title,' . $request->id . ',id,deleted_at,NULL',
            //     'isbn'  => "nullable|max:100",
            //     // 'photo'  => "nullable|mimes:jpeg,jpg,png|max:10000",
            //     'edition'  => "nullable:max:100",
            //     'number_of_page'  => "nullable|max:50",
            //     'summary'  => "nullable|max:255",
            //     'video_url'  => "nullable|max:255",
            //     // 'brochure'  => "nullable|mimes:pdf|max:10000",
            //     'publisher_id'  => "nullable|exists:publishers,id",
            //     'language_id'  => "nullable|exists:languages,id",
            //     'country_id'  => "nullable|exists:countries,id",
            //     'category_id'  => "nullable|exists:categories,id",
            //     'sub_category_id'  => "nullable|exists:sub_categories,id",
            //     'third_category_id'  => "nullable|exists:third_sub_categories,id",
            //     "author_id"   => "required|min:1",
            //     'author_id.*' => "exists:authors,id",
            //     'is_active'  => "required",
            //     // 'sequence'  => "required",

            // ]);
            // if ($validator->fails()) {
            //     return $this->apiResponse([], $validator->errors()->first(), false, 409);
            // }

            $item = Item::findOrFail($request->id);

            $imageName = "";
            if ($image = $request->file('photo')) {
                if ($item->photo) {
                    unlink(public_path("images/" . $item->photo));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $item->photo;
            }



            $brochureFile = "";
            if ($image = $request->file('brochure')) {
                if ($item->brochure) {
                    unlink(public_path("file/" . $item->brochure));
                }

                $brochureFile = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file/'), $brochureFile);
            } else {
                $brochureFile = $item->brochure;
            }


            try {

                DB::transaction(function () use ($request, $item, $brochureFile, $imageName) {
                    $item->title = $request->title;
                    $item->isbn = $request->isbn;
                    $item->photo = $imageName;
                    $item->edition = $request->edition;
                    $item->number_of_page = $request->number_of_page;
                    $item->summary = $request->summary;
                    $item->video_url = $request->video_url;
                    $item->publisher_id = $request->publisher_id;
                    $item->language_id = $request->language_id;
                    $item->country_id = $request->country_id;
                    // $item->category_id = $request->category_id;
                    // $item->sub_category_id = $request->sub_category_id;
                    // $item->third_category_id = $request->third_category_id;
                    $item->publish_status = $request->publish_status;
                    $item->updated_by = auth()->user()->id;
                    $item->brochure = $brochureFile;
                    $item->is_active = $request->boolean('is_active');
                    $item->is_show = $request->boolean('is_show');
                    $item->item_type = $request->item_type;
                    $item->is_free = $request->is_free;
                    $item->publish_date = $request->publish_date;
                    $item->save();

                    ItemAuthor::where('item_id', $item->id)->delete();

                    $authorArr = json_decode($request->author_id);

                    foreach ($authorArr as $key => $value) {
                        $itemAuthor = new ItemAuthor();
                        $itemAuthor->item_id = $item->id;
                        $itemAuthor->author_id = $value;
                        $itemAuthor->save();
                    }
                });

                DB::commit();
                return $this->apiResponse($request->all(), 'Item Updated Successfully', true, 200);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollback();
                return $this->apiResponse([], $th->getMessage(), false, 500);
            }
        }
    }

    public function show($id)
    {
        //
    }





    public function deleteItem($id)
    {

        try {
            DB::transaction(function () use ($id) {
                $item = item::findOrFail($id);
                if ($item->photo) {
                    unlink(public_path("images/" . $item->photo));
                }
                if ($item->brochure) {
                    unlink(public_path("file/" . $item->brochure));
                }
                $item->delete();

                ItemAuthor::where('item_id', $item->id)->delete();
            });
            DB::commit();

            return $this->apiResponse([], 'item Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
