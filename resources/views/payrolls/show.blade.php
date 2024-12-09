@extends('layouts.master')

@section('page_title')
    Payroll Details
@endsection

@section('content')
    <div class="mb-4">
        <h4>Payroll Information</h4>
        <div class="row mt-4">
            <div class="col-md-3">
                <h5><strong>Start Date:</strong> {{ $payroll->start_date }}</h5>
            </div>
            <div class="col-md-3">
                <h5><strong>End Date:</strong> {{ $payroll->end_date }}</h5>
            </div>
            <div class="col-md-3">
                <h5><strong>Official Days:</strong> {{ $payroll->official_days }}</h5>
            </div>
            <div class="col-md-3">
                <h5><strong>Status:</strong> {{ ucfirst($payroll->status) }}</h5>
            </div>
        </div>
    </div>

    <div>
        <h4>Payroll Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th class="text-nowrap">Gross Salary</th>
                    <th class="text-nowrap">Present Days</th>
                    <th>Night Duty/Monibox</th>
                    <th class="text-nowrap">Additional Payments</th>
                    <th>Tax</th>
                    {{-- <th class="text-nowrap">Gross Salary</th> --}}
                    <th class="text-nowrap">Net Salary Payable</th>
                    <th class="text-nowrap">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll->items as $item)
                    <tr>
                        <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                        <td>{{ number_format($item->employee->employeeCurrentSalary->salary_amount, 2) }} AF</td>
                        <td>{{ number_format($item->present_days, 1) }}</td>
                        <td>{{ number_format($item->bonus, 2) }} AF</td>
                        <td>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-secondary">
                                        <th class="pt-2 pb-2">Department</th>
                                        <th class="pt-2 pb-2 text-nowrap">Tests</th>
                                        <th class="pt-2 pb-2 text-nowrap">Total Price</th>
                                        <th class="pt-2 pb-2">Gross</th>
                                        <th class="pt-2 pb-2">Tax</th>
                                        <th class="pt-2 pb-2 text-nowrap">Net Payable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $additionalPayments = json_decode($item->additional_payments, true);
                                    @endphp
                                    @if (is_array($additionalPayments))
                                        @foreach ($additionalPayments as $summary)
                                            <tr>
                                                <td class="pt-2 pb-2">{{ $summary['main_lab_department'] ?? 'N/A' }}</td>
                                                <td class="pt-2 pb-2 text-nowrap">{{ $summary['number_of_tests'] ?? 0 }}
                                                </td>
                                                <td class="pt-2 pb-2 text-nowrap">
                                                    {{ number_format($summary['total_price'] ?? 0, 2) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">
                                                    {{ number_format($summary['payable'] ?? 0, 2) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">
                                                    {{ number_format($summary['tax'] ?? 0, 2) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">
                                                    {{ number_format(($summary['payable'] ?? 0) - ($summary['tax'] ?? 0), 2) }}
                                                    AF
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No additional payments data available
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td class="pb-2 pt-2" colspan="2"><strong>Total</strong></td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('total_price') : 0, 2) }}
                                                AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('payable') : 0, 2) }}
                                                AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('tax') : 0, 2) }}
                                                AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('payable') - collect($additionalPayments)->sum('tax') : 0, 2) }}
                                                AF</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td>
                            {{ number_format($item->tax, 2) }} AF
                        </td>
                        <td>{{ number_format($item->net_salary, 2) }} AF</td>
                        <td>
                            @php
                                $payment = $payroll->payments->firstWhere('employee_id', $item->employee->id);
                            @endphp
                            @if ($payment && $payment->payment_method == 1)
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-dark text-white">
                <tr>
                    <th style="vertical-align: middle">Totals</th>
                    <th><strong class="mb-2 d-inline-block">Gross Salary:</strong><br>
                        <span
                            id="total-salary">{{ number_format(
                                $payroll->items->sum(function ($item) {
                                    return $item->employee->employeeCurrentSalary->salary_amount;
                                }),
                                2,
                            ) }}</span>

                    </th>
                    <th><strong class="mb-2 d-inline-block">Bonus:</strong><br><span
                            id="total-bonus">{{ number_format($payroll->items->sum('bonus'), 2) }}</span> AF</th>
                    <th></th>
                    <th></th>
                    <th>
                        <strong class="mb-2 d-inline-block">Tax:</strong><br>
                        <span id="total-tax">{{ number_format($payroll->items->sum('tax'), 2) }}</span> AF
                    </th>

                    {{-- <th><strong class="mb-2 d-inline-block">Gross Salary:</strong><br><span id="total-gross-salary">{{ number_format($payroll->items->sum('gross_salary'), 2) }}</span> AF</th> --}}
                    <th><strong class="mb-2 d-inline-block">Payable:</strong><br><span
                            id="total-payable">{{ number_format($payroll->items->sum('net_salary'), 2) }}</span> AF</th>
                    {{-- <th><strong class="mb-2 d-inline-block">Grand Total:</strong><br><span
                            id="total-grand-total">{{ number_format($payroll->items->sum('grand_total'), 2) }}</span> AF
                    </th> --}}
                </tr>
            </tfoot>
        </table>
    </div>

    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back to Payrolls</a>
@endsection

@section('styles')
    <style>
        table td {
            vertical-align: top !important;
        }
    </style>
@endsection
