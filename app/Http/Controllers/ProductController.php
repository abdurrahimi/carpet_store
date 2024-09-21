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
        $validatedData = $request->validate([
            'category.id' => 'required|integer|exists:categories,id',
            'supp.id' => 'nullable|integer|exists:suppliers,id',
            'ori_design' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'design_name' => 'nullable|string|max:255',
            'ori_barcode' => 'nullable|string|max:255',
            'pattern' => 'nullable|string|max:255',
            'lebar' => 'required|numeric',
            'panjang' => 'required|numeric',
            'kode_benang' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric',
            'unit_bottom_price' => 'nullable|numeric',
            'unit_top_price' => 'nullable|numeric',
            'status' => 'nullable|boolean',
            'origin' => 'required|integer|in:1,2',
            'sku' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
        ]);
        try {
            $product = new Product();
            $product->category_id = $validatedData['category']['id'];
            $product->supp_id = $validatedData['supp']['id'] ?? null;
            $product->ori_design = $validatedData['ori_design'];
            $product->name = $validatedData['name'];
            $product->color = $validatedData['color'];
            $product->design_name = $validatedData['design_name'];
            $product->ori_barcode = $validatedData['ori_barcode'];
            $product->pattern = $validatedData['pattern'];
            $product->lebar = $validatedData['lebar'];
            $product->panjang = $validatedData['panjang'];
            $product->kode_benang = $validatedData['kode_benang'];
            $product->cost = $validatedData['cost'] === 'N/A' ? 0 : $validatedData['cost'];
            $product->unit_bottom_price = $validatedData['unit_bottom_price'];
            $product->unit_top_price = $validatedData['unit_top_price'];
            $product->status = $validatedData['status'] === 'ACTIVE' ? 1 : 0;
            $product->origin = $validatedData['origin'];
            $product->sku = $validatedData['sku'];
            $product->desc = $validatedData['desc'];
            $product->save();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
        } catch (\Exception $th) {
            return redirect()->route('products.index')->with('error', 'Produk gagal dibuat, hubungi administrator.');
        }

    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'category.id' => 'required|integer|exists:categories,id',
            'supp.id' => 'nullable|integer|exists:suppliers,id',
            'ori_design' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'design_name' => 'nullable|string|max:255',
            'ori_barcode' => 'nullable|string|max:255',
            'pattern' => 'nullable|string|max:255',
            'lebar' => 'required|numeric',
            'panjang' => 'required|numeric',
            'kode_benang' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric',
            'unit_bottom_price' => 'nullable|numeric',
            'unit_top_price' => 'nullable|numeric',
            'status' => 'nullable|boolean',
            'origin' => 'required|integer|in:1,2',
            'sku' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
        ]);
        try {

            $product->category_id = $validatedData['category']['id'];
            $product->supp_id = $validatedData['supp']['id'] ?? null;
            $product->ori_design = $validatedData['ori_design'];
            $product->name = $validatedData['name'];
            $product->color = $validatedData['color'];
            $product->design_name = $validatedData['design_name'];
            $product->ori_barcode = $validatedData['ori_barcode'];
            $product->pattern = $validatedData['pattern'];
            $product->lebar = $validatedData['lebar'];
            $product->panjang = $validatedData['panjang'];
            $product->kode_benang = $validatedData['kode_benang'];
            $product->cost = $validatedData['cost'] === 'N/A' ? 0 : $validatedData['cost'];
            $product->unit_bottom_price = $validatedData['unit_bottom_price'];
            $product->unit_top_price = $validatedData['unit_top_price'];
            $product->status = $validatedData['status'] === 'ACTIVE' ? 1 : 0;
            $product->origin = $validatedData['origin'];
            $product->sku = $validatedData['sku'];
            $product->desc = $validatedData['desc'];
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
