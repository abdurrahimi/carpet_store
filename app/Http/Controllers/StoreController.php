<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    //
    public function getDataStore(Request $request)
    {
        $name = $request->get('name', '');
        $store = Store::query()->select('id', 'name');
        if($name){
            $store = $store->where('name', 'like', DB::raw("'%".$name."%'"));
        }
        $store = $store->limit((int) $request->get('limit', 10))->get();
        return response()->json($store);
    }
}
