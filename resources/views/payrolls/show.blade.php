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
        <p><strong>Total Amount:</strong> {{ $payroll->total_amount }} AFN</p>
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
                        <td>{{ $item->employee->name }}</td>
                        <td>{{ $item->gross_salary }} AFN</td>
                        <td>{{ $item->net_salary }} AFN</td>
                        <td>{{ $item->bonus }} AFN</td>
                        <td>{{ $item->tax }} AFN</td>
                        <td>{{ $item->amount }} AFN</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back to Payrolls</a>
@endsection
