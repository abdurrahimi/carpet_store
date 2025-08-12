<?php

namespace App\Http\Controllers;

use App\Models\LpbbApproval;
use App\Models\LpbbItem;
use App\Models\LpbbMaster;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LpbbController extends Controller
{
    public function index()
    {
        return inertia('Lpbb/Index');
    }

    public function create()
    {
        $store = Store::all();
        $product = Product::all();
        return inertia('Lpbb/Create', [
            'title' => 'Create LPBB',
            'store' => $store,
            'product' => $product,
        ]);
    }

    public function getData(Request $request)
    {
        $data = LpbbMaster::with(['fromStore', 'toStore'])->paginate(10);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate request data
            $request->validate([
                'store_from' => 'required|exists:store,id|different:store_to',
                'store_to'   => 'required|exists:store,id|different:store_from',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:product',
                'products.*.total' => 'required|numeric|min:1'
            ]);

            $model = new LpbbMaster();
            $model->from_store = $request->store_from;
            $model->target_store = $request->store_to;
            $model->save();

            foreach ($request->products as $product) {
                $prd = Product::find($product['id']);
                $modelItem = new LpbbItem();
                $modelItem->lpbb_master_id = $model->id;
                $modelItem->product_id = $prd->id;
                $modelItem->jumlah = $product['total'];
                $modelItem->save();
            }

            $approval = new LpbbApproval();
            $approval->lpbb_master_id = $model->id;
            $approval->tipe = 1;
            $approval->save();

            $approval = new LpbbApproval();
            $approval->lpbb_master_id = $model->id;
            $approval->tipe = 2;
            $approval->save();

            DB::commit();
            return redirect()->route('lpbb.index')->with('success', 'LPBB berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
