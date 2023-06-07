<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Author;
use App\Models\Item;
use App\Models\ItemAuthor;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponseTrait;

    public function getAllBook()
    {
        $items = Item::leftJoin('categories', 'categories.id', '=', 'items.category_id')

            ->select(
                'items.*',
                'categories.name as category_name',

            )
            ->get();
        foreach ($items as $item) {
            $item->authors = ItemAuthor::where('item_id', $item->id)
                ->leftJoin('authors', 'authors.id', '=', 'item_authors.author_id')
                ->select(
                    'authors.name as name',
                    'authors.id as id',
                    'authors.photo as author_photo',
                    'authors.bio as author_bio'

                )
                ->get();
        }

        return $this->apiResponse($items, 'all Book item', true, 200);
    }


    public function getItemById($id)
    {

        $items = Item::where('items.id', $id)
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
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

            ->first();
        $items->authors = ItemAuthor::where('item_id', $items->id)
            ->leftJoin('authors', 'authors.id', '=', 'item_authors.author_id')
            ->select(
                'authors.name as name',
                'authors.id as id',
                'authors.photo as author_photo',
                'authors.bio as author_bio'

            )
            ->get();


        $itemByCategory = Item::where('items.category_id', $items->category_id)
            ->where('items.id', '!=', $items->id)
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->select(
                'items.*',
                'categories.name as category_name',

            )->limit(2)->get();

        foreach ($itemByCategory as $item) {
            $item->authors = ItemAuthor::where('item_id', $item->id)
                ->leftJoin('authors', 'authors.id', '=', 'item_authors.author_id')
                ->select(
                    'authors.name as name',
                    'authors.id as id',
                    'authors.photo as author_photo',
                    'authors.bio as author_bio'

                )
                ->get();
        }
        $items->related_items = $itemByCategory;





        return $this->apiResponse($items, 'Book item', true, 200);
    }





    public function authorDetailsAndBook()
    {

        $authors = Author::get();

        $authors->each(function ($author) {
            $author->items = ItemAuthor::where('author_id', $author->id)
                ->leftJoin('items', 'items.id', '=', 'item_authors.item_id')
                ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
                ->select(
                    'items.title as title',
                    'item_authors.item_id as item_id',
                    'items.photo as photo',
                    'categories.name as category_name',
                )
                ->get();
        });

        return $this->apiResponse($authors, 'all author', true, 200);
    }





    public function destroy($id)
    {
        //
    }
}
