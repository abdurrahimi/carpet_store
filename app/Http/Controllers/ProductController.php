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

        $products = Product::query()
            ->join('products_variant', 'products.id', '=', 'products_variant.product_id')
            ->join('store', 'products.store_id', '=', 'store.id')
            ->leftJoin('users as created_by_user', 'products.created_by', '=', 'created_by_user.id')
            ->leftJoin('users as updated_by_user', 'products.updated_by', '=', 'updated_by_user.id')
            ->select(
                'products.name as product_name',
                'products_variant.name as variant_name',
                'products_variant.length as variant_length',
                'products_variant.color as variant_color',
                'store.name as store_name',
                'products.created_at',
                'created_by_user.name as created_by',
                'products.updated_at',
                'updated_by_user.name as updated_by'
            )
            ->when($name, function($query, $name) {
                return $query->where('products.name', 'like', "%$name%");
            })
            ->when($store_name, function($query, $store_name) {
                return $query->where('store.name', 'like', "%$store_name%");
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
        // Validasi input
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_category' => 'required|exists:product_category,id',
            'variants' => 'required|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.length' => 'required|integer',
            'variants.*.color' => 'required|string|max:100',
            'toko_id.id' => 'required|exists:store,id',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Simpan produk
            $product = Product::create([
                'name' => $validated['product_name'],
                'store_id' => $request->toko_id['id'],
                'category_id' => $validated['product_category'],
                'created_by' => auth()->id(), // Sesuaikan dengan ID user yang login
            ]);

            // Simpan varian produk
            foreach ($validated['variants'] as $variantData) {
                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantData['name'],
                    'length' => $variantData['length'],
                    'color' => $variantData['color'],
                    'created_by' => auth()->id(), // Sesuaikan dengan ID user yang login
                ]);
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
