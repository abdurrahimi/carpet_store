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

        return Inertia::render('Users/Customer',[
            'customer' => Customer::paginate($pageSize)
        ]);
    }

    public function getDataCustomer(Request $request)
    {
        $search = $request->get('search', '');

        $data = Customer::query()
                ->when($search, function($q) use ($search) {
                    return $q->where(DB::raw('lower(name)'), 'like', "%$search%");
                })->paginate(50);
        return response()->json($data);
    }
}
