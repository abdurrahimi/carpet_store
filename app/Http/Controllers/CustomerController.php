<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);

        return Inertia::render('Users/Customer', [
            'customer' => Customer::paginate($pageSize)
        ]);
    }

    public function getDataCustomer(Request $request)
    {
        $search = $request->get('search', '');

        $data = Customer::query()
            ->when($search, function ($q) use ($search) {
                return $q->where(DB::raw('lower(name)'), 'like', "%$search%");
            })->paginate(50);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'nullable|integer|in:1,2',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'store_id' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'npwp' => 'nullable|string|max:50',
            'id_no' => 'nullable|numeric',
            'id_img' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $customer = new Customer();
            $customer->type = $request->type;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->title = $request->title;
            $customer->address = $request->address;
            $customer->store_id = $request->store_id;
            $customer->email = $request->email;
            $customer->npwp = $request->npwp;
            $customer->id_no = $request->id_no;
            $customer->id_img = $request->id_img;
            $customer->created_by = auth()->user()->id; // Assuming lo pake auth
            $customer->save();

            DB::commit();
            return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.index')->with('error', 'Customer gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'nullable|integer|in:1,2',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'store_id' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'npwp' => 'nullable|string|max:50',
            'id_no' => 'nullable|numeric',
            'id_img' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);
            $customer->type = $request->type;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->title = $request->title;
            $customer->address = $request->address;
            $customer->store_id = $request->store_id;
            $customer->email = $request->email;
            $customer->npwp = $request->npwp;
            $customer->id_no = $request->id_no;
            $customer->id_img = $request->id_img;
            $customer->updated_by = auth()->user()->id;
            $customer->save();

            DB::commit();
            return redirect()->route('customers.index')->with('success', 'Customer berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.index')->with('error', 'Customer gagal diupdate.');
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:customers,id',
        ]);

        DB::beginTransaction();
        try {
            Customer::whereIn('id', $request->ids)->delete();
            DB::commit();
            return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.index')->with('error', 'Customer gagal dihapus.');
        }
    }
}
