<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\ProductCategoryColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);

        return Inertia::render('Category/Index', [
            'datas' => ProductCategory::with([
                'color' => function ($q) {
                    return $q->select('id', 'name', 'category_id')->get();
                }
            ])
            ->paginate($pageSize)
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->get('search', '');

        $data = ProductCategory::query()
            ->when($search, function ($q) use ($search) {
                return $q->where(DB::raw('lower(name)'), 'like', "%$search%");
            })->limit(50)->get();
        return response()->json($data);
    }

    public function getDataColor(Request $request, $categoryId)
    {
        $search = $request->get('search', '');

        $data = ProductCategoryColor::query()
            ->where('category_id', $categoryId)
            ->when($search, function ($q) use ($search) {
                return $q->where(DB::raw('lower(name)'), 'like', "%$search%");
            })->limit(50)->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'tipe_penjualan' => 'nullable|integer|in:1,2', //1 = satuan, 2 = meteran
            'color.*.name' => 'required|string|max:20'
        ]);
        
        DB::beginTransaction();
        try {
            $model = new ProductCategory();
            $model->name = $request->name;
            $model->tipe_penjualan = $request->tipe_penjualan;
            $model->save();

            foreach ($request->color as $color) {
                $modelColor = new ProductCategoryColor();
                $modelColor->name = $color['name'];
                $modelColor->category_id = $model->id;
                $modelColor->save();
            }

            DB::commit();
            return redirect()->route('category.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('category.index')->with('error', 'Data gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'tipe_penjualan' => 'nullable|integer|in:1,2', // 1 = satuan, 2 = meteran
            'color.*.name' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $model = ProductCategory::findOrFail($id);
            $model->name = $request->name;
            $model->tipe_penjualan = $request->tipe_penjualan;
            $model->save();

            // Ambil ID warna yang ada di input
            $inputColorIds = collect($request->color)->pluck('id')->filter()->toArray();

            // Hapus warna yang tidak ada dalam input
            ProductCategoryColor::whereNotIn('id', $inputColorIds)->delete();

            // Upsert warna yang ada di input
            ProductCategoryColor::upsert(
                collect($request->color)->map(function ($color) use ($model) {
                    return [
                        'id' => $color['id'] ?? null,
                        'name' => $color['name'],
                        'category_id' => $model->id,
                    ];
                })->toArray(),
                ['id'],
                ['name']
            );

            DB::commit();
            return redirect()->route('category.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->route('category.index')->with('error', 'Data gagal diperbarui.');
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:product_category,id',
        ]);
        DB::beginTransaction();
        try {
            ProductCategory::whereIn('id', $request->ids)->delete();
            ProductCategoryColor::whereIn('category_id', $request->ids)->delete();
            DB::commit();
            return redirect()->route('category.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->route('category.index')->with('error', 'Data gagal dihapus.');
        }
    }
}
