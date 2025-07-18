<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        .no-border td { border: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .title { font-size: 20px; font-weight: bold; text-align: right; }
        .bold { font-weight: bold; }
        .highlight { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <table class="no-border">
        <tr>
            <td><img src="{{ public_path('logo.png') }}" width="80"></td>
            <td class="title">INVOICE</td>
        </tr>
    </table>

    <table class="no-border">
        <tr>
            <td>
                <strong>INVOICE TO:</strong><br>
                {{ $customer['name'] }}<br>
                {{ $customer['address'] }}<br>
                {{ $customer['phone'] }}
            </td>
            <td>
                <strong>DELIVER TO:</strong><br>
                {{ $delivery['name'] }}<br>
                {{ $delivery['address'] }}<br>
                {{ $delivery['phone'] }}
            </td>
            <td>
                <strong>Date:</strong> {{ $date }}<br>
                <strong>Invoice No:</strong> {{ $invoice_no }}<br>
                <strong>Due Date:</strong> {{ $due_date }}
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
            @foreach ($items as $item)
            <tr>
                <td>{{ $item['desc'] }}</td>
                <td class="text-center">{{ $item['qty'] }}</td>
                <td>{{ $item['unit'] }}</td>
                <td class="text-right">{{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="no-border">
        <tr>
            <td>
                <strong>Catatan:</strong><br>
                {!! nl2br(e($notes)) !!}
            </td>
            <td>
                <table>
                    <tr><td>Sub Total</td><td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td></tr>
                    <tr><td>Discount</td><td class="text-right">{{ number_format($discount, 0, ',', '.') }}</td></tr>
                    <tr><td>Tax</td><td class="text-right">{{ number_format($tax, 0, ',', '.') }}</td></tr>
                    <tr class="bold"><td>Total Due</td><td class="text-right highlight">Rp. {{ number_format($total_due, 0, ',', '.') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p>
        <strong>PEMBAYARAN:</strong><br>
        BANK: BCA<br>
        No. Rekening: 261.776.0777<br>
        A/N: PT GALERI KARPETINDO INTERNUSA
    </p>

    <p>
        <strong>TERMS & INSTRUCTIONS:</strong><br>
        1. BARANG TIDAK BISA DITUKAR/DIKEMBALIKAN<br>
        2. KHUSUS SAJADAH ROLL BILA ADA CACAT (KURANG UKURAN), BOLEH DITUKAR JIKA BELUM DIPOTONG/DIPASANG<br>
        3. TIDAK BISA KEMBALI UANG
    </p>

</body>
</html>