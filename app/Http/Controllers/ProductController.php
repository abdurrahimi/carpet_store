<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
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
            ->with([
                'category' => function ($q) {
                    return $q->select('id', 'name');
                },
                'store' => function ($q) {
                    return $q->select('id', 'name');
                },
            ])
            ->leftJoin('store', 'product.store_id', 'store.id')
            ->when($name, function ($query, $name) {
                return $query->where('product.name', 'like', "%$name%");
            })
            ->when($store_name, function ($query, $store_name) {
                return $query->where('store.name', 'like', "%$store_name%");
            })
            ->when($supplier_name, function ($query, $supplier_name) {
                return $query->where('suplier.name', 'like', "%$supplier_name%");
            })
            ->orderBy("updated_at", "desc")
            ->paginate($pageSize);

        $category = ProductCategory::select('id', 'name')->get();
        $supplier = Supplier::select('id', 'name')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $category,
            'supplier' => $supplier
        ]);
    }

    public function getCategory(Request $request)
    {
        $name = $request->get('name', '');
        $pageSize = (int) $request->get('page_size', 10);
        return ProductCategory::query()
            ->select('id', 'name')
            ->when($name, function ($query, $name) {
                return $query->where('product.name', 'like', "%$name%");
            })
            ->paginate($pageSize);

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            //'product_id' => 'nullable|integer',
            'category.id' => 'required|integer',
            'supplier.id' => 'nullable|integer',
            'store.id' => 'required|integer',
            'name' => 'nullable|string|max:255',
            'origin' => 'required|integer',
            'color' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric',
            'design_desc' => 'nullable|string',
            'pattern_desc' => 'nullable|string',
            'pattern_name' => 'nullable|string|max:255',
            'design_name' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'mfg_date' => 'nullable|date',
            'length' => 'required|integer|min:0',
            'width' => 'required|integer|min:0',
            'roll_number' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $product = new Product();
            $product->product_id = $validatedData['product_id'] ?? null;
            $product->category_id = $validatedData['category']['id']  ?? null;
            $product->supp_id = $validatedData['supplier']['id'] ?? null;
            $product->name = $validatedData['name'];
            $product->origin = $validatedData['origin'];
            $product->store_id = $validatedData['store']['id'];
            $product->color = $validatedData['color'];
            $product->cost = $validatedData['cost'];
            $product->unit_price = $validatedData['unit_price'];
            $product->design_desc = $validatedData['design_desc'];
            $product->pattern_desc = $validatedData['pattern_desc'];
            $product->pattern_name = $validatedData['pattern_name'];
            $product->design_name = $validatedData['design_name'];
            $product->year = $validatedData['year'];
            $product->mfg_date = $validatedData['mfg_date'];
            $product->length = $validatedData['length'];
            $product->width = $validatedData['width'];
            $product->roll_number = $validatedData['roll_number'];

            // Simpan product ke database
            $product->save();

            // Commit transaksi
            DB::commit();

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();
            return redirect()->route('products.index')->with('error', 'Produk gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            //'product_id' => 'nullable|integer',
            'category.id' => 'required|integer',
            'supplier.id' => 'nullable|integer',
            'store.id' => 'required|integer',
            'name' => 'nullable|string|max:255',
            'origin' => 'required|integer',
            'color' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric',
            'design_desc' => 'nullable|string',
            'pattern_desc' => 'nullable|string',
            'pattern_name' => 'nullable|string|max:255',
            'design_name' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'mfg_date' => 'nullable|date',
            'length' => 'required|integer|min:0',
            'width' => 'required|integer|min:0',
            'roll_number' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::find($id);
            $product->product_id = $validatedData['product_id'] ?? null;
            $product->category_id = $validatedData['category']['id'] ?? null;
            $product->supp_id = $validatedData['supplier']['id'] ?? null;
            $product->name = $validatedData['name'];
            $product->origin = $validatedData['origin'];
            $product->store_id = $validatedData['store']['id'];
            $product->color = $validatedData['color'];
            $product->cost = $validatedData['cost'];
            $product->unit_price = $validatedData['unit_price'];
            $product->design_desc = $validatedData['design_desc'];
            $product->pattern_desc = $validatedData['pattern_desc'];
            $product->pattern_name = $validatedData['pattern_name'];
            $product->design_name = $validatedData['design_name'];
            $product->year = $validatedData['year'];
            $product->mfg_date = $validatedData['mfg_date'];
            $product->length = $validatedData['length'];
            $product->width = $validatedData['width'];
            $product->roll_number = $validatedData['roll_number'];

            // Simpan product ke database
            $product->save();

            // Commit transaksi
            DB::commit();

            return redirect()->route('products.index')->with('success', 'Produk berhasil diubah.');
        } catch (\Exception $e) {
            dd($e);
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();
            return redirect()->route('products.index')->with('error', 'Terjadi Error.');
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
