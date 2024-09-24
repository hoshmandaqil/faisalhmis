@extends('layouts.master')

@section('page_title')
    Edit Payroll
@endsection

@section('content')
    <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row align-items-end">
            <div class="form-group col-md-4">
                <label for="start_date">Start of Month</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required
                    value="{{ $payroll->start_date }}">
            </div>
            <div class="form-group col-md-4">
                <label for="end_date">End of Month</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required
                    value="{{ $payroll->end_date }}">
            </div>
            <div class="form-group col-md-4">
                <label for="official_days">Official Days</label>
                <input type="number" name="official_days" id="official_days" class="form-control" required
                    value="{{ $payroll->official_days }}" step="0.1">
            </div>
        </div>

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
                    <input type="hidden" name="employees[{{ $item->employee_id }}][employee_id]" value="{{ $item->employee_id }}">
                    <tr>
                        <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                        <td>{{ $item->employee->employeeCurrentSalary->salary_amount }} AF</td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][present_days]"
                                class="form-control present-days" required value="{{ $item->present_days }}" style="max-width: 100px" step="0.1">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][bonus]"
                                class="form-control bonus" value="{{ $item->bonus }}" step="0.1">
                        </td>
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
                                    @foreach ($item->additional_payments as $summary)
                                        <tr>
                                            <td class="pt-2 pb-2">{{ $summary['main_lab_department'] }}</td>
                                            <td class="pt-2 pb-2 text-nowrap">{{ $summary['number_of_tests'] }}</td>
                                            <td class="pt-2 pb-2 text-nowrap">{{ $summary['total_price'] }} AF</td>
                                            <td class="pt-2 pb-2 text-nowrap">{{ $summary['payable'] }} AF</td>
                                            <td class="pt-2 pb-2 text-nowrap">{{ $summary['tax'] }} AF</td>
                                            <td class="pt-2 pb-2 text-nowrap">
                                                {{ $summary['payable'] - $summary['tax'] }} AF</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td class="pb-2 pt-2" colspan="2"><strong>Total</strong></td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ collect($item->additional_payments)->sum('total_price') }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ collect($item->additional_payments)->sum('payable') }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong>{{ collect($item->additional_payments)->sum('tax') }} AF</strong>
                                        </td>
                                        <td class="pb-2 pt-2 text-nowrap">
                                            <strong class="tests-net-payable">{{ collect($item->additional_payments)->sum('payable') - collect($item->additional_payments)->sum('tax') }} AF</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <input type="hidden" name="employees[{{ $item->employee_id }}][additional_payments]"
                                class="form-control additional-payments" required
                                value="{{ json_encode($item->additional_payments) }}">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][tax]" class="form-control tax"
                                readonly value="{{ $item->tax }}">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][gross_salary]"
                                class="form-control gross-salary" readonly value="{{ $item->gross_salary }}">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][net_payable]"
                                class="form-control net-payable" readonly value="{{ $item->net_salary }}">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee_id }}][grand_total]"
                                class="form-control grand-total" readonly value="{{ $item->grand_total }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-dark text-white">
                <tr>
                    <th style="vertical-align: middle">Totals</th>
                    <th colspan="2"><strong class="mb-2 d-inline-block">Base Salary:</strong><br><span id="total-salary">{{ number_format($payroll->items->sum('gross_salary') - $payroll->items->sum('bonus'), 2) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Bonus:</strong><br><span id="total-bonus">{{ number_format($payroll->items->sum('bonus'), 2) }}</span> AF</th>
                    <th></th>
                    <th><strong class="mb-2 d-inline-block">Tax:</strong><br><span id="total-tax">{{ number_format($payroll->items->sum('tax'), 2) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Gross Salary:</strong><br><span id="total-gross-salary">{{ number_format($payroll->items->sum('gross_salary'), 2) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Net Payable:</strong><br><span id="total-payable">{{ number_format($payroll->items->sum('net_salary'), 2) }}</span> AF</th>
                    <th><strong class="mb-2 d-inline-block">Grand Total:</strong><br><span id="total-grand-total">{{ number_format($payroll->items->sum('grand_total'), 2) }}</span> AF</th>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">Update Payroll</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            function calculateTax(salary) {
                let tax = 0;
                if (salary > 5000 && salary <= 12500) {
                    tax = Math.round((salary - 5000) * 0.02);
                } else if (salary > 12500 && salary <= 100000) {
                    tax = Math.round((salary - 12500) * 0.1) + 150;
                } else if (salary > 100000) {
                    tax = Math.round((salary - 100000) * 0.2) + 8900;
                }
                return tax;
            }

            function updateTotals() {
                let totalSalary = 0;
                let totalTax = 0;
                let totalBonus = 0;
                let totalPayable = 0;
                let totalGrandTotal = 0;
                let totalGrossSalary = 0;

                const officialDays = parseFloat($('#official_days').val()) || 30;

                $('tbody > tr').each(function() {
                    const baseSalary = parseFloat($(this).find('td').eq(1).text().replace(/[^\d.-]/g, '')) || 0;
                    const presentDays = parseFloat($(this).find('input.present-days').val()) || 0;
                    const bonus = parseFloat($(this).find('input.bonus').val()) || 0;
                    const testsNetPayable = parseFloat($(this).find('.tests-net-payable').text().replace(/[^\d.-]/g, '')) || 0;

                    const adjustedSalary = (baseSalary / officialDays) * presentDays;
                    const grossSalary = adjustedSalary + bonus;
                    const tax = calculateTax(grossSalary);
                    const netPayable = grossSalary - tax;
                    const grandTotal = netPayable + testsNetPayable;

                    $(this).find('input.tax').val(tax.toFixed(2));
                    $(this).find('input.gross-salary').val(grossSalary.toFixed(2));
                    $(this).find('input.net-payable').val(netPayable.toFixed(2));
                    $(this).find('input.grand-total').val(grandTotal.toFixed(2));

                    totalSalary += adjustedSalary;
                    totalTax += tax;
                    totalBonus += bonus;
                    totalPayable += netPayable;
                    totalGrandTotal += grandTotal;
                    totalGrossSalary += grossSalary;
                });

                $('#total-salary').text(formatNumber(totalSalary));
                $('#total-tax').text(formatNumber(totalTax));
                $('#total-bonus').text(formatNumber(totalBonus));
                $('#total-payable').text(formatNumber(totalPayable));
                $('#total-grand-total').text(formatNumber(totalGrandTotal));
                $('#total-gross-salary').text(formatNumber(totalGrossSalary));
            }

            $('input.present-days, input.bonus, #official_days').on('input', updateTotals);

            // Initial calculation of totals
            updateTotals();
        });

        function formatNumber(num) {
            return num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/persianDatepicker/css/persianDatepicker-default.css') }}" />
    <style>
        table td {
            vertical-align: top !important;
        }

        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }
    </style>
@endsection