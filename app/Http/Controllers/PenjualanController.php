<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {

        $pageSize = (int) $request->get('limit', 10);
        $data = Order::query()->with('store');
        $data = $data->orderBy('created_at', 'desc')->paginate($pageSize);

        foreach ($data as $key => &$value) {
            $value->created_at = Carbon::parse($value->created_at)
                ->timezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
        }

        return Inertia::render('Penjualan/History', [
            'penjualan' => $data
        ]);
    }

    public function kasir(Request $request)
    {
        $pageSize = (int) $request->get('limit', 10);
        $data = Order::query()->with('creator')->where(DB::raw('date(created_at)'), date('Y-m-d'));
        if (Auth::user()->store_id) {
            $data = $data->where('store_id', Auth::user()->store_id);
        }
        $data = $data->orderBy('created_at', 'desc')->paginate($pageSize);

        return Inertia::render('Penjualan/Kasir', [
            'data' => $data
        ]);
    }
}
