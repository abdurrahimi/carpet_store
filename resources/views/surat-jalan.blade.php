<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Surat Jalan</title>
  <style>
    body {
      background: #ccc;
      display: flex;
      justify-content: center;
      padding: 40px 0;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .a4-page {
      background: white;
      width: 210mm;
      height: 297mm;
      padding: 20mm;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      box-sizing: border-box;
      color: #000;
    }

    h1.header {
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .toko-box {
      border: 1px solid black;
      border-radius: 10px;
      padding: 10px;
      float: right;
      text-align: right;
      width: 250px;
      margin-top: -60px;
    }

    .toko-box strong {
      font-size: 16px;
      color: red;
    }

    .info {
      margin-bottom: 10px;
    }

    .info span {
      display: inline-block;
      min-width: 100px;
    }

    .kepada-box {
      border: 1px solid black;
      padding: 10px;
      width: 300px;
      float: right;
      margin-top: 20px;
      margin-bottom: 40px;
    }

    .kepada-box p {
      margin: 0;
      line-height: 1.4;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid black;
      padding: 8px 10px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .note {
      border: 1px solid black;
      border-radius: 10px;
      padding: 10px;
      margin-top: 10px;
      color: red;
    }

    .signatures {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
    }

    .signatures div {
      text-align: center;
      width: 19%;
    }

    .signatures p {
      margin-bottom: 60px;
    }

    /* Print Style */
    @media print {
      body {
        background: white;
        padding: 0;
      }

      .a4-page {
        box-shadow: none;
        width: auto;
        height: auto;
        margin: 0;
        padding: 0;
      }
    }
  </style>
</head>
<body>
  <div class="a4-page">
    <h1 class="header">SURAT JALAN</h1>

    <div class="toko-box">
      <strong>(NAMA TOKO)</strong><br>
      (NO TELP TOKO)
    </div>

    <div class="info">
      <div><span>Tanggal</span>: <span style="color: red;">26 Mei 2025</span></div>
      <div><span>No. Invoice</span>: <span style="color: red;">112/2505/01853</span></div>
      <div><span>No. Delivery</span>: <span style="color: red;">112/2505/0214</span></div>
      <div><span>Sales</span>: <span style="color: red;">Dewi</span></div>
    </div>

    <div class="kepada-box">
      <strong>Kepada Yth :</strong>
      <p>Ibu Rosa<br>
         TK. ROSA<br>
         JL. IR H. JUANDA GUMAWANG BK 10<br>
         BELITANG, OKU TIMUR (DEKAT POLSEK)<br>
         <span style="color: red;">0813 - 7375 - 8075</span></p>
    </div>

    <table>
      <thead>
        <tr>
          <th>NO</th>
          <th>ITEM DESCRIPTION</th>
          <th>SIZE</th>
          <th>QTY</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>CONCORD</td>
          <td>170 x 230</td>
          <td>10</td>
        </tr>
        <tr>
          <td>2</td>
          <td>ALMAYA</td>
          <td>230 x 310</td>
          <td>10</td>
        </tr>
      </tbody>
    </table>

    <div class="note">
      Note: Kirim dengan Ekspedisi Fajar Indah, total 2 Ball
    </div>

    <div class="signatures">
      <div>
        Dibuat oleh,<br><br>
        (....................)
      </div>
      <div>
        Checker,<br><br>
        (....................)
      </div>
      <div>
        Pengirim,<br><br>
        (....................)
      </div>
      <div>
        Finance,<br><br>
        (....................)
      </div>
      <div>
        Penerima,<br><br>
        (....................)
      </div>
    </div>
  </div>
</body>
</html>
