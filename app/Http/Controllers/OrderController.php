<?php

namespace App\Http\Controllers;

use App\Constant\DBConstanst;
use App\Models\Approval;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderAdditional;
use App\Models\OrderApprovalComments;
use App\Models\OrderApprovalLog;
use App\Models\OrderDetail;
use App\Models\OrderVariant;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function order(Request $requests)
    {
        $request = json_decode($requests->data, true);
        $request = new Request($request);
        $request->validate([
            'customer.id' => 'required|integer|exists:customer,id',
            'data' => 'required|array|min:1',
            'data.*.product' => 'required|array',
            'data.*.product.id' => 'required|integer',
            'data.*.qty' => 'required|integer',
            'data.*.discount_price' => 'nullable|numeric',
            'data.*.discount_percentage' => 'nullable|numeric|between:0,100',

            'data.*.variants' => 'nullable|array',
            'data.*.variants.*.panjang' => 'required|numeric|in:6,12,18,24,30',
            'data.*.variants.*.jumlah' => 'required|integer|min:1',
            'data.*.unit_price' => 'required|numeric',

            //'customer' => 'nullable|integer|exists:customer',

            'discount' => 'required|numeric',
            'discount_percentage' => 'required|numeric|between:0,100',

            'additional' => 'nullable|array',
            'additional.*.name' => 'nullable|string|max:255',
            'additional.*.total' => 'nullable|numeric',
            'company.id' => 'required|integer|exists:company,id',
            'shipping_to' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_phone' => 'required|string'
        ]);
        
        DB::beginTransaction();
        try {
            $product_ids = collect($request->input('data'))
                ->pluck('product.id')
                ->filter()
                ->all();

            $products = Product::query()
                ->whereIn('id', $product_ids)->get()
                ->keyBy('id')
                ->toArray();

            $store = Store::query()
                ->where('id', Auth::user()->store_id)
                ->firstOrFail();

            $company = Company::findOrFail($request->input('company.id'));


            $totalAdditional = 0;
            foreach ($request->additional as $additional) {
                $totalAdditional += (int) str_replace('.', '', $additional['total']);
            }

            $totalItemPriceBeforeDisc = 0;
            foreach ($request->data as $produx) {
                $totalItemPriceBeforeDisc += (int)str_replace('.', '', $produx['unit_price']) * ((int) str_replace('.', '', $produx['qty']) / 6) - (int)str_replace('.', '', $produx['discount_price'] ?? 0);
            }

            $attachments = [];
            if ($requests->file('attachments')) {
                foreach ($requests->file('attachments') as $file) {
                    $path = $file->store('uploads/attachments', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                    ];
                }
            }

            //create new temporary order
            $order = new Order();
            $order->customer_id = $request->input('customer.id', 0);
            $order->discount = str_replace('.','',$request->input('discount', 0));
            $order->discount_percentage = $request->input('discount_percentage', 0);;
            $order->total_additional_price = $totalAdditional;
            $order->total_price_before_disc = $totalItemPriceBeforeDisc; //harga sebelum diskon global
            $order->final_price = $totalItemPriceBeforeDisc + $totalAdditional - (int)str_replace('.', '', $request->discount); //harga setelah diskon global
            $order->store_id = Auth::user()->store_id;
            $order->approval_id = 0;
            $order->attachments = isset($attachments) ? json_encode($attachments) : null;
            $order->status = DBConstanst::ORDER_STATUS_PENDING;
            $order->payment_method = $request->input('payment_method', 0);
            $order->shipping_method = strtolower($request->input('shipping_method', ''));

            $company = Company::findOrFail($request->input('company.id'));
            $number = $company->last_invoice_no + 1;
            $order->invoice_no = $company->code .'/'. $store->code . '/' . date('my').'/INV/' . str_pad($number, 6, '0',  STR_PAD_LEFT);
            $company->increment('last_invoice_no');
            $order->company_id = $request->input('company.id');
            $order->shipping_to = $request->shipping_to;
            $order->shipping_address = $request->shipping_address;
            $order->shipping_phone = $request->shipping_phone;
            $order->save();

            $orderId = $order->id;

            //$totalPrice = 0;

            $approval = false;
            foreach ($request->input('data') as $item) {

                $item['unit_price'] = (int) str_replace('.', '', $item['unit_price']);
                $item['discount_price'] = (int) str_replace('.', '', $item['discount_price'] ?? 0);
                
                $productOrigin = $products[$item['product']['id']] ?? null;
                if (!$productOrigin) {
                    DB::rollBack();
                    return response()->json(['message' => 'product is not valid']);
                }

                if ($productOrigin['lowest_price'] > $item['unit_price']) {
                    $approval = true;
                }

                //order detail
                $orderDetail = new OrderDetail();
                $orderDetail->product_id            = $item['product']['id'];
                $orderDetail->unit_lower_price      = $productOrigin['lowest_price'];
                $orderDetail->unit_higher_price     = $productOrigin['higher_price'];
                $orderDetail->product_name          = $item['product']['design_name'];
                $orderDetail->discount              = $item['discount_price'];
                $orderDetail->discount_percentage   = $item['discount_percentage'];
                $orderDetail->unit_selling_price    = $item['unit_price'];
                $orderDetail->qty                   = $item['qty'];
                $orderDetail->dimension             = ($item['qty']*100) . ' x ' . ($item['product']['lebar'] ?? '');
                $orderDetail->order_id              = $orderId;
                $orderDetail->selling_total_price   = ($item['qty'] / 6) * $item['unit_price'] - $item['discount_price'];
                $orderDetail->save();

                //TODO: update stock
                //$totalPrice += ($item['qty'] / 6) * $item['unit_price'] - $item['discount_price'];

                foreach ($item['variants'] as $variant) {
                    $orderVarian = new OrderVariant();
                    $orderVarian->order_detail_id = $orderDetail->id;
                    $orderVarian->panjang = $variant['panjang'];
                    $orderVarian->jumlah = $variant['jumlah'];
                    $orderVarian->save();
                }
            }

            if ($approval) {
                $approval = new Approval();
                $approval->order_id = $orderId;
                $approval->requestor = auth()->user()->id;

                $approval->save();
            }

            foreach ($request->input('additional') as $additional) {
                $orderAdditional = new OrderAdditional();
                $orderAdditional->order_id = $orderId;
                $orderAdditional->detail = $additional['name'];
                $orderAdditional->price = $additional['total'];
                $orderAdditional->save();
            }

            OrderApprovalLog::create([
                'user_id' => auth()->id(),
                'order_id' => $orderId,
                'detail' => 'Order telah dibuat, Menunggu Approval',
                'status' => 1,
            ]);


            DB::commit();
            return redirect()->route('penjualan.kasir', ['data' => $order])->with('success', 'Transaksi berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order: ', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString(),
            ]);
            return redirect()->route('penjualan.kasir')->with('error', 'Transaksi gagal ditambahkan.');
        }
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id', $id)
                ->whereNotIn('status', [DBConstanst::ORDER_STATUS_DELIVERED, DBConstanst::ORDER_STATUS_REJECTED])
                ->firstOrFail();

            switch ($order->status) {
                case DBConstanst::ORDER_STATUS_PENDING:
                    if (Auth::user()->role !== 'finance' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    if ($order->payment_method == DBConstanst::PAYMENT_METHOD_AR) {
                        // Owner approve saldo untuk metode AR
                        $order->status = DBConstanst::ORDER_STATUS_AR_CHECKED;
                        $order->save();

                        OrderApprovalLog::create([
                            'user_id' => auth()->id(),
                            'order_id' => $id,
                            'detail' => 'AR telah diperiksa, Menunggu Approval',
                            'status' => 1,
                        ]);
                    } else {
                        // Pembayaran Cash langsung dianggap diterima
                        $order->status = DBConstanst::ORDER_STATUS_PAYMENT_APPROVED;
                        $order->save();

                        OrderApprovalLog::create([
                            'user_id' => auth()->id(),
                            'order_id' => $id,
                            'detail' => 'Pembayaran Telah Disetujui, Menunggu Pemeriksaan Stock',
                            'status' => 1,
                        ]);
                    }
                    break;
                case DBConstanst::ORDER_STATUS_AR_CHECKED:
                    if (Auth::user()->role !== 'owner' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    $order->status = DBConstanst::ORDER_STATUS_AR_APPROVED;
                    $order->save();

                    OrderApprovalLog::create([
                        'user_id' => auth()->id(),
                        'order_id' => $id,
                        'detail' => 'AR telah distujui, Menunggu Pemeriksaan Stock',
                        'status' => 1,
                    ]);

                    break;
                case DBConstanst::ORDER_STATUS_AR_APPROVED:
                case DBConstanst::ORDER_STATUS_PAYMENT_APPROVED:
                    if (Auth::user()->role !== 'warehouse' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    $order->status = DBConstanst::ORDER_STATUS_STOCK_AVAILABLE;
                    $order->save();

                    OrderApprovalLog::create([
                        'user_id' => auth()->id(),
                        'order_id' => $id,
                        'detail' => 'Stock Telah Diperiksa dan Tersedia, Menunggu Surat Jalan',
                        'status' => 1,
                    ]);
                    break;
                case DBConstanst::ORDER_STATUS_STOCK_AVAILABLE:
                    if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    $order->status = DBConstanst::ORDER_STATUS_SENT_TO_CUSTOMER;
                    $order->save();

                    OrderApprovalLog::create([
                        'user_id' => auth()->id(),
                        'order_id' => $id,
                        'detail' => 'Order Dalam Pengiriman Ke Customer',
                        'status' => 1,
                    ]);

                    break;
                case DBConstanst::ORDER_STATUS_STOCK_UNAVAILABLE:
                    if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    $order->status = DBConstanst::ORDER_STATUS_REQUESTED_TO_SUPPLIER;
                    $order->save();

                    OrderApprovalLog::create([
                        'user_id' => auth()->id(),
                        'order_id' => $id,
                        'detail' => 'Stock Telah Direquest ke Supplier',
                        'status' => 1,
                    ]);
                    break;
                case DBConstanst::ORDER_STATUS_REQUESTED_TO_SUPPLIER:
                case DBConstanst::ORDER_STATUS_SENT_TO_CUSTOMER:
                    if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'super_admin') {
                        DB::rollBack();
                        return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                    }

                    $order->status = DBConstanst::ORDER_STATUS_DELIVERED;
                    $order->save();

                    OrderApprovalLog::create([
                        'user_id' => auth()->id(),
                        'order_id' => $id,
                        'detail' => 'Order Telah Dikirim Ke Customer',
                        'status' => 1,
                    ]);
                    break;
                default:
                    DB::rollBack();
                    throw new Exception('Invalid order status');
            }

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Approval berhasil');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve order: ' . $e->getMessage());
            return response()->json(['message' => 'Approval Gagal.'], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id', $id)
                ->whereNotIn('status', [
                    DBConstanst::ORDER_STATUS_SENT_TO_CUSTOMER,
                    DBConstanst::ORDER_STATUS_REJECTED,
                    DBConstanst::ORDER_STATUS_CANCELED
                ])
                ->firstOrFail();

            // Hanya order dengan status PENDING atau AR_APPROVED yang bisa direject
            if ($order->status == DBConstanst::ORDER_STATUS_PENDING) {
                if (Auth::user()->role !== 'finance' && Auth::user()->role !== 'admin') {
                    return redirect()->route('penjualan.index')->with('error', 'Role Anda tidak memiliki akses untuk melakukan approval pada order ini.');
                }
                $order->status = DBConstanst::ORDER_STATUS_REJECTED;
                $order->save();
                OrderApprovalLog::create([
                    'user_id' => auth()->id(),
                    'order_id' => $id,
                    'detail' => 'Order telah direject, Alasan: ' . $request->reason,
                    'status' => 2,
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'Berhasil diupdate',
                    'order_id' => $order->id,
                ]);
            }

            if (DBConstanst::ORDER_STATUS_AR_APPROVED && DBConstanst::ORDER_STATUS_PAYMENT_APPROVED) {
                $order->status = DBConstanst::ORDER_STATUS_STOCK_UNAVAILABLE;
                $order->save();
                OrderApprovalLog::create([
                    'user_id' => auth()->id(),
                    'order_id' => $id,
                    'detail' => 'Stock telah diperiksa dan tidak tersedia, Menunggu Pengiriman Dokumen ke Supplier',
                    'status' => 2,
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'Berhasil diupdate',
                    'order_id' => $order->id,
                ]);
            }

            return response()->json([
                'message' => 'Gagal diupdate',
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject order: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal diupdate',
            ], 500);
        }
    }

    public function addComments(Request $request, $id)
    {
        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10MB
            'description' => 'nullable|string|max:255',
        ]);
        try {
            Order::findOrFail($id);
            $filename = null;
            if ($request->hasFile('attachment')) {
                //dd("okok");
                $attachment = $request->file('attachment');
                $filename = time() . '_' . $attachment->getClientOriginalName();
                $path = $attachment->storeAs('uploads/attachments', $filename, 'public');
                $filename = $path; // Simpan path lengkap
            }
            //dd($filename);
            // Simpan ke database
            OrderApprovalLog::create([
                'user_id' => auth()->id(),
                'order_id' => $id,
                'detail' => $request->input('description', 'No description provided'),
                'attachment' => $filename,
                'status' => 1,
            ]);

            return redirect()->route('penjualan.index')->with('success', 'Attachment berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Failed to add attachment: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add attachment'], 500);
        }
    }

    public function getStatusHistory(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'order_id' => 'required|integer'
        ]);

        // Ambil data dari database
        $logs = OrderApprovalLog::with('user')->where('order_id', $validated['order_id'])->get();

        // Ubah format datetime agar lebih enak dibaca
        $logs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'order_id' => $log->order_id,
                'status' => $log->status,
                'detail' => $log->detail,
                'user' => $log->user->name ?? null,
                'attachment' => $log->attachment ? asset('storage/' . $log->attachment) : null,
                'created_at' => Carbon::parse($log->created_at)
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($log->updated_at)
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $logs,
        ]);
    }
}
