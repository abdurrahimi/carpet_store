<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\ProductCategory;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);
        $name = $request->get('name', '');
        $supplier_name = $request->get('store', '');

        $products = Product::query()
            ->with([
                'category' => function ($q) {
                    return $q->select('id', 'name');
                }
            ])
            ->when($name, function ($query, $name) {
                return $query->where('product.name', 'like', "%$name%");
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

    public function getDataProduct(Request $request)
    {
        $name = $request->get('name', '');
        $id = $request->get('name', null);
        $products = Product::query()->select('id','name', 'unit_price');
        if($name){
            $products = $products->where('name', 'like', DB::raw("'%".$name."%'"));
        }

        if($id) {
            $products = $products->orWhere('id', '=', $id);
        }

        $products = $products->orWhereRaw('1 = 1')->limit((int) $request->get('limit', 10))->get();
        return response()->json($products);
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

    public function importExcel(Request $request)
    {
        // Validasi file excel
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Load file Excel yang di-upload
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());

        // Ambil sheet pertama
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Mulai dari baris ke-4 (B4)
        foreach ($rows as $index => $row) {
            if ($index < 3) continue;  // Lewatkan 3 baris pertama
            if($row[1] == null) break;

            // Insert data ke database
            $product = new Product();
            $product->ori_design = $row[1];
            $product->name = $row[1];
            $product->color = $row[2];
            $product->design_name = $row[3];
            $product->ori_barcode = $row[4];
            $product->pattern = $row[5];
            $product->lebar = $row[6];
            $product->panjang = $row[7];
            //$product->berat = $row[];
            //$product->category_id = $row[9];
            $product->kode_benang = $row[10];
            $product->cost = $row[11] == 'N/A' ? 0 : $row[11];
            //$product->product_id = $row[12];
            //$product->supp_id = $row[13];
            //$product->name = $row[14];
            $product->unit_bottom_price = $row[12];
            $product->unit_top_price = $row[13];
            $product->status = $row[15] == 'ACTIVE' ? 1 : 0;
            $product->origin = 1;
            $product->sku = $row[18];
            $product->desc = $row[19];

            // Simpan ke database
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupload.');
    }
}
