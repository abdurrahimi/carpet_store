<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Surat Jalan</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 2cm;
    }

    @media print {
      body {
        width: 297mm;
        height: 210mm;
        margin: 0;
        padding: 0;
        background: white;
        -webkit-print-color-adjust: exact;
      }

      .page {
        page-break-after: always;
      }
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fff;
      color: #000;
      margin: 0;
      padding: 0;
    }

    .page {
      width: 297mm;
      min-height: 210mm;
      padding: 20mm;
      margin: auto;
      box-sizing: border-box;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 2px solid #000;
      padding: 16px;
      border-radius: 12px;
    }

    .header h1 {
      font-size: 28px;
      margin: 0;
      font-weight: bold;
    }

    .store-info {
      text-align: right;
      font-size: 14px;
      color: #e63946;
      font-weight: bold;
    }

    .top-section {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      gap: 20px;
    }

    .section {
      font-size: 14px;
      flex: 1;
    }

    .section td {
      padding: 4px 8px;
      vertical-align: top;
    }

    .red {
      color: #e63946;
      font-weight: bold;
    }

    .recipient {
      border: 1px solid #000;
      padding: 12px;
      font-size: 14px;
      flex: 1;
    }

    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 40px;
      font-size: 14px;
    }

    table.data-table th,
    table.data-table td {
      border: 1px solid #000;
      padding: 8px;
      text-align: center;
    }

    table.data-table th {
      background-color: #f0f0f0;
    }

    .note {
      margin-top: 15px;
      border: 1px solid #000;
      border-radius: 8px;
      padding: 10px;
      font-size: 13px;
    }

    .note span {
      color: #e63946;
      font-weight: bold;
    }

    .signatures {
      margin-top: 50px;
      font-size: 14px;
      display: flex;
      justify-content: space-between;
    }

    .signatures div {
      text-align: center;
      flex: 1;
    }

    .signatures div:not(:last-child) {
      margin-right: 10px;
    }

    .sign-line {
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <div class="page">
    <div class="header">
      <h1>SURAT JALAN</h1>
      <div class="store-info">
        {{ $penjualan->store->name ?? ''}}<br />
        {{ $penjualan->store->phone ?? ''}}
      </div>
    </div>

    <div class="top-section">
      <table class="section">
        <tr>
          <td>Tanggal</td>
          <td class="red">: {{ $penjualan->created_at }}</td>
        </tr>
        <tr>
          <td>No. Invoice</td>
          <td class="red">: {{ $penjualan->invoice_no }}</td>
        </tr>
        <tr>
          <td>No. Delivery</td>
          <td class="red">: </td>
        </tr>
        <tr>
          <td>Sales</td>
          <td class="red">: </td>
        </tr>
      </table>

      <div class="recipient">
        <strong>Kepada Yth :</strong><br />
        {{ $penjualan->customer->name }}<br />
        {{ $penjualan->customer->address }}<br />
        <span class="red">+62{{ $penjualan->customer->phone }}</span><br />
      </div>
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th>NO</th>
          <th>ITEM DESCRIPTION</th>
          <th>SIZE</th>
          <th>QTY</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($penjualan->orderDetails as $product)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->dimensi }}</td>
            <td>{{ $product->qty }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="note">
      Note:<br />
      <span><br><br><br><br><br><br></span>
    </div>

    <div class="signatures">
      <div>
        Dibuat oleh,<br /><br />
        <div class="sign-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
      </div>
      <div>
        Checker,<br /><br />
        <div class="sign-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
      </div>
      <div>
        Pengirim,<br /><br />
        <div class="sign-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
      </div>
      <div>
        Finance,<br /><br />
        <div class="sign-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
      </div>
    </div>
  </div>
</body>
</html>
