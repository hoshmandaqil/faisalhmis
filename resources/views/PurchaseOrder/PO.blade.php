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

    @if (in_array('PO_approve', $user_permissions))
        <a href="#" class="btn btn-success btn-sm pull-right" id="approvePosButton">Approve Selected POs</a>
    @endif

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
                        <th>Category</th>
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
                            <td>{{ $po->expenseCategory->name ?? '' }}</td>
                            <td>{{ $po->po_by }}</td>
                            <td>{{ $po->date }}</td>
                            <td>{{ $po->insertedByUser->name }}</td>
                            <td>{{ number_format($po->total_amount, 2) }} AF</td>
                            <td class="font-parastoo">{{ $po->remarks }}</td>
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
                                        <i class="bi bi-check2-circle badge badge-circle badge-success fs-4"></i>
                                    @else
                                        <i class="bi bi-x-lg badge badge-circle badge-danger fs-4"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <x-buttons.dropdown width="200" :delete="auth()->user()->hasPermissionTo('Delete PO')
                                    ? route('PO.destroy', [$application->slug, $po->id])
                                    : false">
                                    <a class="menu-link px-3" href="#"
                                        x-on:click="$store.poView.viewPO({{ json_encode($po) }}, {{ json_encode($po->status()) }})">
                                        View
                                    </a>
                                    <a class="menu-link px-3" href="#"
                                        x-on:click="$store.files.openModal({{ json_encode($po) }}, {{ json_encode($po->status()) }})">
                                        Files/Attachements
                                    </a>
                                    <a class="menu-link px-3" href="#"
                                        x-on:click="$store.status.openModal({{ json_encode($po) }}, {{ json_encode($po->status()) }})">
                                        Manage Status
                                    </a>
                                    @if ($po->status() == 'Issued')
                                        @if (auth()->user()->hasPermissionTo('Edit PO'))
                                            <a class="menu-link px-3" href="#"
                                                x-on:click="$store.form.editForm({{ json_encode($po) }})">
                                                Edit
                                            </a>
                                        @endif
                                    @endif
                                </x-buttons.dropdown>
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

    <!-- Modal -->
    <div id="use-vue">
        <create-purchase-order />
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
