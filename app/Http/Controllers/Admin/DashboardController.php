<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use App\Models\ItemReceive;
use App\Models\ItemReceiveDetail;
use App\Models\ItemRental;
use App\Models\ItemRentalDetail;
use App\Models\VendorPayment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;
    public function dashboardSummery()

    {

        $itemRentList = ItemRental::leftJoin('users', 'users.id', '=', 'item_rentals.user_id')
            ->select('item_rentals.*')
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
        }


        $itemReturnList = ItemRentalDetail::where('item_rental_details.status', 'return')
            ->orWhere('item_rental_details.status', 'overdue')
            ->leftJoin('items', 'items.id', '=', 'item_rental_details.item_id')
            ->leftJoin('item_rentals', 'item_rentals.id', '=', 'item_rental_details.item_rental_id')
            ->leftJoin('item_returns', 'item_returns.item_rental_id', '=', 'item_rentals.id')
            ->leftJoin('item_return_details', 'item_return_details.item_return_id', '=', 'item_returns.id')
            ->select(

                'item_rentals.rental_no as rental_no',
                'item_rental_details.status as rental_status',
                'item_rental_details.item_amount_of_penalty as item_amount_of_penalty',
                'items.title as item_name',
                'items.isbn as isbn',
                'item_return_details.return_date as return_date',
                'item_returns.return_no as return_no',
                'item_rentals.rental_date as rental_date',

            )
            ->latest('item_rental_details.id')
            ->limit(5)
            ->get();



        $itemOrder = ItemOrder::leftJoin('vendors', 'vendors.id', '=', 'item_orders.vendor_id')
            ->select('item_orders.*', 'vendors.name as vendor_name')
            ->latest()
            ->limit(5)
            ->get();





        $receivedOrderList = ItemReceive::leftJoin('vendors', 'vendors.id', '=', 'item_receives.vendor_id')
            ->select('item_receives.*', 'vendors.name as vendor_name')
            ->latest()
            ->limit(5)
            ->get();





        //Vendor Payment Line Graph
        $vendorPaymentGraph = VendorPayment::select(
            DB::raw("(sum(paid_amount)) as paid_amount"),
            DB::raw("(DATE_FORMAT(created_at, '%M')) as month")
        )
            ->orderBy('created_at')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%M')"))
            ->get();

        //rental book status == rental

        $rentalBookGraph = ItemRental::select(
            DB::raw("(sum(qty)) as rental_book"),
            DB::raw("(DATE_FORMAT(created_at, '%d-%b-%Y')) as day")


        )
            ->where('status', 'active')
            ->orderBy('created_at')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%d-%b-%Y')"))
            ->get();


        $itemCount = Item::count();
        //total rent book this month

        $totalRentBook =  ItemRental::where('status', 'active')->whereMonth('created_at', date('m'))->count();

        //total order item this month

        $totalOrderItem =  ItemOrder::whereMonth('created_at', date('m'))->count();

        $totalVendorPayment =  VendorPayment::whereMonth('created_at', date('m'))->sum('paid_amount');

        


























        $book = [
            'itemRentList' => $itemRentList, 'itemReturnList' => $itemReturnList,
            'itemOrderList' => $itemOrder,
            'orderReceivedList' => $receivedOrderList,
            'vendorPaymentGraph' => $vendorPaymentGraph,
            'rentalBookGraph' => $rentalBookGraph,
            'itemCount' => $itemCount,
            'totalRentBook' => $totalRentBook,
            'totalOrderItem' => $totalOrderItem,
            'totalVendorPayment' => $totalVendorPayment,
        ];


        return $this->apiResponse($book, 'success', true, 200);
    }
}
