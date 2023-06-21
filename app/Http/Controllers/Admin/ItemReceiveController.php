<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HelperTrait;
use App\Models\Item;
use App\Models\ItemInventoryStock;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use App\Models\ItemReceive;
use App\Models\ItemReceiveDetail;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemReceiveController extends Controller

{
    use ApiResponseTrait;
    use HelperTrait;

    public function recevedOrderList()
    {
        $recevedOrderList = ItemReceive::leftJoin('vendors', 'vendors.id', '=', 'item_receives.vendor_id')
            ->select('item_receives.*', 'vendors.name as vendor_name')
            ->latest('item_receives.id')
            ->get();
        

        foreach ($recevedOrderList as $key => $value) {
            $recevedOrderList[$key]->items = ItemReceiveDetail::where('item_receive_id', $value->id)
                ->select('item_receive_details.*', 'items.title as item_name', 'items.photo as item_photo')
                ->leftJoin('items', 'items.id', '=', 'item_receive_details.item_id')
                ->get();
        }
        return $this->apiResponse($recevedOrderList, 'Item Receive List', true, 200);
    }

   




    public function unRecevedItemByOrderId(Request $request, $id)
    {

        $itemReceive = ItemOrder::where('item_orders.id', $id)
            ->leftJoin('vendors', 'vendors.id', '=', 'item_orders.vendor_id')
            ->select('item_orders.*', 'vendors.name as vendor_name')
            ->first();
        $itemReceive->items = ItemOrderDetail::where('item_order_id', $id)
            ->select('item_order_details.*', 'items.title as item_name', 'items.photo as item_photo')
            ->leftJoin('items', 'items.id', '=', 'item_order_details.item_id')
            ->get();

        return $this->apiResponse($itemReceive, 'Item Order List', true, 200);
    }


    public function itemOrderReceve(Request $request)
    {
       

        try {
            DB::transaction(
                function () use ($request) {
                    $itemReceive = new ItemReceive();
                    $itemReceive->receive_no = $this->invoiceGenerator(ItemReceive::class);;
                    $itemReceive->item_order_id = $request->item_order_id;
                    $itemReceive->vendor_id = $request->vendor_id;
                    $itemReceive->qty = $request->qty;
                    $itemReceive->invoice_no = $request->invoice_no;
                    $itemReceive->received_date = $request->received_date;
                    $itemReceive->comments = $request->comments;
                    $itemReceive->created_by = Auth::user()->id;
                    $itemReceive->payable_amount = $request->total;
                    $itemReceive->save();

                    // ------------------ Item Receive Detail ------------------ //                 
                    $item = [];
                    foreach ($request->order_items as  $value) {
                        $item[] = [
                            'item_receive_id' => $itemReceive->id,
                            'item_id' => $value['item_id'],
                            'item_qty' => $value['item_qty'],
                            'item_price' => $value['item_price'],
                            'total_price' => $value['total_price'],
                        ];

                        $itemInventoryStock = ItemInventoryStock::where(['item_id' => $value['item_id']])->first();
                        $qty = $value['item_qty'] ? $value['item_qty'] : 0;
                        // Update Item Qty with previous qty -------------------
                        if ($itemInventoryStock) {
                            $qty += $itemInventoryStock->qty;
                            $itemInventoryStock->update(
                                [
                                    'qty' => $qty,
                                    'updated_by' => Auth::user()->id,

                                ]

                            );
                            //$request->item_qty[$key]
                        } else {
                            // Create new inventoryStock -------
                            ItemInventoryStock::create(
                                [
                                    'item_id' => $value['item_id'],
                                    'qty' => $qty,
                                    'created_by' => Auth::user()->id,
                                ],
                            );
                        }

                        // item price 
                    }
                    itemReceiveDetail::insert($item);


                    // item price update

                    foreach ($request->order_items as $key => $value) {
                        $item = Item::find($value['item_id']);
                        $item->price = $value['item_price'];
                        $item->save();
                    }





                    // ------------------ Item Inventory Stock ------------------ //

                    // foreach ($request->order_items as  $value) {
                    //     $itemInventoryStock = ItemInventoryStock::where(['item_id' => $value['item_id']])->first();

                    //     $qty = $value['item_qty'] ? $value['item_qty'] : 0;
                    //     // Update Item Qty with previous qty -------------------
                    //     if ($itemInventoryStock) {
                    //         $qty += $itemInventoryStock->qty;
                    //         $itemInventoryStock->update(
                    //             [
                    //                 'qty' => $qty,
                    //                 'updated_by' => Auth::user()->id,

                    //             ]

                    //         );
                    //         //$request->item_qty[$key]
                    //     } else {
                    //         // Create new inventoryStock -------
                    //         ItemInventoryStock::create(
                    //             [
                    //                 'item_id' => $value['item_id'],
                    //                 'qty' => $qty,
                    //                 'created_by' => Auth::user()->id,
                    //             ],
                    //         );
                    //     }
                    // }


                    // ------------------ Item Order Receved ------------------ //
                    $itemOrder = ItemOrder::find($request->item_order_id);
                    $itemOrder->order_status = "received";
                    $itemOrder->save();
                    // ------------------ Vendor Payment ------------------ //

                    $vandorePayment = new vendorPayment();
                    $vandorePayment->vendor_payment_no = $this->invoiceGenerator(vendorPayment::class);
                    $vandorePayment->vendor_id = $request->vendor_id;
                    $vandorePayment->item_receive_id = $itemReceive->id;
                    $vandorePayment->payable_amount = $request->total;
                    $vandorePayment->created_by = Auth::user()->id;
                    $vandorePayment->save();
                }




            );
            DB::commit();
            return $this->apiResponse([], 'Item Receive Created', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiResponse([], $th->getMessage(), false, 403);
        }
    }
}
