<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemRental;
use App\Models\ItemRentalDetail;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;
    public function dashboardSummery()

    {
        $itemRentList = ItemRental::leftJoin('users', 'users.id', '=', 'item_rentals.user_id')
            ->select('item_rentals.*', 'users.name as user_name', 'users.profile_photo_path as user_photo',)
            ->latest('item_rentals.id')
            ->limit(5)
            ->get();
        // latest 5 data get

        foreach ($itemRentList as $item) {
            $item->item_rents_Detail = ItemRentalDetail::where('item_rental_id', $item->id)

                ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
                ->where('status', 'rental')
                ->select(
                    'item_rental_details.*',
                    'items.title as item_name',
                    'items.photo as item_photo',
                )

                ->get();

            $item->item_rents_Detail_show = ItemRentalDetail::where('item_rental_id', $item->id)

                ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
                // ->where('status', 'rental')
                ->select(
                    'item_rental_details.*',
                    'items.title as item_name',
                    'items.photo as item_photo',
                )
                ->get();
        }


        $itemReturnList = ItemRentalDetail::where('item_rental_details.status', 'return')
            ->orWhere('item_rental_details.status', 'overdue')
            ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
            ->leftJoin('item_rentals', 'item_rentals.id', '=', 'item_rental_details.item_rental_id')
            ->leftJoin('users', 'users.id', '=', 'item_rentals.user_id')
            ->leftJoin('item_returns', 'item_returns.item_rental_id', '=', 'item_rentals.id')
            ->leftJoin('item_return_details', 'item_return_details.item_return_id', '=', 'item_returns.id')
            ->select(

                'item_rentals.rental_no as rental_no',
                'item_rental_details.status as rental_status',
                'item_rental_details.item_amount_of_penalty as item_amount_of_penalty',
                'items.title as item_name',
                'items.photo as item_photo',
                'items.isbn as isbn',
                'item_return_details.return_date as return_date',
                'item_returns.return_no as return_no',
                'item_rentals.rental_date as rental_date',
                'users.name as user_name',
                'users.profile_photo_path as user_photo',
            )
            ->latest('item_rental_details.id')
            ->limit(5)
            ->get();


            $itemCount =Item::count();
            

        $book = ['itemRentList' => $itemRentList, 'itemReturnList' => $itemReturnList,
        'itemCount'=>$itemCount
    ];

        return $this->apiResponse($book, 'success', true, 200);
    }
}
