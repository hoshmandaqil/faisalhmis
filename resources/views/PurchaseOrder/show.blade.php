<div class="container">
    <h6>Purchase Order Details</h6>
    <div class="row">
        <div class="col-md-6">
            <p><strong>PO Number:</strong> {{ $po->id }}</p>
            <p><strong>Requested By:</strong> {{ $po->po_by }}</p>
            <p><strong>Date:</strong> {{ $po->date }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> {{ $po->status() }}</p>
            <p><strong>Total Amount:</strong> {{ number_format($po->total_amount, 2) }} AF</p>
            <div><strong>Remarks:</strong>
                <p dir="rtl">{{ $po->remarks }}</p>
            </div>
        </div>
    </div>
    <h6>Items</h6>
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
            @foreach ($po->items as $item)
                <tr>
                    <td dir="rtl">{{ $item->description }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->amount * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Grand Total</th>
                <th>
                    {{ number_format(
                        $po->items->sum(function ($item) {
                            return $item->amount * $item->quantity;
                        }),
                        2,
                    ) }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>
