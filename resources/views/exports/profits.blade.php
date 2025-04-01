<div class="title pb-3 text-center">
    <div class="fw-bold text-uppercase fs-6">
        Triananda Fajar Ramadhan
    </div>
    <div>Alamat: Desa Kedungombo, Kec. Tengaran, Kab. Semarang</div>
    <div>Telp: 0857-9087-9087</div>
</div>

<table class="table table-bordered w-100">
    <thead class="bg-light">
        <tr>
            <th scope="col">Date</th>
            <th scope="col">Invoice</th>
            <th scope="col" class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($profits as $profit)
        <tr>
            <td>{{ \Carbon\Carbon::parse($profit->created_at)->format('d M Y') }}</td>
            <td>{{ $profit->transaction->invoice }}</td>
            <td class="text-end">{{ number_format($profit->total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="fw-bold bg-light">
            <td colspan="2" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
