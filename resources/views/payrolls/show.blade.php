@extends('layouts.master')

@section('page_title')
    Payroll Details
@endsection

@section('page-action')
@endsection

@section('content')
    <div class="mb-4">
        <h4>Payroll Information</h4>
        <p><strong>Date:</strong> {{ $payroll->payroll_date }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($payroll->total_amount) }} AFN</p>
        <p><strong>Status:</strong> {{ ucfirst($payroll->status) }}</p>
    </div>

    <div>
        <h4>Payroll Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Gross Salary</th>
                    <th>Net Salary</th>
                    <th>Bonus</th>
                    <th>Tax</th>
                    <th>Net Payable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll->items as $item)
                    <tr>
                        <td>{{ $item->employee->first_name }}</td>
                        <td>{{ number_format($item->gross_salary) }} AFN</td>
                        <td>{{ number_format($item->net_salary) }} AFN</td>
                        <td>{{ number_format($item->bonus) }} AFN</td>
                        <td>{{ number_format($item->tax) }} AFN</td>
                        <td>{{ number_format($item->amount) }} AFN</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back to Payrolls</a>
@endsection
