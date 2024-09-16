@extends('layouts.master')

@section('page_title')
    Generate Payroll
@endsection

@section('page-action')
@endsection

@section('content')
    <form action="{{ route('payrolls.create') }}" method="GET" id="payrollDateForm">
        <div class="row align-items-end">
            <div class="form-group col-md-4">
                <label for="start_date">Start of Month</label>
                <input type="text" name="start_date" id="start_date" class="form-control persianDate" required
                    value="{{ request('start_date') }}">
            </div>
            <div class="form-group col-md-4">
                <label for="end_date">End of Month</label>
                <input type="text" name="end_date" id="end_date" class="form-control persianDate" required
                    value="{{ request('end_date') }}">
            </div>
            <div class="col-md-4 mb-3">
                <button type="submit" class="btn btn-primary">Generate Payroll</button>
            </div>
        </div>
    </form>

    @if (request('start_date') && request('end_date'))
        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" class="form-control persianDate" required
                value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" id="end_date" class="form-control persianDate" required
                value="{{ request('end_date') }}">
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="official_days">Official Days</label>
                    <input type="number" name="official_days" id="official_days" class="form-control" required
                        value="30">
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
                    @foreach ($employees as $employee)
                        <input type="hidden" name="employees[{{ $employee->id }}][employee_id]"
                            class="form-control present-days" required value="{{ $employee->id }}">
                        <tr>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->employeeCurrentSalary->salary_amount }} AF</td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][present_days]"
                                    class="form-control present-days" required style="max-width: 100px">
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][bonus]"
                                    class="form-control bonus">
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
                                            <th class="pt-2 pb-2 text-nowrap"">Net Payable</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->lab_tests_summary as $summary)
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
                                                <strong>{{ $employee->lab_tests_summary->sum('total_price') }} AF</strong>
                                            </td>
                                            <td class="pb-2 pt-2 text-nowrap">
                                                <strong>{{ $employee->lab_tests_summary->sum('payable') }}
                                                    AF</strong>
                                            </td>
                                            <td class="pb-2 pt-2 text-nowrap">
                                                <strong>{{ $employee->lab_tests_summary->sum('tax') }}
                                                    AF</strong></td>
                                            <td class="pb-2 pt-2 text-nowrap">
                                                <strong><span>{{ $employee->lab_tests_summary->sum('payable') - $employee->lab_tests_summary->sum('tax') }}</span>
                                                    AF</strong>
                                            </td>
                                            <input type="hidden" name="employees[{{ $employee->id }}][tests_net_payable]"
                                                class="tests-net-payable text-nowrap"
                                                value="{{ $employee->lab_tests_summary->sum('payable') - $employee->lab_tests_summary->sum('tax') }}">
                                        </tr>
                                    </tfoot>
                                </table>
                                <input type="hidden" name="employees[{{ $employee->id }}][additional_payments]"
                                    class="form-control additional-payments" required
                                    value="{{ $employee->lab_tests_summary }}">
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][tax]" class="form-control tax"
                                    readonly>
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][gross_salary]"
                                    class="form-control gross-salary" readonly>
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][net_payable]"
                                    class="form-control net-payable" readonly>
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][grand_total]"
                                    class="form-control grand-total" readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-dark text-white">
                    <tr>
                        <th colspan="2" style="vertical-align: middle">Totals</th>
                        <th><strong class="mb-2 d-inline-block">Salary:</strong><br><span id="total-salary">0</span> AF
                        </th>
                        <th><strong class="mb-2 d-inline-block">Tax:</strong><br><span id="total-tax">0</span> AF</th>
                        <th><strong class="mb-2 d-inline-block">Bonus:</strong><br><span id="total-bonus">0</span> AF</th>
                        <th colspan="3"><strong class="mb-2 d-inline-block">Payable:</strong><br><span
                                id="total-payable">0</span> AF</th>
                        <th><strong class="mb-2 d-inline-block">Grand Total:</strong><br><span
                                id="total-grand-total">0</span> AF</th>
                    </tr>
                </tfoot>
            </table>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Save Payroll</button>
            </div>
        </form>
    @endif
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".persianDate").persianDatepicker();
        });

        document.getElementById('payroll_date').addEventListener('input', function() {
            document.getElementById('payrollDateForm').submit();
        });
    </script>

    <script>
        $(document).ready(function() {
            function updateTotals() {
                let totalSalary = 0;
                let totalTax = 0;
                let totalBonus = 0;
                let totalPayable = 0;
                let totalGrandTotal = 0;

                const officialDays = parseFloat($('#official_days').val()) || 30;

                $('tbody tr').each(function() {
                    const baseSalary = parseFloat($(this).find('td').eq(1).text().replace(/[^\d.-]/g,
                        '')) || 0;
                    const presentDays = parseFloat($(this).find('input.present-days').val()) || 0;
                    const bonus = parseFloat($(this).find('input.bonus').val()) || 0;
                    const tax = parseFloat($(this).find('input.tax').val()) || 0;
                    const netPayable = parseFloat($(this).find('input.net-payable').val()) || 0;
                    const grandTotal = parseFloat($(this).find('input.grand-total').val()) || 0;

                    totalSalary += (baseSalary / officialDays) * presentDays;
                    totalTax += tax;
                    totalBonus += bonus;
                    totalPayable += netPayable;
                    totalGrandTotal += grandTotal;
                });

                $('#total-salary').text(formatNumber(totalSalary));
                $('#total-tax').text(formatNumber(totalTax));
                $('#total-bonus').text(formatNumber(totalBonus));
                $('#total-payable').text(formatNumber(totalPayable));
                $('#total-grand-total').text(formatNumber(totalGrandTotal));
            }

            $('input.present-days, input.bonus, input.additional-payments, #official_days').on('input', function() {
                const officialDays = parseFloat($('#official_days').val()) ||
                30; // Default to 30 if not set

                $('tbody tr').each(function() {
                    const row = $(this);
                    const baseSalary = parseFloat(row.find('td').eq(1).text().replace(/[^\d.-]/g,
                        '')) || 0;
                    const presentDays = parseFloat(row.find('input.present-days').val()) || 0;
                    const bonus = parseFloat(row.find('input.bonus').val()) || 0;
                    const additionalPayments = parseFloat(row.find('input.additional-payments')
                    .val()) || 0;
                    const testsNetPayable = parseFloat(row.find('input.tests-net-payable').val()) ||
                        0;

                    const grossSalary = (baseSalary / officialDays) * presentDays;
                    const taxableIncome = grossSalary + bonus + additionalPayments;
                    const tax = taxableIncome * 0.1;
                    const netPayable = taxableIncome - tax;

                    row.find('input.tax').val(tax.toFixed(2));
                    row.find('input.net-payable').val(netPayable.toFixed(2));
                    row.find('input.gross-salary').val(taxableIncome.toFixed(2));
                    row.find('input.grand-total').val((netPayable + testsNetPayable).toFixed(2));
                });

                updateTotals();
            });

            // Initial calculation of totals
            updateTotals();
        });
    </script>
    <script>
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
