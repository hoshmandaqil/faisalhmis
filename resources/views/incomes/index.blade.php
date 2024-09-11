@extends('layouts.master')

@section('page_title')
    Miscellaneous Income
@endsection

@section('page-action')
    {{-- @if (in_array('PO Creation', $user_permissions)) --}}
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addIncomeModal">
        Add New Income
    </button>
    {{-- @endif --}}
    {{-- @if (in_array('PO Creation', $user_permissions)) --}}
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#incomeCategoriesModal">
        Income Categories
    </button>
    {{-- @endif --}}
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
                            <th>Slip</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Income Details (Remarks)</th>
                            <th>Amount</th>
                            <th>Paid By</th>
                            <th>Paid To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <x-general.no-record :data="$incomes" />

                        @foreach ($incomes as $income)
                            <tr>
                                <td>{{ ($incomes->currentpage() - 1) * $incomes->perpage() + $loop->index + 1 }}
                                <td>{{ $income->slip_no }}</td>
                                <td>{{ $income->date }}</td>
                                <td>{{ $income->incomeCategory->name }}</td>
                                <td>{{ $income->remarks }}</td>
                                <td>{{ number_format($income->amount) }} AF</td>
                                <td>{{ $income->paid_by }}</td>
                                <td>{{ $income->paid_to }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#viewIncome" data-expense="{{ $income }}">
                                        View
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#addIncomeModal" data-expense="{{ $income }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $incomes->links() }}
            </div>
        </div>
    </div>

    <!-- Add Income Modal -->
    <div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog" aria-labelledby="addIncomeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Income</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form class="p-3" method="post" action="{{ route('incomes.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="income_id">
                        <input type="hidden" name="slip_no" id="slip_no">
                        <input type="hidden" name="cashier" id="cashier">

                        {{-- Main Fields --}}
                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">Income Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Income Description
                                            <x-general.required />
                                        </label>
                                        <textarea class="form-control" required name="income_description"></textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Paid By
                                            <x-general.required />
                                        </label>
                                        <input class="form-control" type="text" name="paid_by" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Paid To
                                            <x-general.required />
                                        </label>
                                        <input class="form-control" type="text" name="paid_to" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Category
                                            <x-general.required />
                                        </label>
                                        <select class="form-control" name="category" required>
                                            <option value=""></option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date
                                            <x-general.required />
                                        </label>
                                        <input class="form-control persianDate" type="text" name="date" readonly
                                            required>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Amount
                                            <x-general.required />
                                        </label>
                                        <input class="form-control" type="text" name="amount" required>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end d-flex justify-content-end">
                                <button class="btn btn-lg btn-primary mb-2" type="submit">
                                    <span class="indicator-label">Save</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Categories Modal -->
    <div class="modal fade" id="incomeCategoriesModal" tabindex="-1" role="dialog"
        aria-labelledby="incomeCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <section class="income-category">

                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Add new income category
                                </h4>
                                <div class="card-toolbar">
                                    <button class="btn btn-icon btn-sm btn-active-light-primary ms-2" type="button">
                                        <i class="bi bi-x-lg fs-2x"></i>
                                    </button>
                                </div>
                            </div>
                            <form id="incomeForm" class="p-3" method="post"
                                action="{{ route('income_categories.store') }}">
                                @csrf
                                @method('POST') <!-- Will be replaced by JavaScript if editing -->
                                <input type="hidden" name="id" id="categoryId">
                                <!-- Hidden field to store the category ID -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>Category Name *</label>
                                                <input class="form-control" type="text" name="name" id="name"
                                                    required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Category Name (FA) *</label>
                                                <input class="form-control" type="text" name="name_fa" id="name_fa"
                                                    required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Description</label>
                                                <input class="form-control" type="text" name="description"
                                                    id="description">
                                            </div>
                                            <div class="form-group col-md-2 text-center">
                                                <label>Included in Tax?</label>
                                                <input class="form-control" type="checkbox" name="tax"
                                                    id="tax">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary mb-0" type="submit">Save</button>
                                </div>
                            </form>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <h4>Expense Categories</h4>
                                <button class="btn btn-primary btn-sm" @click="$store.categories.categoryFormToggle()">Add
                                    New Category</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-sm table-rounded border gs-7 gy-3" id="pageTable">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th>No.</th>
                                            <th>Category Name</th>
                                            <th>Category Name (FA)</th>
                                            <th>Description</th>
                                            <th>Tax Included</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td><span>{{ $loop->index + 1 }}</span></td>
                                                <td><span>{{ $category->name }}</span></td>
                                                <td><span>{{ $category->name_fa }}</span></td>
                                                <td><span>{{ $category->description }}</span></td>
                                                <td><span>{{ $category->tax ? 'Included' : 'Not Included' }}</span></td>
                                                <td>
                                                    <div class="d-flex">
                                                        <button class="btn btn-icon btn-success btn-sm edit-btn mr-2"
                                                            data-id="{{ $category->id }}"
                                                            data-name="{{ $category->name }}"
                                                            data-name-fa="{{ $category->name_fa }}"
                                                            data-description="{{ $category->description }}"
                                                            data-parent="{{ $category->parent }}"
                                                            data-tax="{{ $category->tax ? '1' : '0' }}">
                                                            Edit
                                                        </button>
                                                        <form
                                                            action="{{ route('expense_categories.destroy', $category->id) }}"
                                                            method="post">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button type="submit" class="btn btn-icon btn-danger btn-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- View Income -->
    <div class="modal fade" id="viewIncome" tabindex="-1" role="dialog" aria-labelledby="viewIncomeLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <div class="d-print-block" id="paymentPrint">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-rounded border gs-7 gy-3">
                                    <tr>
                                        <td class="mx-auto text-start w-25" rowspan="100%">
                                            <img class="h-50px" src="" alt="Company Logo" />
                                        </td>
                                        <td class="text-end w-25">
                                            <ul class="list-unstyled">
                                                <li><strong>Date: </strong> <span id="expenseDate"></span></li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                    <tr>
                                        <td><strong>Paid By:</strong> <span id="paidBy"></span></td>
                                        <td><strong>Paid To:</strong> <span id="paidTo"></span></td>
                                        <td><strong>Income Category:</strong> <span id="incomeCategory"></span></td>
                                        <td><strong>Slip No:</strong> <span id="voucherNo"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <table
                                    class="table table-sm table-rounded table-row-bordered table-striped border gs-7 gy-3">
                                    <thead>
                                        <tr class="fw-bold fs-6 border-bottom border-gray-200">
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expenseItems">
                                        <!-- Income items will be injected here by jQuery -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                    <tr>
                                        <td><strong>Remarks:</strong> <span id="remarks"></span></td>
                                        <td><strong>Cashier:</strong> <span id="cashier"></span></td>
                                        <td><strong>Total Paid:</strong> <span id="totalPaid"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table gs-7 gy-3">
                                    <tr>
                                        <td class="text-start fw-bold">Paid By</td>
                                        <td class="text-center fw-bold">Received By</td>
                                        <td class="text-end fw-bold">Checked By</td>
                                        <td class="text-end fw-bold">Approved By</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button onclick="printDiv('paymentPrint')" type="button"
                            class="btn btn-icon btn-primary btn-sm">
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>

    <script>
        $('body').on('focus', ".persianDate", function() {
            $(this).persianDatepicker();
        });
    </script>
    {{-- Edit Expense --}}
    <script>
        // Edit Income Category
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const form = document.getElementById('incomeForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const description = this.dataset.description;
                    const paidBy = this.dataset.paidBy;
                    const paidTo = this.dataset.paidTo;
                    const category = this.dataset.category;
                    const date = this.dataset.date;
                    const amount = this.dataset.amount;
                    const remarks = this.dataset.remarks;

                    // Set form action to the update route
                    form.action = `/incomes/${id}`;

                    // Populate the form with income data
                    form.querySelector('#income_id').value = id;
                    form.querySelector('textarea[name="income_description"]').value = description;
                    form.querySelector('input[name="paid_by"]').value = paidBy;
                    form.querySelector('input[name="paid_to"]').value = paidTo;
                    form.querySelector('select[name="category"]').value = category;
                    form.querySelector('input[name="date"]').value = date;
                    form.querySelector('input[name="amount"]').value = amount;
                    form.querySelector('textarea[name="remarks"]').value = remarks;

                    // Open the modal
                    $('#addIncomeModal').modal('show');
                });
            });
        });
    </script>

    {{-- View Income Details --}}
    <script>
        $(document).ready(function() {
            $('#viewIncome').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var income = button.data('income');

                // Populate modal fields with income details
                $('#viewIncome #slipNo').text(income.slip_no);
                $('#viewIncome #expenseDate').text(income.date);
                $('#viewIncome #paidBy').text(income.paid_by);
                $('#viewIncome #paidTo').text(income.paid_to);
                $('#viewIncome #incomeCategory').text(income.category ? income.category.name : '');
                $('#viewIncome #remarks').text(income.remarks);
                $('#viewIncome #cashier').text(income.cashier ? income.cashier.name : '');

                // Clear previous income items
                $('#incomeItems').empty();

                // Populate income items
                $.each(income.expenses, function(i, item) {
                    $('#incomeItems').append(`
                        <tr>
                            <td>${item.expense_description}</td>
                            <td>${item.amount.toLocaleString()} AFN</td>
                            <td>${item.quantity}</td>
                            <td>${item.remarks}</td>
                        </tr>
                    `);
                });
            });
        });
    </script>

    {{-- Edit Income --}}
    <script>
        $(document).ready(function() {
            $('#addIncomeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var income = button.data('income'); // Extract info from data-* attributes

                // If editing, populate the form fields with the income data
                if (income) {
                    $('#income_id').val(income.id);
                    $('textarea[name="income_description"]').val(income.income_description);
                    $('input[name="paid_by"]').val(income.paid_by);
                    $('input[name="paid_to"]').val(income.paid_to);
                    $('select[name="category"]').val(income.category);
                    $('input[name="date"]').val(income.date);
                    $('input[name="amount"]').val(income.amount);
                    $('textarea[name="remarks"]').val(income.remarks);
                    $('input[name="slip_no"]').val(income.slip_no);
                    $('input[name="cashier"]').val(income.cashier);

                    let totalPrice = 0;

                    // Populate income items
                    $('#expense-items-container').empty();
                    $.each(income.expenses, function(index, item) {
                        var expenseItemHtml = `
                            <div class="row mb-4 expense-item-row">
                                <div class="col-md-1">
                                    <a class="btn btn-icon btn-sm btn-primary mt-7 add-item" href="#">
                                        +
                                    </a>
                                    <a class="btn btn-icon btn-sm btn-danger mt-7 remove-item" href="#">
                                        -
                                    </a>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Expense Description *</label>
                                    <input class="form-control" type="text" name="expenses[${index}][expense_description]" value="${item.expense_description}" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Amount *</label>
                                    <input class="form-control" type="number" name="expenses[${index}][amount]" value="${item.amount}" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Quantity *</label>
                                    <input class="form-control" type="number" name="expenses[${index}][quantity]" value="${item.quantity}" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Remarks</label>
                                    <input class="form-control" type="text" name="expenses[${index}][remarks]" value="${item.remarks}">
                                </div>
                            </div>
                        `;
                        $('#expense-items-container').append(expenseItemHtml);

                        totalPrice += Number(item.amount) * item.quantity;
                    });

                    $('#total-amount').text(totalPrice.toFixed(2));
                } else {
                    // Clear the form if adding a new income
                    $('#incomeForm')[0].reset();
                    $('#income_id').val('');
                }
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
