@extends('layouts.master')

@section('page_title')
    Expenses
@endsection

@section('page-action')
    {{-- @if (in_array('PO Creation', $user_permissions)) --}}
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addExpenseModal">
        Add New Expense
    </button>
    {{-- @endif --}}
    {{-- @if (in_array('PO Creation', $user_permissions)) --}}
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#expenseCategoriesModal">
        Expense Categories
    </button>
    {{-- @endif --}}
@endsection

@section('search_bar')
    <div class="search-container my-4">
        <div class="row justify-content-center">
            <!-- Search Bar Section -->
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12 mb-3">
                <form action="{{ route('expenses.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="searchTerm" class="form-control" value="{{ request('searchTerm') }}"
                            placeholder="Search Expense...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="icon-search1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Category Filter Section -->
            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mb-3">
                <form action="{{ route('expenses.search') }}" method="GET">
                    <div class="input-group">
                        <select class="form-control" name="category" id="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">
                                <i class="icon-filter mr-2"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                            <th>No.</th>
                            <th>Slip No.</th>
                            <th>PO No.</th>
                            <th>Paid By</th>
                            <th>Paid To</th>
                            <th>Expense Date</th>
                            <th>Category</th>
                            <th>Expense details (Remarks)</th>
                            <th>Amount</th>
                            <th>file</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ ($expenses->currentpage() - 1) * $expenses->perpage() + $loop->index + 1 }}</td>
                                <td>{{ $expense->slip_no }}</td>
                                <td>{{ $expense->po_id }}</td>
                                <td>{{ $expense->paid_by }}</td>
                                <td>{{ $expense->paid_to }}</td>
                                <td>{{ $expense->date }}</td>
                                <td>{{ $expense->expenseCategory->name ?? '' }}</td>
                                <td class="font-parastoo">{{ $expense->remarks }}</td>
                                <td>{{ number_format($expense->sum_paid) }} AF</td>
                                <td>
                                    <span>
                                        @if ($expense->file)
                                            <i class="icon-check-circle text-success"></i>
                                        @else
                                            <i class="icon-x text-danger"></i>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                data-target="#viewExpense" data-expense="{{ $expense }}"
                                                data-sum-paid="{{ number_format($expense->sum_paid) }}">
                                                View
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                data-target="#expenseFiles" data-expense="{{ $expense }}"
                                                data-sum-paid="{{ number_format($expense->sum_paid) }}">
                                                Files/Attachments
                                            </a>
                                            @if (in_array('edit_expense', $user_permissions))
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                    data-target="#addExpenseModal" data-expense="{{ $expense }}"
                                                    data-sum-paid="{{ number_format($expense->sum_paid) }}">
                                                    Edit
                                                </a>
                                            @endif
                                            @if (in_array('delete_expense', $user_permissions))
                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                    data-target="#deleteExpenseModal"
                                                    data-expense-id="{{ $expense->id }}">
                                                    Delete
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @php
                                $totalAmount += $expense->sum_paid;
                            @endphp
                        @endforeach

                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                            <td><strong>{{ number_format($totalAmount, 2) }} AF</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Expense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form class="p-3" method="post" action="{{ route('expenses.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="expense_id">
                        <input type="hidden" name="slip_no" id="slip_no">
                        <input type="hidden" name="cashier" id="cashier">

                        {{-- Main Fields --}}
                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">Expense Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="form-group col-md-3">
                                        <label>Paid By
                                            *
                                        </label>
                                        <input class="form-control" type="text" name="paid_by" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Paid To
                                            *
                                        </label>
                                        <input class="form-control" type="text" name="paid_to" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Category
                                            *
                                        </label>
                                        <select class="form-control" name="category" required>
                                            <option value=""></option>
                                            @foreach ($categories as $category)
                                                <optgroup label="{{ $category->name }}">
                                                    @if ($category->subCategories->isEmpty())
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </option>
                                                    @else
                                                        @foreach ($category->subCategories as $subCategory)
                                                            <option value="{{ $subCategory->id }}"
                                                                :selected="$store.form.form.category ==
                                                                    $subCategory - > id">
                                                                {{ $subCategory->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </optgroup>
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
                                    <div class="form-group col-md-3">
                                        <label>Purchase Order</label>
                                        <select class="form-control selectpicker" name="po_id" required
                                            data-live-search="true">
                                            <option value="" disabled selected>Please select a PO</option>
                                            @if ($pos->isEmpty())
                                                <option value="0">Without PO</option>
                                            @else
                                                @foreach ($pos as $po)
                                                    @if ($po->approved_by !== null)
                                                        <option value="{{ $po->id }}">
                                                            {{ $po->id }}: {{ $po->description }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Supporting Document</label>
                                        <input class="form-control-file" id="file" type="file"
                                            name="file"="image/*,.pdf">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Remarks (Describe the expense)
                                            *
                                        </label>
                                        <textarea class="form-control" name="remarks" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">Expense Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="expense-items-container">
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
                                            <input class="form-control" type="text"
                                                name="expenses[0][expense_description]" required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Amount *</label>
                                            <input class="form-control" type="number" name="expenses[0][amount]"
                                                placeholder="Amount" required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Quantity *</label>
                                            <input class="form-control" type="number" name="expenses[0][quantity]"
                                                placeholder="Quantity" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Remarks</label>
                                            <input class="form-control" type="text" placeholder="Remarks"
                                                name="expenses[0][remarks]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-5">
                            <div class="card-body center text-center">
                                <h3>Total: <span id="total-amount"></span> AFN</h3>
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
    <div class="modal fade" id="expenseCategoriesModal" tabindex="-1" role="dialog"
        aria-labelledby="expenseCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <section class="expense-category">

                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Add new expense category
                                </h4>
                                <div class="card-toolbar">
                                    <button class="btn btn-icon btn-sm btn-active-light-primary ms-2" type="button">
                                        <i class="bi bi-x-lg fs-2x"></i>
                                    </button>
                                </div>
                            </div>
                            <form id="expenseForm" class="p-3" method="post"
                                action="{{ route('expense_categories.store') }}">
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
                                            <div class="form-group col-md-3">
                                                <label>Parent</label>
                                                <select class="form-control" name="parent" id="parent">
                                                    <option value=""></option>
                                                    @foreach ($categories as $cateogry)
                                                        <option value="{{ $cateogry->id }}">
                                                            <span>{{ $cateogry->name }}</span>
                                                        </option>
                                                    @endforeach
                                                </select>
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
                                            @foreach ($category->subCategories as $subCategory)
                                                <tr>
                                                    <td></td>
                                                    <td><span>{{ $subCategory->name }}</span></td>
                                                    <td><span>{{ $subCategory->name_fa }}</span></td>
                                                    <td><span>{{ $subCategory->description }}</span></td>
                                                    <td><span>{{ $subCategory->tax ? 'Included' : 'Not Included' }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <button class="btn btn-icon btn-success btn-sm edit-btn mr-2"
                                                                data-id="{{ $subCategory->id }}"
                                                                data-name="{{ $subCategory->name }}"
                                                                data-name-fa="{{ $subCategory->name_fa }}"
                                                                data-description="{{ $subCategory->description }}"
                                                                data-parent="{{ $subCategory->parent }}"
                                                                data-tax="{{ $subCategory->tax ? '1' : '0' }}">
                                                                Edit
                                                            </button>
                                                            <form
                                                                action="{{ route('expense_categories.destroy', $subCategory->id) }}"
                                                                method="post">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-icon btn-danger btn-sm">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
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

    <!-- Expense Files -->
    <div class="modal fade" id="expenseFiles" tabindex="-1" role="dialog" aria-labelledby="expenseFilesLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <!-- Main Fields -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="100%">
                                            <h5>Slip No</h5>
                                            <p class="mt-5" id="slipId"></p>
                                        </th>
                                        <th class="text-center">
                                            <h5>Paid By</h5>
                                            <p class="mt-5" id="paidBy"></p>
                                        </th>
                                        <th class="text-center">
                                            <h5>Paid To</h5>
                                            <p class="mt-5" id="paidTo"></p>
                                        </th>
                                        <th class="text-center">
                                            <h5>Total Amount</h5>
                                            <p class="mt-5" id="totalAmount"></p>
                                        </th>
                                        <th class="text-center">
                                            <h5>Date</h5>
                                            <p class="mt-5" id="fileDate"></p>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <!-- File Upload Form -->
                    {{-- <div class="row mb-4">
                        <div class="col-md-12">
                            <form class="p-3" id="uploadFileForm" method="post" enctype="multipart/form-data">
                                <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                    <thead>
                                        <tr>
                                            <th class="text-center mw-25">
                                                <input class="form-control" id="fileInput" type="file"
                                                    accept="image/*,.pdf">
                                            </th>
                                            <th class="text-center">
                                                <input class="form-control" type="text" id="fileRemarks"
                                                    placeholder="Remarks">
                                            </th>
                                            <th class="text-center">
                                                <button class="btn btn-primary" type="submit" id="saveFileBtn">Save
                                                    File</button>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </form>
                        </div>
                    </div> --}}

                    <!-- Files List -->
                    <div class="row mb-4" id="filesLoading" style="display: none;">
                        <h3 class="text-center"><span class="spinner-border spinner-border-sm align-middle me-2"></span>
                            Loading Content...</h3>
                    </div>
                    <div class="row mb-4" id="filesList">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>File</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="expenseFilesTable">
                                    <!-- Files will be injected here by jQuery -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Expense -->
    <div class="modal fade" id="viewExpense" tabindex="-1" role="dialog" aria-labelledby="viewExpenseLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="" style="height: 50px"
                                            class="mb-4">
                                       </td>
                                        <td class="text-center fw-bold">
                                            <h4>Ministry of Public Health</h4>
                                            <h6>Bayazid Rokhan Curative Hospital KBL</h6>
                                            <h6>Finance Department</h6>
                                            <h6>Expense Voucher</h6>
                                        </td>
                                        <td class="text-end w-25">
                                            <ul>
                                                <li class="list-unstyled"><strong>Voucher No: <span
                                                            id="voucherNo"></span></strong></li>
                                                <li class="list-unstyled"><strong>Date: <span
                                                            id="expenseDate"></span></strong></li>
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
                                        <td><strong>Expense Category:</strong> <span id="expenseCategory"></span></td>
                                        <td><strong>Purchase Order:</strong> <span id="purchaseOrder"></span></td>
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
                                            <th>Quantity</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expenseItems">
                                        <!-- Expense items will be injected here by jQuery -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                    <tr>
                                        <td colspan="100%"><strong>Remarks:</strong> <span id="remarks"></span></td>
                                    </tr>
                                    <tr>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExpenseModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteExpenseForm" method="POST">
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
    {{-- Edit Expense --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const form = document.getElementById('expenseForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const nameFa = this.getAttribute('data-name-fa');
                    const description = this.getAttribute('data-description');
                    const parent = this.getAttribute('data-parent');
                    const tax = this.getAttribute('data-tax') == '1';

                    // Set form action to the update route
                    form.action = `/expense-categories/${id}`;

                    // Populate the form with category data
                    form.querySelector('#categoryId').value = id;
                    form.querySelector('#name').value = name;
                    form.querySelector('#name_fa').value = nameFa;
                    form.querySelector('#description').value = description;
                    form.querySelector('#parent').value = parent;
                    form.querySelector('#tax').checked = tax;

                    // Open the modal
                    $('#expenseCategoriesModal').modal('show');
                });
            });
        });
    </script>
    {{-- Add expense item row --}}
    <script>
        $(document).ready(function() {
            let itemIndex = 1;

            // Function to add a new row
            $(document).on('click', '.add-item', function(e) {
                e.preventDefault();

                // Clone the last row and reset values
                let newItem = $('.expense-item-row:last').clone();
                newItem.find('input').val('');

                // Update the name attributes with the new index
                newItem.find('input[name^="expenses"]').each(function() {
                    let name = $(this).attr('name');
                    $(this).attr('name', name.replace(/\[0\]/, '[' + itemIndex + ']'));
                });

                // Append the new item to the container
                $('#expense-items-container').append(newItem);

                itemIndex++;
            });

            // Function to remove a row
            $(document).on('click', '.remove-item', function(e) {
                e.preventDefault();

                // Check if there are more than one rows, then remove the row
                if ($('.expense-item-row').length > 1) {
                    $(this).closest('.expense-item-row').remove();
                } else {
                    alert('At least one item is required.');
                }
            });
        });
    </script>
    {{-- View Expense --}}
    <script>
        $(document).ready(function() {
            $('#viewExpense').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var expense = button.data('expense');
                var totalPaid = button.data('sum-paid');

                // Populate modal fields
                $('#viewExpense #voucherNo').text(expense.slip_no);
                $('#viewExpense #expenseDate').text(expense.date);
                $('#viewExpense #paidBy').text(expense.paid_by);
                $('#viewExpense #paidTo').text(expense.paid_to);
                $('#viewExpense #expenseCategory').text(expense.expense_category ? expense.expense_category
                    .name : '');
                $('#viewExpense #purchaseOrder').text(expense.po_id == null ? 'Without PO' : expense.po_id);
                $('#viewExpense #remarks').text(expense.remarks);
                $('#viewExpense #cashier').text(expense.cashier_user ? expense.cashier_user.name : '');
                $('#viewExpense #totalPaid').text(totalPaid ? totalPaid + ' AFN' : '0 AFN');

                // Clear previous expense items
                $('#expenseItems').empty();

                // Populate expense items
                $.each(expense.expenses, function(i, item) {
                    $('#expenseItems').append(`
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
    {{-- View Expense Files --}}
    <script>
        $(document).ready(function() {
            $('#expenseFiles').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var expense = button.data('expense');
                var sumPaid = button.data('sum-paid');

                // Populate modal fields
                $('#slipId').text(expense.slip_no);
                $('#paidBy').text(expense.paid_by);
                $('#paidTo').text(expense.paid_to);
                $('#totalAmount').text(sumPaid + ' AFN');
                $('#fileDate').text(expense.date);
                $('#fileRemarks').val(expense.remarks)

                // Load files (simulated here, you would use AJAX to load actual files from the server)
                loadFiles(expense.slip_no);

                // // Handle file upload
                // $('#uploadFileForm').off('submit').on('submit', function(e) {
                //     e.preventDefault();

                //     var fileData = new FormData();
                //     fileData.append('file', $('#fileInput')[0].files[0]);
                //     fileData.append('remarks', $('#fileRemarks').val());
                //     fileData.append('slip_id', expense.slip_no);

                //     // Simulate file upload (you would actually send this to the server)
                //     console.log("File uploaded", fileData);
                //     // After upload, refresh the file list
                //     loadFiles(expense.slip_no);
                // });
            });

            function loadFiles(expenseId) {
                fetch(`/expenses/${expenseId}/files`)
                    .then(response => response.json())
                    .then(expenses => {
                        // Clear existing table rows
                        $('#expenseFilesTable').empty();

                        // Populate the table with the fetched files
                        expenses.forEach((expense, index) => {
                            $('#expenseFilesTable').append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td><a href="/storage/expenses/${expense.file}" target="_BLANK">View File</a></td>
                                        <td>${expense.remarks}</td>
                                        <td>
                                            <form
                                                action="{{ route('expense-files-delete') }}"
                                                method="post">
                                                @method('DELETE')
                                                @csrf
                                                <input type="hidden" name="slip_no" value="${expense.slip_no}" />
                                                <button type="submit" class="btn btn-icon btn-danger btn-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                `);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching files:', error);
                    });
            }
        });
    </script>
    {{-- Edit Expense --}}
    <script>
        $(document).ready(function() {
            $('#addExpenseModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var expense = button.data('expense'); // Extract info from data-* attributes

                // If editing, populate the form fields with the expense data
                if (expense) {
                    $('#expense_id').val(expense.id);
                    $('input[name="paid_by"]').val(expense.paid_by);
                    $('input[name="paid_to"]').val(expense.paid_to);
                    $('select[name="category"]').val(expense.category);
                    $('input[name="date"]').val(expense.date);
                    $('textarea[name="remarks"]').val(expense.remarks);
                    $('select[name="po_id"]').val(expense.po_id || 0);
                    $('input[name="slip_no"]').val(expense.slip_no);
                    $('input[name="cashier"]').val(expense.cashier);

                    let totalPrice = 0
                    // Populate expense items
                    $('#expense-items-container').empty();
                    $.each(expense.expenses, function(index, item) {
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
                                    <input class="form-control" type="number" name="expenses[${index}][amount]" value="${item.amount}" placeholder="Amount" required>
                                </div>
                                 <div class="form-group col-md-2">
                                    <label>Quantity *</label>
                                    <input class="form-control" type="number" name="expenses[${index}][quantity]" value="${item.quantity}" placeholder="Quantity" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Remarks</label>
                                    <input class="form-control" type="text" name="expenses[${index}][remarks]" value="${item.remarks}">
                                </div>
                            </div>
                        `;
                        $('#expense-items-container').append(expenseItemHtml);

                        totalPrice += Number(item.amount) * item.quantity
                    });

                    $('#total-amount').text(totalPrice.toFixed(2));
                } else {
                    // Clear the form if adding a new expense
                    $('#expenseForm')[0].reset();
                    $('#expense_id').val('');
                    $(this).find('form').trigger('reset');
                }
            });

        });
    </script>
    {{-- Update Total Amount --}}
    <script>
        $(document).ready(function() {
            function updateTotalAmount() {
                let totalAmount = 0;

                // Iterate over each item row
                $('#expense-items-container .expense-item-row').each(function() {
                    let amount = parseFloat($(this).find('input[name*="[amount]"]').val());
                    let quantity = parseFloat($(this).find('input[name*="[quantity]"]').val());

                    if (!isNaN(amount) && !isNaN(quantity)) {
                        totalAmount += amount * quantity;
                    }
                });

                // Display the total amount
                $('#total-amount').text(totalAmount.toFixed(2));
            }

            // Update total amount whenever an amount or quantity input field changes
            $('#expense-items-container').on('input', 'input[name*="[amount]"], input[name*="[quantity]"]',
                function() {
                    updateTotalAmount();
                });

            // Initialize total amount on page load
            updateTotalAmount();
        });
    </script>


    <script>
        $('#deleteExpenseModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var expenseId = button.data('expense-id');

            var form = $(this).find('form');
            var actionUrl = '{{ route('expenses.destroy', ':id') }}';
            actionUrl = actionUrl.replace(':id', expenseId);

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
