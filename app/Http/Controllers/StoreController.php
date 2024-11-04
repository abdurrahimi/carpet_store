<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->get('name', '');
        $store = Store::query()->with('manager');
        if($name){
            $store = $store->where('name', 'like', DB::raw("'%$name.%'"))
                            ->orWhere('name', 'like', "");
        }
        $store = $store->paginate((int) $request->get('limit', 10));

        return Inertia::render('Store/Index', [
            'store' => $store
        ]);
    }

    public function getDataStore(Request $request)
    {
        $name = $request->get('name', '');
        $store = Store::query()->select('id', 'name');
        if($name){
            $store = $store->where('name', 'like', DB::raw("'%$name%'"))
                            ->orWhere('name', '<>', null);
        }
        $store = $store->limit((int) $request->get('limit', 10))->get();
        return response()->json($store);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'store_type' => 'required|integer|min:1|max:2',
            'manager.id' => 'nullable|integer',
            'name' => 'required|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|digits_between:10,20'
        ]);

        try {
            $store = new Store();
            $store->store_type = $validatedData['store_type'];
            $store->manager_id = $validatedData['manager']['id'] ?? null;
            $store->name = $validatedData['name'];
            $store->address = $validatedData['address'] ?? null;
            $store->phone = $validatedData['phone'] ?? null;
            $store->created_by = Auth::user()->id;
            $store->updated_by = Auth::user()->id;
            $store->save();

            return redirect()->route('store.index')->with('success', 'Store created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating store: ' . $e->getMessage());
            return redirect()->route('store.index')->with('error', 'Failed to create store.');
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'store_type' => 'required|integer|min:1|max:2',
            'manager.id' => 'nullable|integer',
            'name' => 'required|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|digits_between:10,20'
        ]);

        try {
            $store = Store::findOrFail($id);
            $store->store_type = $validatedData['store_type'];
            $store->manager_id = $validatedData['manager']['id'] ?? null;
            $store->name = $validatedData['name'];
            $store->address = $validatedData['address'] ?? null;
            $store->phone = $validatedData['phone'] ?? null;
            $store->updated_by = Auth::user()->id;;
            $store->save();

            return redirect()->route('store.index')->with('success', 'Store update successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating store: ' . $e->getMessage());
            return redirect()->route('store.index')->with('error', 'Failed to update store.');
        }
    }

    // Remove the specified resource from storage
    public function destroy($id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();

            return redirect()->route('store.index')->with('success', 'Store delete successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting store: ' . $e->getMessage());
            return redirect()->route('store.index')->with('error', 'Failed to delete store.');
        }
    }
}
