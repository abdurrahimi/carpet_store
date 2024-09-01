<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', '');
        $pageSize = (int) $request->get('limit', 10);
        $stock = Stock::query()->with([
            'product' => function($q){
                return $q->select('id', 'name');
            },
            'supplier' => function($q){
                return $q->select('id', 'name');
            },
            'store' => function($q){
                return $q->select('id', 'name');
            },
            'approval' => function($q){
                return $q->select('id', 'status');
            }
        ])
        ->has('product')
        ->has('approval');

        if ($type) {
            $stock = $stock->where('type', $type);
        }

        $stock = $stock->paginate($pageSize);

        return Inertia::render('Stock/Index', [
            'stock' => $stock
        ]);
    }

    public function getDataStock(Request $request)
    {
        $type = $request->get('type', '');
        $stock = Stock::query()->select('id', 'total', 'type');

        if ($type) {
            $stock = $stock->where('type', $type);
        }

        $stock = $stock->limit((int) $request->get('limit', 10))->get();
        return response()->json($stock);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ref_id' => 'nullable|integer',
            'product.id' => 'required|integer|exists:product',
            'supplier.id' => 'required|integer|exists:supplier',
            'product_type' => 'required|integer|in:1,2',
            'store.id' => 'required|integer|exists:store',
            'total' => 'required|integer'
        ]);
        DB::beginTransaction();
        try {
            $manajer = Store::find($validatedData['store']['id'])->manager_id;

            //create approval
            $approval = new Approval();
            $approval->status = 0;
            $approval->requestor_id = Auth::user()->id;
            $approval->approver_id = $manajer;
            $approval->save();

            //create stock
            $stock = new Stock();
            $stock->ref_id = $validatedData['ref_id'] ?? null;
            $stock->supplier_id = $validatedData['supplier']['id'];
            $stock->product_id = $validatedData['product']['id'];
            $stock->store_id = $validatedData['store']['id'];
            $stock->approval_id = $approval->id;
            $stock->total = $validatedData['total'];
            $stock->stock_type = $validatedData['product_type'];
            $stock->type = 'IN';
            $stock->created_by = Auth::user()->id;
            $stock->updated_by = Auth::user()->id;
            $stock->save();

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'Stock created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating stock: ' . $e->getMessage());
            return redirect()->route('stock.index')->with('error', 'Failed to create stock.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $stock = Stock::with('approval')->find($id);
            if($stock->approval->status == 1){
                return redirect()->route('stock.index')->with('failed', 'Stock cannot be deleted');
            }
            $stock->delete();
            $stock->approval->delete();
            DB::commit();
            return redirect()->route('stock.index')->with('success', 'Stock deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting stock: ' . $e->getMessage());
            return redirect()->route('stock.index')->with('error', 'Failed to delete stock.');
        }
    }
}
