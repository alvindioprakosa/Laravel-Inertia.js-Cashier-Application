<div class="title" style="padding-bottom: 13px;">
    <div style="text-align: center; text-transform: uppercase; font-size: 15px;">
        Triananda Fajar Ramadhan
    </div>
    <div style="text-align: center;">
        Alamat: Desa Kedungombo, Kec. Tengaran, Kab. Semarang
    </div>
    <div style="text-align: center;">
        Telp: 0857-9087-9087
    </div>
</div>

<table style="width: 100%;">
    <thead>
        <tr style="background-color: #e6e6e7;">
            <th scope="col">Date</th>
            <th scope="col">Invoice</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($profits as $profit)
        <tr>
            <td>{{ $profit->created_at }}</td>
            <td>{{ $profit->transaction->invoice }}</td>
            <td class="text-end">{{ formatPrice($profit->total) }}</td>
        </tr>
        @endforeach
        <tr style="background-color: #e6e6e7;">
            <td colspan="2" class="text-end fw-bold">TOTAL</td>
            <td class="text-end fw-bold">{{ formatPrice($total) }}</td>
        </tr>
    </tbody>
</table>
