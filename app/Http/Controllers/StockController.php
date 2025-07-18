<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Supplier;
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
                return $q->select('id', 'design_name');
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

    public function checkStock(Request $request)
    {
        return Inertia::render('Stock/Check');
    }

    public function getStockLists(Request $request)
    {
        $query = $request->get('query', '');
        $data = Product::query()
            ->with(['productStock', 'color'])
            ->select('id', 'design_name')
            ->where(DB::raw('lower(design_name)'), 'like', '%' . strtolower($query) . '%')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    public function getDataStockDetail(Request $request)
    {
        $query = $request->get('id', '');
        $data = Product::query()
            ->with('productStock')
            ->where('id', $query)
            ->first();

        return response()->json($data);
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
            'type' => 'required|in:IN,OUT,REJECT',
            'panjang' => 'nullable|integer',
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
            $approval->type = 'stock';
            $approval->title = 'Stock Approval';
            $supplier = Supplier::find($validatedData['supplier']['id']);
            $product = Product::find($validatedData['product']['id']);
            $store = Store::find($validatedData['store']['id']);

            $prefix = '';
            if($request->type == 'IN') {
                $prefix = 'Stock Masuk';
            }

            if($request->type == 'OUT') {
                $prefix = 'Stock Keluar';
            }

            if($request->stock_type == 1) {
                $prefix .= ' Baru';
            }

            if($request->stock_type == 2) {
                $prefix .= ' Bekas';
            }

            $approval->detail = $prefix . ' produk '.$product->design_name.' di '.$store->name.' dari supplier '.$supplier->name.' sebanyak '.$request->total.', ditambahkan oleh user '. Auth::user()->name;
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
            $stock->type = $request->type;
            $stock->panjang = $request->panjang ?? 0;
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
