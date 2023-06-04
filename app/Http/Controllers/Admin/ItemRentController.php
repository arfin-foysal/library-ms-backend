<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HelperTrait;
use App\Models\Item;
use App\Models\ItemInventoryStock;
use App\Models\ItemRental;
use App\Models\ItemRentalDetail;
use App\Models\ItemReturn;
use App\Models\ItemReturnDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ItemRentController extends Controller

{
    use ApiResponseTrait;
    use HelperTrait;

    public function itemAndAvailableQty()
    {
        $itemAndAvailableQty =
            Item::leftJoin('item_inventory_stocks', 'item_inventory_stocks.item_id', '=', 'items.id')
            ->leftJoin('languages', 'languages.id', '=', 'items.language_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->leftJoin('publishers', 'publishers.id', '=', 'items.publisher_id')
            ->leftJoin('countries', 'countries.id', '=', 'items.country_id')
            ->select(
                'items.*',
                'item_inventory_stocks.qty',
                'languages.name as language_name',
                'categories.name as category_name',
                'publishers.name as publisher_name',
                'countries.name as country_name'

            )

            ->get();
        return $this->apiResponse($itemAndAvailableQty, 'Available Quantity', true, 200);
    }


    public function itemRentCreate(Request $request)
    {


        DB::beginTransaction();
        try {

            $itemRentel = new ItemRental();
            $itemRentel->rental_no = $this->invoiceGenerator(ItemRental::class);
            $itemRentel->rental_date = Carbon::now();
            $itemRentel->return_date = $request->return_date;
            $itemRentel->qty = $request->qty;
            $itemRentel->user_id = $request->user_id;
            $itemRentel->note = $request->note;
            $itemRentel->created_by = Auth::user()->id;
            $itemRentel->save();

            // ------------------- Item Rental Detail --------------------//

            $item = [];
            foreach ($request->items as  $value) {
                $item[] = [
                    'item_rental_id' => $itemRentel->id,
                    'item_id' => $value['item_id'],
                    'item_qty' => $value['item_qty'],
                    'return_date' => $value['return_date'],
                ];

                // ------------------ Item Inventory Stock ------------------ //


                $itemInventoryStock = ItemInventoryStock::where('item_id', $value['item_id'])->first();

                //stack qty not less than 0
                if ($itemInventoryStock->qty < $value['item_qty']) {
                    DB::rollback();
                    return $this->apiResponse([], 'Item Quantity Not Available', false, 500);
                } else {
                    $itemInventoryStock->qty = $itemInventoryStock->qty - $value['item_qty'];
                    $itemInventoryStock->save();
                }
            }

            ItemRentalDetail::insert($item);
            DB::commit();
            return $this->apiResponse([], 'Item Rental Created Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function itemRenstList()
    {
        $itemRentelList = ItemRental::leftJoin('users', 'users.id', '=', 'item_rentals.user_id')
            ->select('item_rentals.*', 'users.name as user_name', 'users.profile_photo_path as user_photo',)
            ->get();

        foreach ($itemRentelList as $item) {
            $item->item_rents_Detail = ItemRentalDetail::where('item_rental_id', $item->id)
                ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
                ->where('status', 'rental')
                ->select(
                    'item_rental_details.*',
                    'items.title as item_name',
                    'items.photo as item_photo',
                )
                ->get();
        }
        foreach ($itemRentelList as $item) {
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

        return $this->apiResponse($itemRentelList, 'Item Rental List', true, 200);
    }




    public function returnItem(Request $request)
    {

        // return $request;

        DB::beginTransaction();
        try {

            $itemReturn = new ItemReturn();
            $itemReturn->return_no =  $this->invoiceGenerator(ItemReturn::class);
            $itemReturn->item_rental_id = $request->item_rental_id;
            $itemReturn->qty = $request->qty;
            $itemReturn->return_date = Carbon::now();
            $itemReturn->comments = $request->comments;
            $itemReturn->created_by = Auth::user()->id;
            $itemReturn->save();

            $itemRent = ItemRental::where('id', $request->item_rental_id)->first();
            $itemRent->amount_of_penalty += $request->amount_of_penalty;
            $itemRent->payment_status = 'paid';
            $itemRent->save();

            foreach ($request->return_item as $value) {
                $itemReturnDetail = new ItemReturnDetail();
                $itemReturnDetail->item_return_id = $itemReturn->id;
                $itemReturnDetail->item_id = $value['item_id'];
                $itemReturnDetail->comments = $request->comments;
                $itemReturnDetail->item_qty = $value['item_qty'];
                $itemReturnDetail->return_date = Carbon::now();
                $itemReturnDetail->save();


                //if status is not damaged then update item inventory stock


                if ($value['status'] !== 'damaged') {
                    $itemInventoryStock = ItemInventoryStock::where('item_id', $value['item_id'])->first();
                    $itemInventoryStock->qty = $itemInventoryStock->qty + $value['item_qty'];
                    $itemInventoryStock->save();
                }




                $itemRentelDetail = ItemRentalDetail::where('item_rental_id', $request->item_rental_id)
                    ->where('item_id',  $value['item_id'])
                    ->first();

                $itemRentelDetail->status = $value['status'];
                $itemRentelDetail->item_amount_of_penalty = $value['item_amount_of_penalty'];
                if ($value['item_amount_of_penalty'] == 0) {
                    $itemRentelDetail->item_payment_status = 'nonamount';
                } else {
                    $itemRentelDetail->item_payment_status = 'paid';
                }

                $itemRentelDetail->save();
            }

            DB::commit();

            return $this->apiResponse([], 'Item Rental Return Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function bookRentActive(Request $request, $id)
    {
        $itemRentel = ItemRental::find($id);
        $itemRentel->status = 'active';
        $itemRentel->save();

        return $this->apiResponse([], 'Item Rental Active Successfully', true, 200);
    }


    public function deleteRentsItem($id)
    {
        DB::beginTransaction();
        try {
            $itemRentel = ItemRental::find($id);
            $itemRentel->delete();

            $itemRentelDetail = ItemRentalDetail::where('item_rental_id', $id)->get();
            foreach ($itemRentelDetail as $value) {
                $itemInventoryStock = ItemInventoryStock::where('item_id', $value->item_id)->first();
                $itemInventoryStock->qty = $itemInventoryStock->qty + $value->item_qty;
                $itemInventoryStock->save();
            }
            ItemRentalDetail::where('item_rental_id', $id)->delete();
            DB::commit();
            return $this->apiResponse([], 'Item Rental Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function dateExpiredItem()
    {

        try {
            $itemRentelDetail = ItemRentalDetail::where([['item_rental_details.return_date', '<', Carbon::now()], ['item_rental_details.status', 'rental']])
                ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
                ->leftJoin('item_rentals', 'item_rentals.id', '=', 'item_rental_details.item_rental_id')
                ->leftJoin('users', 'users.id', '=', 'item_rentals.user_id')


                ->select(
                    'item_rental_details.*',
                    'items.title as item_name',
                    'items.photo as item_photo',
                    'item_rentals.rental_no as rental_no',
                    'item_rentals.status as rental_status',
                    'item_rentals.user_id as user_id',
                    'users.name as user_name',
                    'users.profile_photo_path as user_photo',


                )->get();




        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
        return $itemRentelDetail;
    }



    public function damagedItemList(){

        try {
            $itemDamagedList = ItemRentalDetail::where([['item_rental_details.status', 'damaged']])
                ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
                ->leftJoin('item_rentals', 'item_rentals.id', '=', 'item_rental_details.item_rental_id')
                ->leftJoin('users', 'users.id', '=', 'item_rentals.user_id')
                ->leftJoin('item_returns', 'item_returns.item_rental_id', '=', 'item_rentals.id')
                ->leftJoin('item_return_details', 'item_return_details.item_return_id', '=', 'item_returns.id')
                ->select(
                    'item_rental_details.*',
                    'items.title as item_name',
                    'items.photo as item_photo',
                    'item_rentals.rental_no as rental_no',
                    'item_rentals.rental_date as rental_date',
                    'item_rentals.status as rental_status',
                    'item_rentals.user_id as user_id',
                    'users.name as user_name',
                    'users.profile_photo_path as user_photo',
                )
                 ->get();

                return $this ->apiResponse($itemDamagedList, 'Item Damaged List', true, 200);
                
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
       
        }

    }


}
