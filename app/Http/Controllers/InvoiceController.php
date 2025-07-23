<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download($orderId)
    {
        $order = Order::with(['orderDetails', 'customer', 'store', 'company', 'orderAdditional'])
            ->findOrFail($orderId);

        return view('invoice', [
            'order' => $order,
        ]);
    }
}
