@extends('layouts.master')

@section('page_title')
    Purchase Orders
@endsection

@section('page-action')
    @if (in_array('PO Creation', $user_permissions))
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
            Add New PO
        </button>
    @endif

    {{-- @if (in_array('PO_approve', $user_permissions))
        <a href="#" class="btn btn-success btn-sm pull-right" id="approvePosButton">Approve Selected POs</a>
    @endif --}}

    @if (\Request::is('search_patient_list'))
        <a type="button" class="btn btn-danger btn-sm" href="{{ route('patient.index') }}">
            Clear Search
        </a>
    @endif
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

    <div class="row">
        <div class="table-responsive">
            <table
                class="table table-sm table-rounded border table-striped table-row-bordered table-column-bordered gs-7 gy-3"
                id="pageTable">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200 text-center">
                        <th>No.</th>
                        <th>PO NO</th>
                        <th>Requested</th>
                        <th>PO Date</th>
                        <th>Inserted By</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Expense</th>
                        <th>file</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <x-general.no-record :data="$pos" />

                    @foreach ($pos as $po)
                        <tr class="text-center">
                            <td>{{ ($pos->currentpage() - 1) * $pos->perpage() + $loop->index + 1 }}</td>
                            <td>{{ $po->id }}</td>
                            <td>{{ $po->po_by }}</td>
                            <td>{{ $po->date }}</td>
                            <td>{{ $po->insertedByUser->name }}</td>
                            <td>{{ number_format($po->total_amount, 2) }} AF</td>
                            <td class="font-parastoo" dir="rtl">{{ $po->remarks }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-primary' => $po->status() == 'Issued',
                                    'badge-info' => $po->status() == 'Checked',
                                    'badge-warning' => $po->status() == 'Verified',
                                    'badge-success' => $po->status() == 'Approved',
                                    'badge-danger' => $po->status() == 'Rejected',
                                ])>
                                    {{ $po->status() }}
                                </span>
                            </td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-success' => $po->expenses()->exists(),
                                    'badge-danger' => !$po->expenses()->exists(),
                                ])>{{ $po->expenses()->exists() ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <span>
                                    @if ($po->files->isNotEmpty())
                                        <i class="icon-check-circle text-success"></i>
                                    @else
                                        <i class="icon-x text-danger"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-warning btn-sm dropdown-toggle" id="btnGroupDrop1"
                                            data-toggle="dropdown" type="button" aria-haspopup="true"
                                            aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            @if (in_array('View Purchase Order', $user_permissions))
                                                <a class="dropdown-item px-3" href="#"
                                                    onclick="viewPO({{ $po->id }})" data-toggle="modal"
                                                    data-target="#viewModal">
                                                    View
                                                </a>
                                            @endif
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                data-target="#expenseFiles" data-expense="{{ $po }}"
                                                data-sum-paid="{{ number_format($po->id) }}">
                                                Files/Attachments
                                            </a>
                                            <a class="dropdown-item px-3" href="#"
                                                onclick="openManageStatusModal({{ $po }}, '{{ $po->status() }}')">
                                                Manage Status
                                            </a>
                                            @if ($po->status() == 'Issued')
                                                @if (in_array('Edit Purchase Order', $user_permissions))
                                                    <a type="button" class="dropdown-item px-3 edit-po"
                                                        data-po="{{ $po }}" data-toggle="modal"
                                                        data-target="#editModal">
                                                        Edit
                                                    </a>
                                                    {{-- <button type="button" class="btn btn-warning btn-sm edit-po"
                                                    data-id="{{ $po->id }}" data-toggle="modal"
                                                    data-target="#editModal">Edit</button> --}}
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        {{ $pos->links() }}
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Purchase Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="poForm" action="{{ route('PO.store') }}" class="p-3" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Main Fields --}}
                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">PO Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="form-group col-md-3">
                                        <label>PO Requested By <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="po_by" name="po_by"
                                            required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" id="date"
                                            value="{{ date('Y-m-d') }}" name="date" required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Supporting Document</label>
                                        <input class="form-control-file" id="file" type="file"
                                            name="file"="image/*,.pdf">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Remarks <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="remarks" name="remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">PO Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="poItems">
                                    <div class="row mb-4 po-item-row">
                                        <div class="col-md-1 d-flex justify-content-start align-items-center">
                                            <a class="btn btn-icon btn-sm btn-primary mt-7 add-item" href="#">
                                                <i class="icon-plus"></i>
                                            </a>
                                            <a class="btn btn-icon btn-sm btn-danger mt-7 remove-item ml-2" href="#"
                                                style="display: none;">
                                                <i class="icon-minus"></i>
                                            </a>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Description <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="description[]" required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Amount <span class="text-danger">*</span></label>
                                            <input class="form-control amount" type="number" step="0.1"
                                                name="amount[]" required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Quantity <span class="text-danger">*</span></label>
                                            <input class="form-control quantity" type="number" name="quantity[]"
                                                required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Remarks</label>
                                            <input class="form-control" type="text" name="item_remarks[]">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Subtotal</label>
                                            <input class="form-control subtotal" type="text" name="subtotal[]"
                                                readonly disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-5">
                            <div class="card-body center text-center">
                                <h3>Total: <span id="total">0</span> AFN</h3>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button class="btn btn-lg btn-primary mb-5" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewPoImages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Files </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewImagesBody">
                    loading...
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="PoRejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reject Reason </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('po_reject') }}" method="POST" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="row">
                            <input type="hidden" name="po_reject_id" id="po_reject_id">
                            <div class="form-group col-12">
                                <label>Reject Reason:</label>
                                <textarea name="reject_comment" id=""class="form-control" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger btn-sm">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="actionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">PO Actions </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="actionsModalBody">
                    <form action="{{ url('po_actions') }}" method="POST" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="po_id" id="po_id">
                        <div class="row">
                            <div class="form-group col-3 text-center">
                                <label>Check</label>
                                <input class="form-control" type="checkbox" name="po_checked"
                                    {{ in_array('PO_Check', $user_permissions) ? '' : 'disabled' }}>
                            </div>
                            <div class="form-group col-3 text-center">
                                <label>Verify</label>
                                <input class="form-control" type="checkbox" name="po_verified"
                                    {{ in_array('PO_verify', $user_permissions) ? '' : 'disabled' }}>
                            </div>
                            <div class="form-group col-3 text-center">
                                <label>Approve</label>
                                <input class="form-control" type="checkbox" name="po_approve"
                                    {{ in_array('PO_approve', $user_permissions) ? '' : 'disabled' }}>
                            </div>
                            <div class="form-group col-3">
                                <br>
                                <button type="submit" class="btn btn-info">Save</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Edit PO Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Purchase Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPoForm" action="{{ route('PO.update', ['PO' => ':id']) }}" class="p-3"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Hidden field for PO ID -->
                        <input type="hidden" id="edit_po_id" name="po_id">

                        {{-- Main Fields --}}
                        <div class="card mb-5">
                            <div class="card-header">
                                <h5 class="card-title">Edit PO Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- Use the same fields as the create modal -->
                                <div class="row mb-4">
                                    <div class="form-group col-md-4">
                                        <label>PO Requested By <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="edit_po_by" name="po_by"
                                            required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="edit_date" name="date"
                                            required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="form-group col-md-12">
                                        <label>Remarks <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="edit_remarks" name="remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Use the same PO items section -->
                        <div id="editPoItems"></div>

                        <!-- Total calculation -->
                        <div class="card mb-5">
                            <div class="card-body center text-center">
                                <h3>Total: <span id="edit_total">0</span> AFN</h3>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button class="btn btn-lg btn-primary mb-5" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View PO Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="printPayment">
                <div class="modal-header d-none d-print-block">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mr-auto">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="" style="height: 50px"
                                class="mb-4">
                        </div>
                        <div class="text-center mx-auto">
                            <h5 class="modal-title">Ministry of Public Health</h5>
                            <h5 class="modal-title">Faisal Curative Hospital</h5>
                            <h5 class="modal-title">Finance Department</h5>
                            <h5 class="modal-title">Purchase Order Voucher</h5>
                        </div>
                        <div class="ml-auto">
                            <img src="{{ asset('assets/img/logo/mlogo.png') }}" alt="" style="height: 50px"
                                class="mb-4">
                        </div>
                    </div>
                </div>
                <div class="modal-header d-print-none">
                    <h5 class="modal-title" id="viewModalLabel">View Purchase Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- PO details will be loaded here -->
                </div>
                <div class=" modal-footer d-print-none">
                    <div class="d-flex justify-content-between m-4">
                        <button type="button" class="btn btn-secondary mr-4" data-dismiss="modal">Close</button>
                        <button onclick="printDiv('printPayment')" type="button" class="btn btn-primary">Print</button>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manageStatusModal" tabindex="-1" role="dialog"
        aria-labelledby="manageStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageStatusModalLabel">Manage PO Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- The content you provided will go here -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="2">
                                            <h5>PO No</h5>
                                            <h6 id="modal-po-id"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>PO Description</h5>
                                            <h6 id="modal-po-description"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>Total Amount </h5>
                                            <h6 id="modal-po-total-amount"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>Status </h5>
                                            <h6 class="text-danger" id="modal-po-status"></h6>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>Status</th>
                                        <th>By</th>
                                        <th>Date</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Checked</td>
                                        <td id="checked-by">X</td>
                                        <td id="checked-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" id="check-button">Check</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Verified</td>
                                        <td id="verified-by">X</td>
                                        <td id="verified-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" id="verify-button">Verify</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Approved</td>
                                        <td id="approved-by">X</td>
                                        <td id="approved-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" id="approve-button">Approve</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Rejected</td>
                                        <td id="rejected-by">X</td>
                                        <td id="rejected-date">X</td>
                                        <td id="reject-comment"></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" id="reject-button">Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>
    {{-- View Expense Files --}}

    <script>
        $(document).ready(function() {
            // Add new PO item row
            $('#poItems').on('click', '.add-item', function(e) {
                e.preventDefault();
                let row = $(this).closest('.po-item-row').clone();
                row.find('input').val('');
                row.find('.remove-item').show();
                $(this).closest('.po-item-row').after(row);
            });

            // Remove PO item row
            $('#poItems').on('click', '.remove-item', function(e) {
                e.preventDefault();
                $(this).closest('.po-item-row').remove();
                calculateTotal();
            });

            // Calculate total on keyup
            $('#poItems').on('keyup', '.amount, .quantity', function() {
                let row = $(this).closest('.po-item-row');
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let subtotal = amount * quantity;
                row.find('.subtotal').val(subtotal.toFixed(2));
                calculateTotal();
            });

            // Calculate the total for all rows
            function calculateTotal() {
                let total = 0;
                $('.po-item-row').each(function() {
                    let subtotal = parseFloat($(this).find('.subtotal').val()) || 0;
                    total += subtotal;
                });
                $('#total').text(total.toFixed(2));
            }
        });
    </script>

    <script>
        $('body').on('focus', ".persianDate", function() {
            $(this).persianDatepicker();
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
        $(document).on('click', '.edit-po', function() {
            let po = $(this).data('po');
            // Fetch the PO data using AJAX

            // Populate the modal fields
            $('#edit_po_id').val(po.id);
            $('#edit_po_by').val(po.po_by);
            $('#edit_date').val(po.date);
            $('#edit_remarks').val(po.remarks);

            // Clear old PO items
            $('#editPoItems').html('');

            // Loop through PO items and populate fields
            $.each(po.items, function(index, item) {
                let newRow = `
                        <div class="row mb-4 po-item-row">
                            <div class="col-md-1 d-flex justify-content-start align-items-center">
                                <a class="btn btn-icon btn-sm btn-primary mt-7 add-edit-item" href="#">
                                    <i class="icon-plus"></i>
                                </a>
                                <a class="btn btn-icon btn-sm btn-danger mt-7 remove-edit-item ml-2" href="#">
                                    <i class="icon-minus"></i>
                                </a>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Description <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="description[]" value="${item.description}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input class="form-control amount" type="number" step="0.1" name="amount[]" value="${item.amount}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input class="form-control quantity" type="number" name="quantity[]" value="${item.quantity}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Remarks</label>
                                <input class="form-control" type="text" name="item_remarks[]" value="${item.remarks}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Subtotal</label>
                                <input class="form-control subtotal" type="text" name="subtotal[]" value="${item.subtotal}" readonly disabled>
                            </div>
                        </div>`;
                $('#editPoItems').append(newRow);
            });

            // Calculate total
            calculateEditTotal();

            // Open the modal
            $('#editModal').modal('show');
        });

        // Add new PO item row in edit modal
        $('#editPoItems').on('click', '.add-edit-item', function(e) {
            e.preventDefault();
            let newRow = `
            <div class="row mb-4 po-item-row">
                <div class="col-md-1 d-flex justify-content-start align-items-center">
                    <a class="btn btn-icon btn-sm btn-primary mt-7 add-edit-item" href="#">
                        <i class="icon-plus"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-danger mt-7 remove-edit-item ml-2" href="#">
                        <i class="icon-minus"></i>
                    </a>
                </div>
                <div class="form-group col-md-3">
                    <label>Description <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="description[]" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Amount <span class="text-danger">*</span></label>
                    <input class="form-control amount" type="number" step="0.1" name="amount[]" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input class="form-control quantity" type="number" name="quantity[]" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Remarks</label>
                    <input class="form-control" type="text" name="item_remarks[]">
                </div>
                <div class="form-group col-md-2">
                    <label>Subtotal</label>
                    <input class="form-control subtotal" type="text" name="subtotal[]" readonly disabled>
                </div>
            </div>`;
            $(this).closest('.po-item-row').after(newRow);
        });

        // Remove PO item row in edit modal
        $('#editPoItems').on('click', '.remove-edit-item', function(e) {
            e.preventDefault();
            $(this).closest('.po-item-row').remove();
            calculateEditTotal();
        });

        // Calculate total on keyup in edit modal
        $('#editPoItems').on('keyup', '.amount, .quantity', function() {
            let row = $(this).closest('.po-item-row');
            let amount = parseFloat(row.find('.amount').val()) || 0;
            let quantity = parseFloat(row.find('.quantity').val()) || 0;
            let subtotal = amount * quantity;
            row.find('.subtotal').val(subtotal.toFixed(2));
            calculateEditTotal();
        });

        // Function to calculate total in edit modal
        function calculateEditTotal() {
            let total = 0;
            $('#editPoItems .po-item-row').each(function() {
                let amount = parseFloat($(this).find('.amount').val()) || 0;
                let quantity = parseInt($(this).find('.quantity').val()) || 0;
                let subtotal = amount * quantity;
                $(this).find('.subtotal').val(subtotal.toFixed(2));
                total += subtotal;
            });
            $('#edit_total').text(total.toFixed(2));
        }
    </script>
    <script>
        // Function to view PO details
        function viewPO(poId) {
            console.log('Viewing PO with ID:', poId);
            $.ajax({
                url: `/PO/${poId}`,
                type: 'GET',
                success: function(response) {
                    console.log('Received response:', response);
                    $('#viewModal .modal-body').html(response);
                    $('#viewModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PO details:', xhr.responseText);
                    console.error('Status:', status);
                    console.error('Error:', error);
                    alert('Error fetching PO details. Please check the console for more information.');
                }
            });
        }
    </script>
    <script>
        function openManageStatusModal(po, status) {
            // Populate modal with PO data
            $('#modal-po-id').text(po.id);
            $('#modal-po-description').text(po.remarks);
            $('#modal-po-total-amount').text(po.total_amount);
            $('#modal-po-status').text(status);

            // Populate status data
            $('#checked-by').text(po.checked_by || 'X');
            $('#checked-date').text(po.checked_date || 'X');
            $('#verified-by').text(po.verified_by || 'X');
            $('#verified-date').text(po.verified_date || 'X');
            $('#approved-by').text(po.approved_by || 'X');
            $('#approved-date').text(po.approved_date || 'X');
            $('#rejected-by').text(po.rejected_by || 'X');
            $('#rejected-date').text(po.rejected_date || 'X');
            $('#reject-comment').text(po.reject_comment || '');

            // Show/hide buttons based on current status
            updateStatusButtons(status);

            // Open the modal
            $('#manageStatusModal').modal('show');
        }

        function updateStatusButtons(currentStatus) {
            // Hide all buttons first
            $('#check-button, #verify-button, #approve-button, #reject-button').hide();

            // Show appropriate buttons based on current status
            switch (currentStatus) {
                case 'Issued':
                    $('#check-button').show();
                    break;
                case 'Checked':
                    $('#verify-button').show();
                    break;
                case 'Verified':
                    $('#approve-button').show();
                    $('#reject-button').show();
                    break;
            }
        }

        // Add click handlers for status buttons
        $('#check-button, #verify-button, #approve-button, #reject-button').click(function() {
            let action = $(this).text().toLowerCase();
            let poId = $('#modal-po-id').text();
            updatePOStatus(poId, action);
        });

        function updatePOStatus(poId, status) {
            $.ajax({
                url: '/po_status',
                type: 'POST',
                data: {
                    po_id: poId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Status updated successfully');
                    $('#manageStatusModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error('Error updating PO status:', error);
                    alert('Error updating PO status. Please try again.');
                }
            });
        }
    </script>
@endsection
