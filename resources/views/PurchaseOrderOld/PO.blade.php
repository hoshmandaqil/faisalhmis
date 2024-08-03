@extends('layouts.master')

@section('page_title')
    Purchase Orders (Old HMS)
@endsection

@section('page-action')
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
                    <form action="{{ url('searchPO_old') }}" method="post">
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


    @if (Illuminate\Support\Facades\Route::is('Old_PO.index'))
        <div class="row gutters">
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <div class="notify danger">
                    <div class="notify-body">
                        <span class="type"><a href="{{ url('approvedPOs') }}"class="text-white">Approved POs
                            </a></span>
                        <div class="notify-title">Total Approved Pos: <a href="{{ url('approvedPOs') }}">
                                <b>{{ $approvedPos }}</b></a></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <div class="notify danger">
                    <div class="notify-body">
                        <span class="type"><a href="{{ url('unapprovedPOs') }}"class="text-white">Unapproved POs
                            </a></span>
                        <div class="notify-title">Total Unapproved Pos: <a href="{{ url('unapprovedPOs') }}">
                                <b>{{ $unapprovedPos }}</b></a></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <div class="notify info">
                    <div class="notify-body">
                        <span class="type"><a href="{{ url('rejectedPOs') }}" class="text-white">Rejected POs </a></span>
                        <div class="notify-title">Total Rejected Pos: <a href="{{ url('rejectedPOs') }}">
                                <b>{{ $rejectedPos }}</b></a></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <div class="notify success">
                    <div class="notify-body">
                        <span class="type"><a href="{{ route('PO.index') }}" class="text-white">All POs </a></span>
                        <div class="notify-title">Total Pos: <a href="{{ route('PO.index') }}">
                                <b>{{ $totalPos }}</b></a></div>
                    </div>
                </div>
            </div>
        </div>
    @endif



    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table id="scrollVertical" class="table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>Date</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Expense Passed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($POs as $po)
                            <tr>
                                <td>{{ $po->id }} - old <span><input type="checkbox" class="selectedPos"
                                            value="{{ $po->id }}"></span></td>
                                <td>{{ $po->description }}</td>
                                <td>{{ $po->quantity }}</td>
                                <td>{{ $po->price }}</td>
                                <td>{{ $po->total_price }}</td>
                                <td>{{ $po->date }}</td>
                                <td>{{ $po->createdBy->name ?? 'Ismail Payenda' }}</td>
                                <td>
                                    @if ($po->approve != 0)
                                        <span class="badge badge-success">Approved</span>
                                    @elseif ($po->comment != null)
                                        <span class="badge badge-danger">Rejected</span>
                                        <span class="badge">{{ $po->comment }}</span>
                                    @else
                                        <span class="badge badge-info">Under Process</span>
                                    @endif
                                </td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>Check</td>
                                            <td>Verfy</td>
                                            <td>Approve</td>
                                        </tr>

                                        <tr>
                                            <td><input disabled type="checkbox" {{ $po->check != null ? 'Checked' : '' }}>
                                            </td>
                                            <td><input disabled type="checkbox" {{ $po->verify != null ? 'Checked' : '' }}>
                                            </td>
                                            <td><input disabled type="checkbox" {{ $po->approve == 1 ? 'Checked' : '' }}>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <td>
                                    <span class="badge badge-danger">Not yet</span>

                                    {{-- @if ($po->approved != 0)
                                    <span class="badge badge-success">Passed</span>
                                    @else
                                    <span class="badge badge-danger">Not yet</span>
                                    @endif --}}
                                </td>


                                <td>
                                    Check the old system
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $POs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                                <label>Item Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description[]" required></textarea>
                            </div>
                            <div class="form-group col-2">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input class="form-control quantity" type="text" name="quantity[]" value="0"
                                    required>
                            </div>
                            <div class="form-group col-2">
                                <label>Price <span class="text-danger">*</span></label>
                                <input class="form-control price" type="text" name="price[]" value="0" required>
                            </div>
                            <div class="form-group col-2">
                                <label>Total Price <span class="text-danger">*</span></label>
                                <input class="form-control total-price" type="text" name="total_price[]"
                                    value="0" readonly>
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

    <div class="modal fade" id="poEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                    <form id="poFormEdit" action="" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="po_id_edit" id="po_id_edit">
                        <div class="row">
                            <div class="form-group col-2">
                                <label>Item Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description_edit" required></textarea>
                            </div>
                            <div class="form-group col-2">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input class="form-control quantity" type="text" name="quantity" id="quantity_edit"
                                    required>
                            </div>
                            <div class="form-group col-2">
                                <label>Price <span class="text-danger">*</span></label>
                                <input class="form-control price" type="text" name="price" id="price_edit"
                                    required>
                            </div>
                            <div class="form-group col-2">
                                <label>Total Price <span class="text-danger">*</span></label>
                                <input class="form-control total-price" type="text" name="total_price"
                                    id="total_price_edit" readonly>
                            </div>
                            <div class="form-group col-2">
                                <label>PO Date <span class="text-danger">*</span></label>
                                <input class="form-control persianDate" autocomplete="off" autofill="off" type="text"
                                    name="date" id="date_edit" required>
                            </div>
                            <div class="form-group col-2">
                                <label>Files </label>
                                <input type="file" name="files" accept='image/*' class="imagesUpload" multiple>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>
    <script>
        function newPo() {
            $('#addMore').append(`

            <div class="row">
                <div class="form-group col-2">
                    <label>Item Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="description[]" required></textarea>
                </div>
                <div class="form-group col-2">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input class="form-control quantity" type="text" name="quantity[]" value ="0" required>
                </div>
                <div class="form-group col-2">
                    <label>Price <span class="text-danger">*</span></label>
                    <input class="form-control price" type="text" name="price[]" value ="0" required>
                </div>
                <div class="form-group col-2">
                    <label>Total Price <span class="text-danger">*</span></label>
                    <input class="form-control total-price" type="text" name="total_price[]" value ="0" readonly>
                </div>

                <div class="form-group col-2">
                    <label>PO Date <span class="text-danger">*</span></label>
                    <input class="form-control persianDate" autocomplete="off" autofill="off" type="text" name="date[]" required>
                </div>

                <div class="form-group col-2">
                    <label>Files</label>
                    <input class="imagesUpload" type="file" accept='image/*' name="files[]" multiple >
                </div>
            </div>
            `);
        }
    </script>
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
@endsection
