@extends('layouts.master')

@section('page_title')
    Payroll Details
@endsection

@section('content')
    <div class="mb-4">
        <h4>Payroll Information</h4>
        <p><strong>Start Date:</strong> {{ $payroll->start_date }}</p>
        <p><strong>End Date:</strong> {{ $payroll->end_date }}</p>
        <p><strong>Official Days:</strong> {{ $payroll->official_days }}</p>
        <p><strong>Status:</strong> {{ ucfirst($payroll->status) }}</p>
    </div>

    <div>
        <h4>Payroll Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th class="text-nowrap">Base Salary</th>
                    <th class="text-nowrap">Present Days</th>
                    <th>Bonus</th>
                    <th class="text-nowrap">Additional Payments</th>
                    <th>Tax</th>
                    <th class="text-nowrap">Gross Salary</th>
                    <th class="text-nowrap">Net Salary Payable</th>
                    <th class="text-nowrap">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll->items as $item)
                    <tr>
                        <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                        <td>{{ number_format($item->base_salary) }} AF</td>
                        <td>{{ $item->present_days }}</td>
                        <td>{{ number_format($item->bonus) }} AF</td>
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
                                    @if(is_array($additionalPayments))
                                        @foreach ($additionalPayments as $summary)
                                            <tr>
                                                <td class="pt-2 pb-2">{{ $summary['main_lab_department'] ?? 'N/A' }}</td>
                                                <td class="pt-2 pb-2 text-nowrap">{{ $summary['number_of_tests'] ?? 0 }}</td>
                                                <td class="pt-2 pb-2 text-nowrap">{{ number_format($summary['total_price'] ?? 0) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">{{ number_format($summary['payable'] ?? 0) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">{{ number_format($summary['tax'] ?? 0) }} AF</td>
                                                <td class="pt-2 pb-2 text-nowrap">
                                                    {{ number_format(($summary['payable'] ?? 0) - ($summary['tax'] ?? 0)) }} AF
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No additional payments data available</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td class="pb-2 pt-2" colspan="2"><strong>Total</strong></td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('total_price') : 0) }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('payable') : 0) }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('tax') : 0) }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ number_format(is_array($additionalPayments) ? collect($additionalPayments)->sum('payable') - collect($additionalPayments)->sum('tax') : 0) }} AF</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td>{{ number_format($item->tax) }} AF</td>
                        <td>{{ number_format($item->gross_salary) }} AF</td>
                        <td>{{ number_format($item->net_payable) }} AF</td>
                        <td>{{ number_format($item->grand_total) }} AF</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-dark text-white">
                <tr>
                    <th colspan="2" style="vertical-align: middle">Totals</th>
                    <th><strong class="mb-2 d-inline-block">Salary:</strong><br><span id="total-salary">{{ number_format($payroll->items->sum('gross_salary')) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Tax:</strong><br><span id="total-tax">{{ number_format($payroll->items->sum('tax')) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Bonus:</strong><br><span id="total-bonus">{{ number_format($payroll->items->sum('bonus')) }}</span> AF</th>
                    <th colspan="3"><strong class="mb-2 d-inline-block">Payable:</strong><br><span id="total-payable">{{ number_format($payroll->items->sum('net_payable')) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Grand Total:</strong><br><span id="total-grand-total">{{ number_format($payroll->items->sum('grand_total')) }}</span> AF</th>
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
