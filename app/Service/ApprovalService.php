<?php

namespace App\Service;

use App\Models\Approval;
use App\Models\Karyawan;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Stock;
use App\Models\StockBekas;
use App\Models\User;

class ApprovalService
{
    public function validateApproval($approvalId)
    {
        return Approval::where('id', $approvalId)->where('status', 0)->exists();

        /* $user = User::find($data->requestor_id);
        $karyawan = Karyawan::find($user->karyawan_id);
        if ($karyawan->leader_id != auth()->user()->karyawan_id) return false;
        return true; */
    }

    public function approve($approvalId, $type)
    {
        if ($type == 0) return false;
        $data = Approval::find($approvalId);
        $data->status = $type;
        $data->save();

        if ($data->type == 'stock') {
            $this->approveStockAdd($approvalId);
        }
    }

    private function approveStockAdd($approvalId)
    {

        $stock = Stock::where('approval_id', $approvalId)->first();
        //stock type 1 = baru , 2 = bekas
        if ($stock->type == 'IN' && $stock->stock_type == '1') {
            $product = Product::find($stock->product_id);
            $product->stock_new += $stock->total;
            $product->save();

            $stock = ProductStock::where('product_id', $stock->product_id)->first();
            if($product->panjang == 30) {
                $stock['30meter'] += $stock->total;
            }
            $stock->save();
        } else if ($stock->type == 'OUT' && $stock->stock_type == '1') {
            $product = Product::find($stock->product_id);
            $product->stock_new -= $stock->total;
            $product->save();
        } else if ($stock->type == 'IN' && $stock->stock_type == '2') {
            $stock2 = new StockBekas();
            $stock2->product_id = $stock->product_id;
            $stock2->jumlah = $stock->total;
            $stock2->panjang = $stock->panjang;
            $stock2->save();
        } else if ($stock->type == 'OUT' && $stock->stock_type == '2') {
            $product = StockBekas::where('code', $stock->code)->first();
            $product->panjang -= $stock->panjang;
            $product->save();
        }
    }
}
