<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HelperTrait;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemOrderController extends Controller
{

    use ApiResponseTrait;
    use HelperTrait;

    public function itemList()
    {
        $itemOrder = ItemOrder::leftJoin('vendors', 'vendors.id', '=', 'item_orders.vendor_id')
            ->select('item_orders.*', 'vendors.name as vendor_name')
            ->latest()
            ->get();


        foreach ($itemOrder as $key => $value) {
            $itemOrder[$key]->items = ItemOrderDetail::where('item_order_id', $value->id)
                ->select('item_order_details.*', 'items.title as item_name', 'items.photo as item_photo')
                ->leftJoin('items', 'items.id', '=', 'item_order_details.item_id')
                // ->latest();
                ->get();
        }



        return $this->apiResponse($itemOrder, 'Item Order List', true, 200);
    }


    public function itemOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'tentative_date' => 'required',
            'discount' => 'required',
            'total' => 'required',
            'vendor_id' => 'required',
            'note' => 'required',
            'is_active' => 'required',
            'order_items' => 'required|array',
            'order_items.*.item' => 'required',
            'order_items.*.item_qty' => 'required',
            'order_items.*.amount' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse([], $validator->errors()->first(), false, 403);
        }

        try {

            DB::transaction(
                function () use ($request) {
                    $itemOrder = new ItemOrder();
                    $itemOrder->order_no = $this->invoiceGenerator(ItemOrder::class);
                    $itemOrder->amount = $request->amount;
                    $itemOrder->tentative_date = $request->tentative_date;
                    $itemOrder->discount = $request->discount;
                    $itemOrder->total = $request->total;
                    $itemOrder->vendor_id = $request->vendor_id;
                    $itemOrder->qty = $request->qty;
                    $itemOrder->note = $request->note;
                    $itemOrder->is_active = $request->boolean('is_active');
                    $itemOrder->created_by = auth()->user()->id;
                    $itemOrder->save();

                    $item = [];

                    foreach ($request->order_items as  $value) {
                        $item[] = [
                            'item_order_id' => $itemOrder->id,
                            'item_id' => $value['item'],
                            'item_qty' => $value['item_qty'],
                            'item_price' => $value['item_price'],
                            'total_price' => $value['amount'],
                        ];
                    }
                    ItemOrderDetail::insert($item);
                }
            );

            DB::commit();
            return $this->apiResponse([], 'Item Order Created Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }



    public function orderDelete($id)
    {

        try {
            $itemOrder = ItemOrder::find($id);
            if ($itemOrder) {
                $itemOrder->delete();
                return $this->apiResponse([], 'Item Order Deleted Successfully', true, 200);
            } else {
                return $this->apiResponse([], 'Item Order Not Found', false, 404);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
