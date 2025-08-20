<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /* Tampilan di layar */
        body {
            background: #eee;
            margin: 0;
            padding: 20px;
            font-family: sans-serif;
        }

        .a4-sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .no-border td {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .highlight {
            color: red;
            font-weight: bold;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .a4-sheet {
                box-shadow: none;
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }

            @page {
                size: A4;
                margin: 20mm;
            }

            body::before,
            body::after {
                display: none !important;
                content: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="a4-sheet">
        <table class="no-border">
            <tr>
                <td>
                    <img src="{{ '/'.$order->company->logo ?? 'https://media.istockphoto.com/id/2173059563/vector/coming-soon-image-on-white-background-no-photo-available.jpg?s=612x612&w=0&k=20&c=v0a_B58wPFNDPULSiw_BmPyhSNCyrP_d17i2BPPyDTk=' }}" width="80">
                </td>
                <td>{{$order->company->name}}</td>
                <td class="title">INVOICE</td>
            </tr>
        </table>

        <table class="no-border">
            <tr>
                <td></td>
                <td style="padding-left: 65px">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="text-align: left; width: 40%; border: none;"><strong>Date</strong></td>
                            <td style="width: 5%; border: none;">:</td>
                            <td style="text-align: left; border: none;">{{ $order->created_at->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: none;"><strong>Invoice No</strong></td>
                            <td style="border: none;">:</td>
                            <td style="text-align: left; border: none;">{{ $order->invoice_no }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: none;"><strong>Due Date</strong></td>
                            <td style="border: none;">:</td>
                            <td style="text-align: left; border: none;">{{ $order->created_at->addDays(10)->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>INVOICE TO:</strong><br>
                    {{ $order->customer['name'] }}<br>
                    {{ $order->customer['address'] }}<br>
                    {{ $order->customer['phone'] }}
                </td>
                <td style="padding-left: 70px">
                    <strong>DELIVER TO:</strong><br>
                    {{ $order->shipping_to }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_phone }}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr class="bold">
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td>Meters</td>
                    <td class="text-right">{{ number_format($item->unit_selling_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->selling_total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                @foreach ($order->orderAdditional as $item)
                <tr>
                    <td>{{ $item->detail }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td>Pcs</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <tr>
                <td style="width: 70%;">
                    <strong>Catatan:</strong><br>
                    {{-- {!! nl2br(e("mantap deh dah bisa")) !!} --}}
                </td>
                <td>
                    <table>
                        <tr>
                            <td>Sub Total</td>
                            <td class="text-right">{{ number_format($order->total_price_before_disc, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">{{ number_format($order->discount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bold">
                            <td>Total Due</td>
                            <td class="text-right highlight">Rp. {{ number_format($order->final_price, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p>
            <strong>PEMBAYARAN:</strong><br>
            BANK: {{$order->company->bank_name}}<br>
            No. Rekening: {{$order->company->bank_account_number}}<br>
            A/N: {{$order->company->bank_account_holder}}
        </p>

        <p>
            <strong>TERMS & INSTRUCTIONS:</strong><br>
            1. BARANG TIDAK BISA DITUKAR/DIKEMBALIKAN<br>
            2. KHUSUS SAJADAH ROLL BILA ADA CACAT (KURANG UKURAN), BOLEH DITUKAR JIKA BELUM DIPOTONG/DIPASANG<br>
            3. TIDAK BISA KEMBALI UANG
        </p>
    </div>
</body>

</html>