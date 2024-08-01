<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);
        $name = $request->get('name', '');
        $store_name = $request->get('store', '');
        $supplier_name = $request->get('store', '');

        $products = Product::query()->select('product.*', 'store.name as store_name')
            ->leftJoin('store', 'product.store_id', 'store.id')
            ->when($name, function($query, $name) {
                return $query->where('product.name', 'like', "%$name%");
            })
            ->when($store_name, function($query, $store_name) {
                return $query->where('store.name', 'like', "%$store_name%");
            })
            ->when($supplier_name, function($query, $supplier_name) {
                return $query->where('suplier.name', 'like', "%$supplier_name%");
            })
            ->paginate($pageSize);

        $category = ProductCategory::select('id','name')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $category
        ]);
    }

    public function store(Request $request)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        $name =  $request->get('search', '');
        $pageSize = $request->get('page_size', 10);
        try {

            $product = Product::query();
            if($name) {
                $product = $product->where('name','like', "%$name%");
            }

            // Commit transaksi
            DB::commit();

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        }
    }
}
