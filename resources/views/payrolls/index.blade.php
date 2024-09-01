@extends('layouts.master')

@section('page_title')
    Payrolls
@endsection

@section('page-action')
    <a href="{{ route('payrolls.create') }}" class="btn btn-primary mb-3">Generate Payroll</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Payroll Date</th>
                <th>Official Days</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->id }}</td>
                    <td>{{ $payroll->payroll_date }}</td>
                    <td>{{ $payroll->official_days }}</td>
                    <td>{{ number_format($payroll->total_amount) }}</td>
                    <td>{{ ucfirst($payroll->status) }}</td>
                    <td>
                        <a href="{{ route('payrolls.show', $payroll->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
