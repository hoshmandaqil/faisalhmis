@extends('layouts.master')

@section('page_title')
    Overview Report
@endsection

@section('page-action')
@endsection


@section('on_print_page_header')
@endsection
@section('content')
    <div class="d-print-block" x-ref="overviewReport">

        {{-- Finance Facts --}}
        <div class="row">
            <div class="col-md-3 mb-5">
                <div class="card text-white bg-primary border-0">
                    <div class="card-body text-center">
                        <h3 class="mb-5 text-white">{{ number_format($report->total_income) }} AFN</h3>
                        <h5 class="text-white">Total Income</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-5">
                <div class="card text-white bg-secondary border-0">
                    <div class="card-body text-center">
                        <h3 class="mb-5">{{ number_format($report->total_payroll_payment) }} AFN</h3>
                        <h5>Total Salary</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-5">
                <div class="card text-white bg-info border-0">
                    <div class="card-body text-center">
                        <h3 class="mb-5">{{ number_format($report->total_expense) }} AFN</h3>
                        <h5>Total Expenses</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-5">
                <div class="card text-white bg-warning border-0">
                    <div class="card-body text-center">
                        <h3 class="mb-5 text-default">
                            {{ number_format($report->total_expense + $report->total_payroll_payment) }} AFN</h3>
                        <h5>Total Expense + Salary</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-5">
                <div class="card text-white bg-success border-0">
                    <div class="card-body text-center">
                        <h3 class="mb-5">
                            {{ number_format($report->total_income - $report->total_expense - $report->total_payroll_payment) }}
                            AFN
                        </h3>
                        <h5>Total Profit</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card text-white bg-success border-0">
                    <div class="card-body text-center">
                        <h3> <span class="text-info">
                                {{ number_format($report->cashbook['opening_balance']) }} AFN
                            </span>
                        </h3>
                        <h5>Opening balance</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card text-white bg-success border-0">
                    <div class="card-body text-center">
                        <h3> <span class="text-info">
                                {{ number_format($report->cashbook['closing_balance']) }} AFN
                            </span>
                        </h3>
                        <h5>Closing balance</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Income --}}
        <div class="row">
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Income by Category</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm striped">
                            <tbody>
                                {{-- @if ($report->income_by_category->count() <= 0) --}}
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                {{-- @endif --}}
                                @foreach ($report->income_by_category as $income)
                                    <tr>
                                        <td class="fw-bold">{{ $income->getType()->type }}:</td>
                                        <td>{{ number_format($income->sum_payment) }} AFN</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-bold">Miscellaneous Income:</td>
                                    <td>{{ number_format($report->miscellaneous_income) }} AFN</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <strong>Total: </strong>
                        {{-- {{ number_format($report->income_by_category->sum('sum_payment') + $report->miscellaneous_income) }} --}}
                        AFN
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
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
            <div class="col-md-4 mb-5">
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
        </div>

        {{-- Expenses --}}
        <div class="row">
            <div class="col-md-6 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Expense by Category</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm striped">
                            <tbody>
                                {{-- @if ($report->expense_by_category->count() <= 0 && $report->total_payroll_payment <= 0) --}}
                                    <tr>
                                        <td class="text-danger fw-bold" colspan="100%">*No Records Found</td>
                                    </tr>
                                {{-- @endif --}}
                                @foreach ($report->expense_by_category as $category_expense)
                                    <tr>
                                        <td class="fw-bold">{{ $category_expense->expenseCategory->name ?? '' }}:</td>
                                        <td>{{ number_format($category_expense->sum_payment) }} AFN</td>
                                        <td>
                                            <a href="#" target="__blank"
                                                onclick="redirectToExpense('{{ $category_expense->expenseCategory->id ?? '' }}')">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($report->total_payroll_payment > 0)
                                    <tr>
                                        <td class="fw-bold">Payroll:</td>
                                        <td>{{ number_format($report->total_payroll_payment) }} AFN</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <strong>Total: </strong>
                        {{-- {{ number_format($report->expense_by_category->sum('sum_payment') + $report->total_payroll_payment) }} --}}
                        AFN
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-5">
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
        </div>
    </div>
@endsection
@section('scripts')
@endsection
