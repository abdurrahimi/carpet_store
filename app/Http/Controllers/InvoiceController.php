<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Request $request)
    {
        // Contoh data dummy, nanti kamu bisa ambil dari DB
        $invoiceData = [
            'date' => now()->format('d-m-Y'),
            'invoice_no' => 'INV-2025-001',
            'due_date' => now()->addDays(7)->format('d-m-Y'),
            'customer' => [
                'name' => 'PT Sukses Makmur',
                'address' => 'Jl. Raya Kemayoran No.99',
                'phone' => '081234567890',
            ],
            'delivery' => [
                'name' => 'PT Sukses Makmur (Gudang)',
                'address' => 'Jl. Pergudangan 88',
                'phone' => '085566778899',
            ],
            'items' => [
                ['desc' => 'Dubai Polos Hijau [14 x 6 m]', 'qty' => 72, 'unit' => 'Meter', 'price' => 150000, 'total' => 10800000],
                ['desc' => 'Obrasan', 'qty' => 6, 'unit' => 'Pcs', 'price' => 25000, 'total' => 150000],
                ['desc' => 'Pemasangan', 'qty' => 2, 'unit' => 'Meter', 'price' => 0, 'total' => 0],
                ['desc' => 'Sambungan', 'qty' => 2, 'unit' => 'Pcs', 'price' => 50000, 'total' => 100000],
                ['desc' => 'Estimasi Ongkos Kirim', 'qty' => 1, 'unit' => '', 'price' => 0, 'total' => 0],
            ],
            'notes' => '',
            'subtotal' => 11050000,
            'discount' => 0,
            'tax' => 0,
            'total_due' => 11050000,
        ];

        $pdf = Pdf::loadView('invoice-pdf', $invoiceData)
            ->setPaper('A4');

        return $pdf->download("invoice-{$invoiceData['invoice_no']}.pdf");
    }

    public function downloadQuotation(Request $request)
    {
        $quotationData = [
            'date' => now()->format('d-m-Y'),
            'quotation_no' => 'QTN-2025-045',
            'valid_until' => now()->addDays(14)->format('d-m-Y'),
            'customer' => [
                'name' => 'CV Maju Jaya',
                'address' => 'Jl. Industri No. 10, Bekasi',
                'phone' => '085612345678',
            ],
            'items' => [
                ['desc' => 'Karpet Dubai Hijau (14x6m)', 'qty' => 72, 'unit' => 'Meter', 'price' => 150000, 'total' => 10800000],
                ['desc' => 'Obrasan', 'qty' => 6, 'unit' => 'Pcs', 'price' => 25000, 'total' => 150000],
            ],
            'subtotal' => 10950000,
            'discount' => 0,
            'tax' => 0,
            'total_due' => 10950000,
            'notes' => 'Harga belum termasuk biaya kirim dan pemasangan.',
        ];

        $pdf = Pdf::loadView('quotation-pdf', $quotationData)->setPaper('A4');

        return $pdf->download("quotation-{$quotationData['quotation_no']}.pdf");
    }
}
