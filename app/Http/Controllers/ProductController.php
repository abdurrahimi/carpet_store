<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\ProductCategory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);
        $name = $request->get('search', '');
        $supplier_name = $request->get('store', '');

        $products = Product::query()
            ->when($name, function ($query, $name) {
                $query->where(DB::raw('lower(design_name)'), 'like', "%" . strtolower($name) . "%");
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
            'sku' => 'required|string|max:100',
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
            'image' => 'nullable|string',
            'ori_barcode' => 'required|string',
            'thickness' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'lebar' => 'nullable|integer'
        ]);

        try {
            $fileData = base64_decode(preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $request->input('image')));
            $fileName = 'product-' . time() . '.png';
            $filePath = "public/products/$fileName";
            Storage::put($filePath, $fileData);

            $product = new Product();
            $product->sku = $request->input('sku');
            $product->category = $request->input('category');
            $product->design_name = $request->input('design_name');
            $product->color = $request->input('color');
            $product->pattern = $request->input('pattern');
            $product->panjang_per_roll = $request->input('panjang_per_roll');
            $product->tipe = $request->input('tipe');
            $product->origin = $request->input('origin');
            $product->backing = $request->input('backing');
            $product->kode_benang = $request->input('kode_benang');
            $product->reorder_level = $request->input('reorder_level');
            $product->manufacture_id = $request->input('manufacture_id');
            $product->manufacture_category = $request->input('manufacture_category');
            $product->supplier_id = $request->input('supplier_id');
            $product->deskripsi = $request->input('deskripsi');
            $product->image = Storage::url("products/$fileName");
            $product->ori_barcode = $request->input('ori_barcode', '');
            $product->thickness = $request->input('thickness', 0);
            $product->weight = $request->input('weight', 0);
            $product->lebar = $request->input('lebar', 0);
            $product->save();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
        } catch (\Exception $th) {

            return redirect()->route('products.index')->with('error', 'Produk gagal dibuat, hubungi administrator.');
        }
    }

    public function update($id, Request $request)
    {

        $validatedData = $request->validate([
            'sku' => 'required|string|max:100',
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
            'supplier.id' => 'nullable|number|max:100',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|string',
            'ori_barcode' => 'required|string',
            'thickness' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'lebar' => 'nullable|integer',
        ]);

        try {

            $fileData = base64_decode(preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $request->input('image')));
            $fileName = 'product-' . time() . '.png';
            $filePath = "public/products/$fileName";
            Storage::put($filePath, $fileData);

            $product = Product::findOrFail($id);
            $product->sku = $request->input('sku');
            $product->category = $request->input('category');
            $product->design_name = $request->input('design_name');
            $product->color = $request->input('color');
            $product->pattern = $request->input('pattern');
            $product->panjang_per_roll = $request->input('panjang_per_roll');
            $product->tipe = $request->input('tipe');
            $product->origin = $request->input('origin');
            $product->backing = $request->input('backing');
            $product->kode_benang = $request->input('kode_benang');
            $product->reorder_level = $request->input('reorder_level');
            $product->manufacture_id = $request->input('manufacture_id');
            $product->manufacture_category = $request->input('manufacture_category');
            $product->supplier_id = $request->input('supplier_id');
            $product->deskripsi = $request->input('deskripsi');
            $product->image = Storage::url("products/$fileName");
            $product->ori_barcode = $request->input('ori_barcode', '');
            $product->thickness = $request->input('thickness', 0);
            $product->weight = $request->input('weight', 0);
            $product->lebar = $request->input('lebar', 0);
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

    public function deleteBulk(Request $request)
    {
        $product = Product::whereIn('id', $request->input('id'))->delete();
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
            if ($index < 3) {
                continue; // Lewatkan 3 baris pertama
            }

            if ($row[1] == null) {
                break;
            }

            // Menyiapkan data untuk upsert
            $data = [
                'sku' => $row[1],
                'category' => $row[2],
                'design_name' => $row[3],
                'color' => $row[4],
                'pattern' => $row[5],
                'panjang_per_roll' => $row[6],
                'tipe' => $row[7],
                'origin' => $row[8],
                'backing' => $row[8],
                'kode_benang' => $row[9],
                'reorder_level' => $row[10],
                'manufacture_id' => $row[11],
                'manufacture_category' => $row[12],
                'supplier_id' => $row[13],
                'deskripsi' => $row[14]
            ];

            // Upsert data ke database
            Product::upsert(
                [$data],
                ['sku'],
                [
                    'category',
                    'design_name',
                    'color',
                    'pattern',
                    'panjang_per_roll',
                    'tipe',
                    'origin',
                    'backing',
                    'kode_benang',
                    'reorder_level',
                    'manufacture_id',
                    'manufacture_category',
                    'supplier_id',
                    'deskripsi'
                ]
            );
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupload.');
    }
}
