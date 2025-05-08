<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Order;
use App\Models\OrderAdditional;
use App\Models\OrderDetail;
use App\Models\OrderVariant;
use App\Models\Product;
use DBConstanst;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Throw_;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        $request->validate([
            'data' => 'required|array|min:1',
            'data.*.product' => 'required|array',
            'data.*.product.id' => 'required|integer',
            'data.*.qty' => 'required|integer',
            'data.*.discount_price' => 'nullable|numeric',
            'data.*.discount_percentage' => 'nullable|numeric|between:0,100',

            'data.*.variants' => 'nullable|array',
            'data.*.variants.*.panjang' => 'required|numeric|in:6,12,18,24,30',
            'data.*.variants.*.jumlah' => 'required|integer|min:1',
            'data.*.unit_price' => 'required|numeric',
            
            //'customer' => 'nullable|integer|exists:customer',

            'discount' => 'required|numeric',
            'discount_percentage' => 'required|numeric|between:0,100',

            'additional' => 'nullable|array',
            'additional.*.name' => 'nullable|string|max:255',
            'additional.*.total' => 'nullable|numeric',
        ]);
        //dd($request->all());
        DB::beginTransaction();
        try {
            $product_ids = collect($request->input('data'))
                            ->pluck('product.id')
                            ->filter()
                            ->all();

            $products = Product::query()
                            ->whereIn('id', $product_ids)->get()
                            ->keyBy('id')
                            ->toArray();

            $totalAdditional = 0;
            foreach($request->additional as $additional){
                $totalAdditional += (int) str_replace('.', '', $additional['total']);
            }

            $totalItemPriceBeforeDisc = 0;
            foreach($request->data as $produx){
                $totalItemPriceBeforeDisc += (int)str_replace('.','', $produx['unit_price']) * ((int) str_replace('.','', $produx['qty'])/6);
            }

            //create new temporary order
            $order = new Order();
            $order->customer_id = $request->input('customer.id', 0);
            $order->discount = $request->input('discount', 0);
            $order->discount_percentage = $request->input('discount_percentage', 0);;
            $order->total_additional_price = $totalAdditional;
            $order->total_price_before_disc = $totalItemPriceBeforeDisc; //harga sebelum diskon global
            $order->final_price = $totalItemPriceBeforeDisc + $totalAdditional - (int)str_replace('.','', $request->discount); //harga setelah diskon global
            
            $order->approval_id = 0;
            $order->save();

            $orderId = $order->id;

            //$totalPrice = 0;

            $approval = false;
            foreach($request->input('data') as $item) {

                $productOrigin = $products[$item['product']['id']] ?? null;
                if(!$productOrigin){
                    DB::rollBack();
                    return response()->json(['message' => 'product is not valid']);
                }

                if($productOrigin['lowest_price'] > $item['unit_price']){
                    $approval = true;
                }

                //order detail
                $orderDetail = new OrderDetail();
                $orderDetail->product_id            = $item['product']['id'];
                $orderDetail->unit_lower_price      = $productOrigin['lowest_price'];
                $orderDetail->unit_higher_price     = $productOrigin['higher_price'];
                $orderDetail->product_name          = $item['product']['design_name'];
                $orderDetail->discount              = $item['discount_price'];
                $orderDetail->discount_percentage   = $item['discount_percentage'];
                $orderDetail->unit_selling_price    = $item['unit_price'];
                $orderDetail->qty                   = $item['qty'];
                $orderDetail->order_id              = $orderId;
                $orderDetail->selling_total_price   = ($item['qty'] / 6) * $item['unit_price'] - $item['discount_price'];
                $orderDetail->save();
                
                //TODO: update stock
                //$totalPrice += ($item['qty'] / 6) * $item['unit_price'] - $item['discount_price'];

                foreach($item['variants'] as $variant) {
                    $orderVarian = new OrderVariant();
                    $orderVarian->order_detail_id = $orderDetail->id;
                    $orderVarian->panjang = $variant['panjang'];
                    $orderVarian->jumlah = $variant['jumlah'];
                    $orderVarian->save();
                }
            }    
            
            if($approval) {
                $approval = new Approval();
                $approval->order_id = $orderId;
                $approval->requestor = auth()->user()->id;

                $approval->save();
            }

            foreach($request->input('additional') as $additional) {
                $orderAdditional = new OrderAdditional();
                $orderAdditional->order_id = $orderId;
                $orderAdditional->detail = $additional['name'];
                $orderAdditional->price = $additional['total'];
                $orderAdditional->save();
            }
            

            DB::commit();
            return response()->json([
                'message' => 'success',
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order: ' . $e->getMessage());
            return redirect()->route('penjualan.kasir')->with('error', 'Transaksi gagal ditambahkan.');
        }
        
    }

    public function approveArMoney(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id',$id)
                    ->where('status', DBConstanst::ORDER_STATUS_PENDING)
                    ->firstOrFail();
            $order->status = DBConstanst::ORDER_STATUS_AR_APPROVED;
            $order->save();

            DB::commit();
            return response()->json([
                'message' => 'success',
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve ar order: ' . $e->getMessage());
            return response()->json(['message' => 'failed'], 500);
        }
    }
}
