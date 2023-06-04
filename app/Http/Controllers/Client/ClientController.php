<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemAuthor;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponseTrait;

    public function getAllBook()
    {
        $items = Item::
            //  leftJoin('authors','authors.id','=','items.author_id')
            leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->leftJoin('publishers', 'publishers.id', '=', 'items.publisher_id')
            ->leftJoin('countries', 'countries.id', '=', 'items.country_id')
            ->leftJoin('languages', 'languages.id', '=', 'items.language_id')
            ->select(
                'items.*',
                'categories.name as category_name',
                'publishers.name as publisher_name',
                'countries.name as country_name',
                'languages.name as language_name',
            )
            ->get();
        foreach ($items as $item) {
            $item->authors = ItemAuthor::where('item_id', $item->id)
                ->select(
                    'authors.name as name',
                    'authors.id as id',
                    'authors.photo as author_photo',
                    'authors.bio as author_bio'

                )
                ->leftJoin('authors', 'authors.id', '=', 'item_authors.author_id')->get();
        }

        return $this->apiResponse($items, 'all Book item', true, 200);
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
