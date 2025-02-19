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
            'color' => 'nullable|array',
            'color.*.id' => 'nullable|integer',
            'color.*.name' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $model = ProductCategory::findOrFail($id);
            $model->name = $request->name;
            $model->tipe_penjualan = $request->tipe_penjualan;
            $model->save();

            $colorData = collect($request->color ?? []);

            // Ambil semua warna yang sudah ada untuk kategori ini
            $existingColorIds = ProductCategoryColor::where('category_id', $model->id)
                ->pluck('id')
                ->toArray();

            // Filter ID warna hanya yang milik kategori ini
            $inputColorIds = $colorData->pluck('id')
                ->filter(fn($id) => in_array($id, $existingColorIds))
                ->toArray();

            // Hapus warna yang tidak ada dalam input tapi milik kategori ini
            ProductCategoryColor::where('category_id', $model->id)
                ->whereNotIn('id', $inputColorIds)
                ->delete();

            // Upsert warna hanya untuk kategori ini
            ProductCategoryColor::upsert(
                $colorData->map(fn($color) => [
                    'id' => in_array($color['id'] ?? null, $existingColorIds) ? $color['id'] : null,
                    'name' => $color['name'],
                    'category_id' => $model->id, // Pastikan kategori benar
                ])->toArray(),
                ['id', 'category_id'], // Gunakan kombinasi ID dan category_id
                ['name']
            );

            DB::commit();
            return redirect()->route('category.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
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
