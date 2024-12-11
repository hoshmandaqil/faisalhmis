@extends('layouts.master')

@section('page_title')
    Payroll Payment Report
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                <div class="search-box">
                    <form action="{{ url('searchPO') }}" method="post">
                        @csrf
                        <input type="text" name="search" class="search-query" value="{{ request('search') }}"
                            placeholder="Search Income ...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>
            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection
@section('content')
    <!-- Row start -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif

    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table id="scrollVertical" class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Slip No</th>
                            <th>Pement Date</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Employee F/Name</th>
                            <th>Employee Position</th>
                            <th>Present Days</th>
                            <th>Gross Salary</th>
                            <th>Tax</th>
                            <th>Net Salary</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <x-general.no-record :data="$payrollPayments" />
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach ($payrollPayments as $payment)
                            @php
                                $blance = $payment->payroll->items->where('employee_id', $payment->employee->id)->sum('net_salary') - $payment->amount;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }} </td>
                                <td>{{ $payment->slip_no }}</td>
                                <td>{{ $payment->payment_date }}</td>
                                <td>{{ $payment->employee->id }}</td>
                                <td>{{ $payment->employee->first_name }}{{ $payment->employee->last_name }}</td>
                                <td>{{ $payment->employee->father_name }}</td>
                                <td>{{ $payment->employee->position }}</td>
                                <td>{{ number_format($payment->payroll->items->where('employee_id', $payment->employee->id)->sum('present_days')) }}
                                    Days</td>
                                <td>{{ number_format($payment->payroll->items->where('employee_id', $payment->employee->id)->sum('gross_salary')) }}
                                    AF</td>
                                <td>{{ number_format($payment->payroll->items->where('employee_id', $payment->employee->id)->sum('tax')) }}
                                    AF</td>
                                <td>{{ number_format($payment->payroll->items->where('employee_id', $payment->employee->id)->sum('net_salary')) }}
                                    AF</td>
                                <td>{{ number_format($payment->amount) }} AF</td>
                                <td>{{ number_format($blance ) }}
                                    AF</td>
                                <td>
                                    @if ($blance == 0)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $totalAmount += $payment->amount;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                            <td><strong>{{ number_format($totalAmount) }} AF</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
