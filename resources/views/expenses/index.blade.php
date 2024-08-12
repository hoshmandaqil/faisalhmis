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
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{ url('searchPO') }}" method="post">
                        @csrf
                        <input type="text" name="search" class="search-query" value="{{ request('search') }}"
                            placeholder="Search PO By Id, Description or Price...">
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
                            <th>No.</th>
                            <th>Slip No.</th>
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
                        @foreach ($expenses as $expense)
                            <tr class="text-center">
                                <td>{{ ($expenses->currentpage() - 1) * $expenses->perpage() + $loop->index + 1 }}</td>
                                <td>{{ $expense->slip_no }}</td>
                                <td>{{ $expense->paid_by }}</td>
                                <td>{{ $expense->paid_to }}</td>
                                <td>{{ toShamsi($expense->date) }}</td>
                                <td>{{ $expense->expenseCategory->name ?? '' }}</td>
                                <td class="font-parastoo">{{ $expense->remarks }}</td>
                                <td>{{ number_format($expense->sum_paid) }} AF</td>
                                <td>
                                    <span>
                                        @if ($expense->file)
                                            <i class="bi bi-check2-circle badge badge-circle badge-success fs-4"></i>
                                        @else
                                            <i class="bi bi-x-lg badge badge-circle badge-danger fs-4"></i>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{-- <x-buttons.dropdown width="200" :delete="auth()->user()->hasPermissionTo('Delete Expense')
                                        ? route('expenses.destroy', [$application->slug, $expense->id])
                                        : false">
                                        @php
                                            $shamsiDate = toShamsi($expense->date);
                                        @endphp
                                        <a class="menu-link px-3" href="#"
                                            x-on:click="$store.view.viewSlip({{ json_encode($expense) }},'{{ $shamsiDate }}')">
                                            View
                                        </a>
                                        <a class="menu-link px-3" href="#"
                                            x-on:click="$store.files.openModal({{ json_encode($expense) }} )">
                                            Files/Attachements
                                        </a>
                                        @if (auth()->user()->hasPermissionTo('Edit Expense'))
                                            <a class="menu-link px-3" href="#"
                                                x-on:click="$store.form.editForm({{ json_encode($expense) }},'{{ $shamsiDate }}')">
                                                Edit
                                            </a>
                                        @endif
                                    </x-buttons.dropdown> --}}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="btn btn-sm btn-dark" onclick="newPo()">Add New</button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="poForm" action="{{ route('PO.store') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="numberOfFilesPerEach" id="numberOfFilesPerEach">
                        <div class="row">
                            <div class="form-group col-2">
                                <label>Paid By <span class="text-danger">*</span></label>
                                <input class="form-control quantity" type="text" name="quantity[]" value="0"
                                    required>
                            </div>
                            <div class="form-group col-2">
                                <label>Paid To <span class="text-danger">*</span></label>
                                <input class="form-control price" type="text" name="price[]" value="0" required>
                            </div>
                            <div class="form-group col-2">
                                <label>Category</label>
                                <div class="input-group ">
                                    <select class="form-control col-md-8 offse-2 selectpicker labDepsName"
                                        data-live-search="true">
                                        <option value="general">General</option>
                                        @foreach ($categories as $category)
                                            <optgroup label="{{ $category->name }}">
                                                @if ($category->subCategories->isEmpty())
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @else
                                                    @foreach ($category->subCategories as $subCategory)
                                                        <option value="{{ $subCategory->id }}">
                                                            {{ $subCategory->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-2">
                                <label>Total Price <span class="text-danger">*</span></label>
                                <input class="form-control total-price" type="text" name="total_price[]" value="0"
                                    readonly>
                            </div>
                            <div class="form-group col-2">
                                <label>PO Date <span class="text-danger">*</span></label>
                                <input class="form-control persianDate" autocomplete="off" autofill="off" type="text"
                                    name="date[]" required>
                            </div>
                            <div class="form-group col-2">
                                <label>Files </label>
                                <input type="file" name="files[]" accept='image/*' class="imagesUpload" multiple>
                            </div>
                        </div>
                        <div id="addMore"></div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn" onclick="submitForm()">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
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
                                                            data-parent="{{ $category->parent_id }}"
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
                                                                data-parent="{{ $subCategory->parent_id }}"
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>

    <script>
        $('body').on('focus', ".persianDate", function() {
            $(this).persianDatepicker();
        });

        function submitForm() {
            var numberOfFiles = new Array();
            $('.imagesUpload').each(function() {
                var numFiles = $(this)[0].files.length;
                numberOfFiles.push(numFiles);
            });
            $('#numberOfFilesPerEach').val(numberOfFiles);
        }

        $(document).on('input', '.quantity', function() {
            var quantity = $(this).val();
            var price = $(this).parents('div.row').find('.price').val();
            $(this).parents('div.row').find('.total-price').val(quantity * price);
        });

        $(document).on('input', '.price', function() {
            var price = $(this).val();
            var quantity = $(this).parents('div.row').find('.quantity').val();
            $(this).parents('div.row').find('.total-price').val(quantity * price);
        });

        function viewPoImages(id) {
            if (id != '') {
                $('#viewImagesBody').empty();
                $('#viewImagesBody').load('{{ url('getPOImages/') }}' + '/' + id, function() {});
            }
        }


        $('#actionsModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var po_id = button.data('po-id');
            var modal = $(this)

            modal.find('.modal-content #po_id').val(po_id);
        })


        $('#PoRejectModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var po_id = button.data('reject-po-id');
            var modal = $(this)

            modal.find('.modal-content #po_reject_id').val(po_id);
        })
    </script>
    <script>
        var selectedOpds = [];
        $('#approvePosButton').click(function() {
            selectedOpds.length = 0;
            if (confirm("Are you sure you want to Proceed?")) {

                $('.selectedPos:checkbox:checked').each(function() {
                    // var sThisVal = (this.checked ? $(this).val() : "");
                    selectedOpds.indexOf($(this).val()) === -1 ? selectedOpds.push($(this).val()) : console
                        .log("This item already exists");

                    // selectedOpds.push($(this).val());

                });
                $.ajax({
                    url: "/approveMultiplePos",
                    type: "post",
                    data: {
                        'pos': selectedOpds,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response) {
                            window.location.reload();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("an error occured!");
                    }
                });
            }
        })
    </script>
    <script>
        $('#poEditModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var po_id = button.data('id');
            var description = button.data('description');
            var quantity = button.data('quantity');
            var price = button.data('price');
            var total_price = button.data('total_price');
            var date = button.data('date');
            var modal = $(this)

            // Set values in edit popup
            $('#poFormEdit').attr('action', 'PO/' + po_id);
            modal.find('.modal-content #po_id_edit').val(po_id);
            modal.find('.modal-content #description_edit').val(description);
            modal.find('.modal-content #quantity_edit').val(quantity);
            modal.find('.modal-content #price_edit').val(price);
            modal.find('.modal-content #total_price_edit').val(total_price);
            modal.find('.modal-content #date_edit').val(date);
        });
    </script>
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
