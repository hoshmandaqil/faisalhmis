@extends('layouts.master')

@section('page_title')
    Edit Payroll
@endsection

@section('page-action')
@endsection

@section('content')
    <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="payroll_date">Payroll Date</label>
            <input type="date" name="payroll_date" id="payroll_date" class="form-control" value="{{ $payroll->payroll_date }}"
                required>
        </div>

        <div class="form-group">
            <label for="official_days">Official Days</label>
            <input type="number" name="official_days" id="official_days" class="form-control"
                value="{{ $payroll->official_days }}" required>
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
                    <th class="text-nowrap">Net Payable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll->items as $item)
                    <tr>
                        <td>{{ $item->employee->first_name }}</td>
                        <td>{{ $item->employee->base_salary }} AFN</td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee->id }}][present_days]"
                                class="form-control present-days" value="{{ $item->present_days }}" required>
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee->id }}][bonus]"
                                class="form-control bonus" value="{{ $item->bonus }}" required>
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee->id }}][additional_payments]"
                                class="form-control additional-payments" value="{{ $item->additional_payments }}">
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee->id }}][tax]" class="form-control tax"
                                value="{{ $item->tax }}" readonly>
                        </td>
                        <td>
                            <input type="number" name="employees[{{ $item->employee->id }}][net_payable]"
                                class="form-control net-payable" value="{{ $item->amount }}" readonly>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Payroll</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('input.present-days, input.bonus, input.additional-payments').on('input', function() {
                const row = $(this).closest('tr');
                const baseSalary = parseFloat(row.find('td').eq(1).text().replace(' AFN', ''));
                const presentDays = parseFloat(row.find('input.present-days').val()) || 0;
                const bonus = parseFloat(row.find('input.bonus').val()) || 0;
                const additionalPayments = parseFloat(row.find('input.additional-payments').val()) || 0;

                const grossSalary = (baseSalary / 30) * presentDays;
                const taxableIncome = grossSalary + bonus + additionalPayments;
                const tax = taxableIncome * 0.1; // Example tax calculation
                const netPayable = taxableIncome - tax;

                row.find('input.tax').val(tax.toFixed(2));
                row.find('input.net-payable').val(netPayable.toFixed(2));
                row.find('input.gross-salary').val(taxableIncome.toFixed(2));
            });
        });
    </script>
@endsection
