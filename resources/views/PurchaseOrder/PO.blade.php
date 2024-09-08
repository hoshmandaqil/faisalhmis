@extends('layouts.master')

@section('page_title')
    Purchase Orders
@endsection

@section('page-action')
    @if (in_array('PO Creation', $user_permissions))
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createPOModal">
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
                                <x-buttons.dropdown width="200">
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
                                        {{-- @if (auth()->user()->hasPermissionTo('Edit PO')) --}}
                                        <a class="menu-link px-3" href="#"
                                            x-on:click="$store.form.editForm({{ json_encode($po) }})">
                                            Edit
                                        </a>
                                        {{-- @endif --}}
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
        <handle-purchase-order />
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>

    <script>
        $('body').on('focus', ".persianDate", function() {
            $(this).persianDatepicker();
        });
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
@endsection
