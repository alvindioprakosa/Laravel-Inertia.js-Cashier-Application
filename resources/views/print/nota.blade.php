<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
<head>
    <title>Nota Pembelian</title>
    <style>
        html { font-family: Verdana; }
        .content { width: 80mm; font-size: 10px; padding: 20px; }
        .title, .thanks, .azost { text-align: center; }
        .title div { text-transform: uppercase; font-size: 15px; }
        .separate-line { border-top: 1px dashed #000; height: 1px; margin: 5px 0; }
        .transaction-table { width: 100%; font-size: 10px; }
        .transaction-table .final-price { text-align: right; }
        @media print {
            @page { width: 80mm; margin: 0; }
        }
    </style>
    <script> window.print(); </script>
</head>
<body>
    <div class="content">
        <div class="title">
            <div>Yan Afriyoko</div>
            <div>Alamat: Desa Gedangalas, Kec. Gajah, Kab. Demak</div>
            <div>Telp: 0857-9087-9089</div>
        </div>

        <div class="separate-line"></div>
        <table class="transaction-table">
            <tr><td>TANGGAL</td><td>:</td><td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</td></tr>
            <tr><td>FAKTUR</td><td>:</td><td>{{ $transaction->invoice }}</td></tr>
            <tr><td>KASIR</td><td>:</td><td>{{ $transaction->cashier->name ?? '-' }}</td></tr>
            <tr><td>PEMBELI</td><td>:</td><td>{{ $transaction->customer->name ?? 'Umum' }}</td></tr>
        </table>

        <div class="separate-line"></div>
        <table class="transaction-table">
            <tr><td>PRODUK</td><td>QTY</td><td class="final-price">HARGA</td></tr>
            <tr><td colspan="3"><div class="separate-line"></div></td></tr>
            @foreach ($transaction->details as $item)
            <tr>
                <td>{{ $item->product->title }}</td>
                <td style="text-align: center">{{ $item->qty }}</td>
                <td class="final-price">{{ formatPrice($item->price) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="3"><div class="separate-line"></div></td></tr>
            <tr><td>SUB TOTAL</td><td>:</td><td class="final-price">{{ formatPrice($transaction->grand_total) }}</td></tr>
            <tr><td>DISKON</td><td>:</td><td class="final-price">{{ formatPrice($transaction->discount) }}</td></tr>
            <tr><td>TUNAI</td><td>:</td><td class="final-price">{{ formatPrice($transaction->cash) }}</td></tr>
            <tr><td>KEMBALI</td><td>:</td><td class="final-price">{{ formatPrice($transaction->change) }}</td></tr>
        </table>

        <div class="thanks">=====================================</div>
        <div class="azost">TERIMA KASIH<br>ATAS KUNJUNGAN ANDA</div>
    </div>
</body>
</html>
