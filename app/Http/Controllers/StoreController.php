<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->get('name', '');
        $store = Store::query()->select('id', 'name');
        if($name){
            $store = $store->where('name', 'like', DB::raw("'%$name.%'"))
                            ->orWhere('name', 'like', "");
        }
        $store = $store->limit((int) $request->get('limit', 10))->get();

        return Inertia::render('Store/Index', [
            'store' => $store
        ]);
    }

    public function getDataStore(Request $request)
    {
        $name = $request->get('name', '');
        $store = Store::query()->select('id', 'name');
        if($name){
            $store = $store->where('name', 'like', DB::raw("'%$name.%'"))
                            ->orWhere('name', 'like', "");
        }
        $store = $store->limit((int) $request->get('limit', 10))->get();
        return response()->json($store);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'store_type' => 'required|integer|min:1|max:2',
                'manager_id' => 'nullable|integer',
                'name' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'phone' => 'nullable|digits_between:10,20',
                'created_by' => 'nullable|integer',
                'updated_by' => 'nullable|integer',
            ]);

            $store = new Store();
            $store->store_type = $validatedData['store_type'];
            $store->manager_id = $validatedData['manager_id'];
            $store->name = $validatedData['name'];
            $store->address = $validatedData['address'];
            $store->phone = $validatedData['phone'];
            $store->created_by = $validatedData['created_by'];
            $store->updated_by = $validatedData['updated_by'];
            $store->save();

            return response()->json(['message' => 'Store created successfully', 'store' => $store], 201);
        } catch (\Exception $e) {
            Log::error('Error creating store: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create store'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'store_type' => 'required|integer|min:1|max:2',
                'manager_id' => 'nullable|integer',
                'name' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'phone' => 'nullable|digits_between:10,20',
                'created_by' => 'nullable|integer',
                'updated_by' => 'nullable|integer',
            ]);

            $store = Store::findOrFail($id);
            $store->store_type = $validatedData['store_type'];
            $store->manager_id = $validatedData['manager_id'];
            $store->name = $validatedData['name'];
            $store->address = $validatedData['address'];
            $store->phone = $validatedData['phone'];
            $store->created_by = $validatedData['created_by'];
            $store->updated_by = $validatedData['updated_by'];
            $store->save();

            return response()->json(['message' => 'Store updated successfully', 'store' => $store], 200);
        } catch (\Exception $e) {
            Log::error('Error updating store: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update store'], 500);
        }
    }

    // Remove the specified resource from storage
    public function destroy($id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();

            return response()->json(['message' => 'Store deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting store: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete store'], 500);
        }
    }
}
