<div class="container">
    <h2>Purchase Order Details</h2>
    <div class="row">
        <div class="col-md-6">
            <p><strong>PO Number:</strong> {{ $po->id }}</p>
            <p><strong>Requested By:</strong> {{ $po->po_by }}</p>
            <p><strong>Date:</strong> {{ $po->date }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> {{ $po->status() }}</p>
            <p><strong>Total Amount:</strong> {{ number_format($po->total_amount, 2) }} AF</p>
            <p><strong>Remarks:</strong> {{ $po->remarks }}</p>
        </div>
    </div>
    <h3>Items</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->amount * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
