@extends('layouts.master')

@section('page_title')
    Sale Report
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
    @include('layouts.page_header_print', ['reportName' => 'Sale Report', 'from' => $from, 'to' => $to])
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
            @if (empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered"
                        id="scrollVertical">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Patient Name</th>
                                <th>Patient ID</th>
                                <th>Medicine Name</th>
                                <th>QTY</th>
                                <th>Sale Price</th>
                                <th>Total</th>
                                <th>Grand Total</th>
                                <th>Sold By</th>
                                <th>Registered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $allGrandTotal = 0; ?>
                            @foreach ($pharmacies as $patient_medicines)
                                <?php
                                $grandTotal = 0;
                                foreach ($patient_medicines as $calculateGrandTotal) {
                                    $grandTotal += $calculateGrandTotal->quantity * $calculateGrandTotal->unit_price;
                                }
                                $allGrandTotal += $grandTotal;
                                ?>

                                @foreach ($patient_medicines as $key => $pharmacy)
                                    <tr style="border-bottom:{{ $loop->last ? '1px solid black' : '0' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pharmacy->patient->patient_name }}</td>
                                        <td>{{ $pharmacy->patient->patient_generated_id }}</td>
                                        <td>{{ $pharmacy->medicine->medicine_name }}</td>
                                        <td>{{ $pharmacy->quantity }}</td>
                                        <td>{{ $pharmacy->unit_price }}</td>
                                        <td>{{ $pharmacy->quantity * $pharmacy->unit_price }}</td>
                                        @if ($key == 0)
                                            <td class="text-center"
                                                rowspan="{{ count($patient_medicines) }}"><b>{{ $grandTotal }}</b></td>
                                        @endif
                                        <td>{{ $pharmacy->user->name }}</td>
                                        <td>{{ $pharmacy->patient->createdBy != null ? $pharmacy->patient->createdBy->name : 'No User' }}</td>

                                    </tr>
                                @endforeach
                            @endforeach

                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><b>Total:</b></td>
                            <td class="text-center"><b>{{ number_format($allGrandTotal) }}</b></td>
                            <td></td>
                            <td></td>
                        </tr>
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
                        id="exampleModalLabel">Date Wise Sell Report</h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm"
                        action="{{ url('date_wise_sale_report') }}"
                        method="GET"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control"
                                name="from"
                                type="date"
                                value="{{ $from != null ? $from : date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control"
                                name="to"
                                type="date"
                                value="{{ $to != null ? $to : date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="label">Report Type:</label>
                            <select class="form-control"
                                id="type"
                                name="type"
                                required>
                                <option value="in_patients"
                                    {{ isset($_GET['type']) && $_GET['type'] == 'in_patients' ? 'selected' : '' }}>In-Patients</option>
                                <option value="out_patients"
                                    {{ isset($_GET['type']) && $_GET['type'] == 'out_patients' ? 'selected' : '' }}>Out-Patients</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control"
                                id="doc"
                                name="doc">
                                <option value=""></option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ isset($_GET['doc']) && $_GET['doc'] == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
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
@endsection
