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
        $name = $request->get('search', '');
        $supplier_name = $request->get('store', '');

        $products = Product::query()
            ->when($name, function ($query, $name) {
                $query->where(DB::raw('lower(design_name)'), 'like', "%".strtolower($name)."%");
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
        $products = Product::query();
        if ($name) {
            $products = $products->where(DB::raw('lower(design_name)'), 'like', DB::raw("'%" . strtolower($name) . "%'"));
        }

        if ($id) {
            $products = $products->orWhere('id', '=', $id);
        }
        //$products = $products->orWhereRaw('1 = 1');
        $products = $products->limit((int) $request->get('limit', 10))->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'sku' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'design_name' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'panjang_per_roll' => 'nullable|integer',
            'tipe' => 'nullable|string|max:100',
            'origin' => 'nullable|integer',
            'backing' => 'nullable|string|max:100',
            'kode_benang' => 'nullable|string|max:100',
            'reorder_level' => 'nullable|string|max:100',
            'manufacture_id' => 'nullable|string|max:100',
            'manufacture_category' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        try {
            $product = new Product();
            $product->sku = $validatedData['sku'];
            $product->category = $validatedData['category'];
            $product->design_name = $validatedData['design_name'];
            $product->color = $validatedData['color'];
            $product->pattern = $validatedData['pattern'];
            $product->panjang_per_roll = $validatedData['panjang_per_roll'];
            $product->tipe = $validatedData['tipe'];
            $product->origin = $validatedData['origin'];
            $product->backing = $validatedData['backing'];
            $product->kode_benang = $validatedData['kode_benang'];
            $product->reorder_level = $validatedData['reorder_level'];
            $product->manufacture_id = $validatedData['manufacture_id'];
            $product->manufacture_category = $validatedData['manufacture_category'];
            $product->supplier_id = $validatedData['supplier_id'];
            $product->deskripsi = $validatedData['deskripsi'];
            $product->image = $validatedData['image'];
            $product->save();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
        } catch (\Exception $th) {
            
            return redirect()->route('products.index')->with('error', 'Produk gagal dibuat, hubungi administrator.');
        }
    }

    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'sku' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'design_name' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'panjang_per_roll' => 'nullable|integer',
            'tipe' => 'nullable|string|max:100',
            'origin' => 'nullable|integer',
            'backing' => 'nullable|string|max:100',
            'kode_benang' => 'nullable|string|max:100',
            'reorder_level' => 'nullable|string|max:100',
            'manufacture_id' => 'nullable|string|max:100',
            'manufacture_category' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        try {

            $product = Product::findOrFail($id);
            $product->sku = $validatedData['sku'];
            $product->category = $validatedData['category'];
            $product->design_name = $validatedData['design_name'];
            $product->color = $validatedData['color'];
            $product->pattern = $validatedData['pattern'];
            $product->panjang_per_roll = $validatedData['panjang_per_roll'];
            $product->tipe = $validatedData['tipe'];
            $product->origin = $validatedData['origin'];
            $product->backing = $validatedData['backing'];
            $product->kode_benang = $validatedData['kode_benang'];
            $product->reorder_level = $validatedData['reorder_level'];
            $product->manufacture_id = $validatedData['manufacture_id'];
            $product->manufacture_category = $validatedData['manufacture_category'];
            $product->supplier_id = $validatedData['supplier_id'];
            $product->deskripsi = $validatedData['deskripsi'];
            $product->image = $validatedData['image'];
        $product->save();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diubah.');
        } catch (\Exception $th) {
            return redirect()->route('products.index')->with('error', 'Produk gagal diubah, hubungi administrator.');
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
            if ($index < 3)
                continue;  // Lewatkan 3 baris pertama
            if ($row[1] == null)
                break;

            // Insert data ke database
            $product = new Product();
            $product->ori_design = $row[1];
            $product->name = $row[3];
            $product->color = $row[2];
            $product->design_name = $row[3];
            $product->ori_barcode = $row[4];
            $product->pattern = $row[5];
            $product->lebar = $row[6];
            $product->panjang = $row[7];
            $product->kode_benang = $row[10];
            $product->cost = $row[11] == 'N/A' ? 0 : $row[11];
            $product->unit_bottom_price = $row[12];
            $product->unit_top_price = $row[13];
            $product->status = $row[15] == 'ACTIVE' ? 1 : 0;
            $product->origin = 2;
            $product->sku = $row[18];
            $product->desc = $row[19];

            // Simpan ke database
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupload.');
    }
}
