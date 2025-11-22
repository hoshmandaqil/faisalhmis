@extends('layouts.master')

@section('page_title')
    Overview Report
@endsection

@section('page-action')
    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal" type="button">
        Create Report
    </button>
@endsection


@section('on_print_page_header')
@endsection
@section('content')
    <div class="d-print-block" x-ref="overviewReport">

        <div class="row">
            <div class="col">
                <h4 class="mb-4">Date Range: {{ $from }} - {{ $to }}</h4>
            </div>
        </div>
        {{-- Finance Facts --}}
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-primary border-0 mb-0">
                    <div class="card-body text-center">
                        <h3 class="mb-4 text-white">{{ number_format($totalIncome) }} AFN</h3>
                        <h5 class="text-white">Total Income</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-secondary border-0 mb-0">
                    <div class="card-body text-center">
                        <h3 class="mb-4">{{ number_format($totalPayrollPayment) }} AFN</h3>
                        <h5>Total Salary</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-info border-0 mb-0">
                    <div class="card-body text-center">
                        <h3 class="mb-4">{{ number_format($totalExpenses) }} AFN</h3>
                        <h5>Total Expenses</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-warning border-0 mb-0">
                    <div class="card-body text-center">
                        <h3 class="mb-4 text-default">
                            {{ number_format($totalExpenses + $totalPayrollPayment) }} AFN</h3>
                        <h5>Total Expense + Salary</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success border-0 mb-0">
                    <div class="card-body text-center">
                        <h3 class="mb-4">
                            {{ number_format($totalIncome - $totalExpenses - $totalPayrollPayment) }}
                            AFN
                        </h3>
                        <h5>Total Profit</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-success border-0 mb-0">
                    <div class="card-body text-center">
                        <h3> <span class="text-info">
                                {{ number_format($totalAvailableCash - 1249955) }}AFN
                            </span>
                        </h3>
                        <h5>Available Cash</h5>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-4 mb-4">
                <div class="card text-white bg-success border-0 mb-0">
                    <div class="card-body text-center">
                        <h3> <span class="text-info">
                                {{ number_format($report->cashbook['closing_balance']) }} AFN
                            </span>
                        </h3>
                        <h5>Closing balance</h5>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="row align-items-start">
            {{-- Income --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Income by Category</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm striped">
                            <tbody>
                                @if (count($incomeCategories) <= 0)
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                @endif
                                @foreach ($incomeCategories as $category => $amount)
                                    <tr>
                                        <td class="fw-bold">{{ $category }}</td>
                                        <td>{{ number_format($amount) }} AFN</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <strong>Total: </strong>
                        {{ number_format(array_sum($incomeCategories)) }}
                        AFN
                    </div>
                </div>
            </div>
            @if (false)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Income by Cashier</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm striped">
                                <tbody>
                                    {{-- @if ($report->income_by_cashier->count() <= 0) --}}
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                    {{-- @endif --}}
                                    @foreach ($report->income_by_cashier as $cashier_income)
                                        <tr>
                                            <td class="fw-bold">{{ $cashier_income->name }}:</td>
                                            <td>{{ number_format($cashier_income->sum_payment) }} AFN</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{-- <strong>Total: </strong> {{ number_format($report->income_by_cashier->sum('sum_payment')) }} --}}
                            AFN
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Income by Program</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm striped">
                                <tbody>
                                    {{-- @if ($report->income_by_program->count() <= 0) --}}
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                    {{-- @endif --}}
                                    @foreach ($report->income_by_program as $program_income)
                                        <tr>
                                            <td class="fw-bold">{{ $program_income->name }}:</td>
                                            <td>{{ number_format($program_income->sum_payment) }} AFN</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{-- <strong>Total: </strong> {{ number_format($report->income_by_program->sum('sum_payment')) }} --}}
                            AFN
                        </div>
                    </div>
                </div>
            @endif

            {{-- Expenses --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Expense by Category</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm striped">
                            <tbody>
                                @if (count($expenseCategories) <= 0 && $totalPayrollPayment <= 0)
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                @endif
                                @foreach ($expenseCategories as $category => $amount)
                                    <tr>
                                        <td class="fw-bold">{{ $category ?? '' }}</td>
                                        <td>{{ number_format($amount) }} AFN</td>
                                        {{-- <td>
                                            <a href="#" target="__blank"
                                                onclick="redirectToExpense('{{ $category_expense->expenseCategory->id ?? '' }}')">View</a>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                @if ($totalPayrollPayment > 0)
                                    <tr>
                                        <td class="fw-bold">Payroll:</td>
                                        <td>{{ number_format($totalPayrollPayment) }} AFN</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if ($totalPayrollPayment > 0)
                    <div class="card-footer">
                        <strong>Total: </strong>
                        {{ number_format($expenseCategories->sum() + $totalPayrollPayment) }}
                        AFN
                    </div>
                    @endif
                    @if (!$totalPayrollPayment)
                    <div class="card-footer">
                        <strong>Total: </strong>
                        {{ number_format($expenseCategories->sum() ) }}
                        AFN
                    </div>
                    @endif
                </div>
            </div>
            @if (false)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Expense by Cashier</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm striped">
                                <tbody>
                                    {{-- @if ($report->expense_by_cashier->count() <= 0) --}}
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                    {{-- @endif --}}
                                    @foreach ($report->expense_by_cashier as $cashier_expense)
                                        <tr>
                                            <td class="fw-bold">{{ $cashier_expense->name }}:</td>
                                            <td>{{ number_format($cashier_expense->sum_payment) }} AFN</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{-- <strong>Total: </strong> {{ number_format($report->expense_by_cashier->sum('sum_payment')) }} --}}
                            AFN
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Date Wise General income Report</h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm" action="{{ url()->current() }}" method="GET"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" name="from" type="date"
                                value="{{ $from != null ? $from : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" name="to" type="date"
                                value="{{ $to != null ? $to : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Report Type:</label>
                            <select class="form-control" name="report_type">
                                <option value="0"></option>
                                <option value="income" {{ request('report_type') == 'income' ? 'selected' : '' }}>
                                    Other Income Only</option>
                                <option value="expense" {{ request('report_type') == 'expense' ? 'selected' : '' }}>
                                    Expense Only</option>
                                <option value="salary" {{ request('report_type') == 'salary' ? 'selected' : '' }}>
                                    Salary Only</option>
                            </select>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
