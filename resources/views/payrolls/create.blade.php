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
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="official_days">Official Days</label>
                    <input type="number" name="official_days" id="official_days" class="form-control" required>
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
                            <td>{{ $employee->employeeCurrentSalary->salary_amount }} AFN</td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][present_days]"
                                    class="form-control present-days" required>
                            </td>
                            <td>
                                <input type="number" name="employees[{{ $employee->id }}][bonus]"
                                    class="form-control bonus" required>
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
                                                <td class="pt-2 pb-2">{{ $summary['number_of_tests'] }}</td>
                                                <td class="pt-2 pb-2">{{ $summary['total_price'] }} AFN</td>
                                                <td class="pt-2 pb-2">{{ $summary['payable'] }} AFN</td>
                                                <td class="pt-2 pb-2">{{ $summary['tax'] }} AFN</td>
                                                <td class="pt-2 pb-2">{{ $summary['payable'] - $summary['tax'] }} AFN</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td class="pb-2 pt-2" colspan="2"><strong>Total</strong></td>
                                            <td class="pb-2 pt-2">
                                                <strong>{{ $employee->lab_tests_summary->sum('total_price') }} AFN</strong>
                                            </td>
                                            <td class="pb-2 pt-2">
                                                <strong>{{ $employee->lab_tests_summary->sum('payable') }}
                                                    AFN</strong>
                                            </td>
                                            <td class="pb-2 pt-2"><strong>{{ $employee->lab_tests_summary->sum('tax') }}
                                                    AFN</strong></td>
                                            <td class="pb-2 pt-2">
                                                <strong><span>{{ $employee->lab_tests_summary->sum('payable') - $employee->lab_tests_summary->sum('tax') }}</span>
                                                    AFN</strong>
                                            </td>
                                            <input type="hidden" name="employees[{{ $employee->id }}][tests_net_payable]"
                                                class="tests-net-payable"
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
            </table>

            <button type="submit" class="btn btn-primary">Generate Payroll</button>
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
            $('input.present-days, input.bonus, input.additional-payments').on('input', function() {
                const row = $(this).closest('tr');
                const baseSalary = parseFloat(row.find('td').eq(1).text().replace(' AFN', ''));
                const presentDays = parseFloat(row.find('input.present-days').val()) || 0;
                const bonus = parseFloat(row.find('input.bonus').val()) || 0;
                const additionalPayments = parseFloat(row.find('input.additional-payments').val()) || 0;
                const testsNetPayable = parseFloat(row.find('input.tests-net-payable').val()) || 0;

                const grossSalary = (baseSalary / 30) * presentDays;
                const taxableIncome = grossSalary + bonus + additionalPayments;
                const tax = taxableIncome * 0.1;
                const netPayable = taxableIncome - tax;

                row.find('input.tax').val(tax.toFixed(2));
                row.find('input.net-payable').val(netPayable.toFixed(2));
                row.find('input.gross-salary').val(taxableIncome.toFixed(2));
                row.find('input.grand-total').val((netPayable + testsNetPayable).toFixed(2));
            });
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/persianDatepicker/css/persianDatepicker-default.css') }}" />
    <style>
        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }
    </style>
@endsection