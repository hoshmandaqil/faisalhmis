@extends('layouts.master')

@section('page_title')
    Laboratory Tests Report
@endsection

@section('page-action')
    <button class="btn btn-warning btn-sm"
        data-toggle="modal"
        data-target="#exampleModal"
        type="button">
        Create Report
    </button>
    <button class="btn btn-sm btn-dark "
        onclick="window.print()">Print</button>
@endsection
@section('styles')
    <!-- Bootstrap Select CSS -->
    <link href="{{ asset('assets/vendor/bs-select/bs-select.css') }}"
        rel="stylesheet" />
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

@section('on_print_page_header')
    @include('layouts.page_header_print', ['reportName' => 'Laboratory Tests Report', 'from' => 'Beginning', 'to' => 'Today'])
@endsection

@section('content')

    <!-- Row start -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}"
                role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            @if ($departmentName == null)
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table class="table"
                        id="scrollVertical">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Test Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Payable</th>
                                <th>Normal Range</th>
                                <th>Department Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalPrice = 0;
                            $totalQuantity = 0; ?>
                            @foreach ($labTests as $test)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->dep_name }}</td>
                                    <td>{{ $test->quantity }}</td>
                                    <td>{{ $test->price }}</td>
                                    <td>{{ $discount = ($test->price * $test->mainDepartment->discount) / 100 }}</td>
                                    <td>{{ $test->price - $discount }}</td>
                                    <td>{{ $test->normal_range }}</td>
                                    <td>{{ $test->mainDepartment->dep_name }}</td>
                                    <?php
                                    $totalQuantity += $test->quantity;
                                    $totalPrice += $test->price;
                                    ?>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="1"></td>
                                <td class="font-weight-bold">Total:</td>
                                <td class="font-weight-bold">{{ number_format($totalQuantity) }}</td>
                                <td class="font-weight-bold">{{ number_format($totalPrice) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade"
        id="exampleModal"
        data-backdrop="static"
        data-keyboard="false"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Laboratory Test Report</h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm"
                        action="{{ url('laboratory_tests_report') }}"
                        method="GET"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="label">Department Name</label>
                            <select class="form-control selectpicker"
                                name="department"
                                data-live-search="true">
                                @foreach ($mainLabDepartments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ $department->id == $departmentName ? 'Selected' : '' }}>{{ $department->dep_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-secondary"
                                data-dismiss="modal"
                                type="button">Close</button>

                            <button class="btn btn-primary submit-btn"
                                type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <!-- Bootstrap Select JS -->
    <script src="{{ asset('assets/vendor/bs-select/bs-select.min.js') }}"></script>
@endsection
