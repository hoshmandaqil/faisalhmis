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
                                <td>{{ $income->incomeCategory ? $income->incomeCategory->name : 'N/A' }}</td>
                                <td>{{ $income->remarks }}</td>
                                <td>{{ number_format($income->amount) }} AF</td>
                                <td>{{ $income->paid_by }}</td>
                                <td>{{ $income->paid_to }}</td>
                                <td>
                                    @php
                                        $shamsiDate = $income->date;
                                    @endphp
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <!-- View Income Modal Trigger -->
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                data-target="#viewIncome" data-income="{{ $income }}"
                                                data-category="{{ $income->incomeCategory ? $income->incomeCategory->name : 'N/A' }}"
                                                data-cashier="{{ $income->user->name }}">
                                                View
                                            </a>
                                            <!-- Edit Income Modal Trigger -->
                                            @if (in_array('edit_income', $user_permissions))
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                    data-target="#addIncomeModal" data-income="{{ $income }}">
                                                    Edit
                                                </a>
                                            @endif
                                            <!-- Delete Income Modal Trigger -->
                                            @if (in_array('delete_income', $user_permissions))
                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                    data-target="#deleteIncomeModal" data-income-id="{{ $income->id }}">
                                                    Delete
                                                </a>
                                            @endif
                                        </div>
                                    </div>
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
                                            *
                                        </label>
                                        <input class="form-control" type="date" name="date"
                                            value="{{ date('Y-m-d') }}" required>
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

    <!-- Income Categories Modal -->
    <div class="modal fade" id="incomeCategoriesModal" tabindex="-1" role="dialog"
        aria-labelledby="incomeCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Income Categories</h4>
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
                                <h4>Income Categories</h4>
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
                                                            action="{{ route('income_categories.destroy', $category->id) }}"
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
                                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt=""
                                                style="height: 50px" class="mb-4">
                                        </td>
                                        <td class="text-end w-25">
                                            <ul class="list-unstyled">
                                                <li><strong>Date: </strong> <span id="incomeDate"></span></li>
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
                                        <td><strong>Slip No:</strong> <span id="slipNo"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                    <tr>
                                        <td><strong>Remarks:</strong> <span id="remarks"></span></td>
                                        <td><strong>Cashier:</strong> <span id="cashier"></span></td>
                                        <td><strong>Amount:</strong> <span id="amount"></span></td>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteIncomeModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteIncomeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteIncomeModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this income?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteIncomeForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
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
    {{-- Reset Income Form on Modal Open --}}
    <script>
        $(document).ready(function() {
            $('#incomeCategoriesModal').on('show.bs.modal', function() {
                var form = $('#incomeForm');

                // Clear all input fields
                form.find('input[type="text"], input[type="number"], textarea').val('');

                // Uncheck checkboxes
                form.find('input[type="checkbox"]').prop('checked', false);

                // Reset select elements to default option
                form.find('select').prop('selectedIndex', 0);

                // For example, if you have a hidden field for category ID:
                $('#categoryId').val('');
            });
        });
    </script>
    {{-- Edit Income Category --}}
    <script>
        // Edit Income Category    
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const form = document.getElementById('incomeForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const nameFa = this.dataset.nameFa;
                    const description = this.dataset.description;
                    const tax = this.dataset.tax;

                    // Set form action to the update route
                    form.action = `/income-categories/${id}`;
                    form.querySelector('#categoryId').value = id;

                    // Populate the form with income data
                    form.querySelector('input[name="name"]').value = name;
                    form.querySelector('input[name="name_fa"]').value = nameFa;
                    form.querySelector('input[name="description"]').value = description;
                    form.querySelector('input[name="tax"]').checked = tax === '1';

                    // Open the modal
                    $('#incomeCategoriesModal').modal('show');
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
                var incomeCategory = button.data('category');
                var incomeCashir = button.data('cashier');

                // Populate modal fields with income details
                $('#viewIncome #slipNo').text(income.slip_no);
                $('#viewIncome #incomeDate').text(income.date);
                $('#viewIncome #paidBy').text(income.paid_by);
                $('#viewIncome #paidTo').text(income.paid_to);
                $('#viewIncome #incomeCategory').text(incomeCategory);
                $('#viewIncome #remarks').text(income.remarks);
                $('#viewIncome #cashier').text(incomeCashir);
                $('#viewIncome #amount').text(income.amount);

                // Clear previous income items
                $('#incomeItems').empty();
            });
        });
    </script>


    {{-- Edit Income --}}
    <script>
        $(document).ready(function() {
            $('#addIncomeModal').on('show.bs.modal', function(event) {
                // Clear all form fields
                $('#incomeForm')[0].reset();

                // Clear any hidden fields or fields not reset by form.reset()
                $('#income_id').val('');
                $('textarea[name="income_description"]').val('');
                $('input[name="paid_by"]').val('');
                $('input[name="paid_to"]').val('');
                $('select[name="category"]').val('');
                $('input[name="date"]').val('');
                $('input[name="amount"]').val('');
                $('textarea[name="remarks"]').val('');
                $('input[name="slip_no"]').val('');
                $('input[name="cashier"]').val('');

                // Reset total amount display
                $('#total-amount').text('0.00');
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

                    $('#total-amount').text(totalPrice.toFixed(2));
                } else {
                    // Clear the form if adding a new income
                    $('#incomeForm')[0].reset();
                    $('#income_id').val('');
                }
            });
        });
    </script>

    <script>
        $('#deleteIncomeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var incomeId = button.data('income-id');
            var modal = $(this);
            var form = modal.find('#deleteIncomeForm');

            var actionUrl = '/incomes/' + incomeId;
            form.attr('action', actionUrl);
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
