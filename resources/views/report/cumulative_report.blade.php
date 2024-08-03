@extends('layouts.master')

@section('page_title')
    General Profits Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
    <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>
@endsection
@section('styles')
    <style>
        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        .lab_tests_table tr td {
            border: none !important;
        }

        .lab_tests_td {
            width: 30%;
        }

        .lab_tests_tr {
            display: inline-block;
            height: 30px;
        }

        .patient_number {
            font-size: 11px
        }
    </style>
    <!-- Pricing css -->
    <link rel="stylesheet" href="{{ asset('assets/css/pricing.css') }}">
@endsection

@section('on_print_page_header')
    @include('layouts.page_header_print', [
        'reportName' => 'General Incomes Report',
        'from' => $from,
        'to' => $to,
    ])
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
            @if (empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table">
                        <thead>
                            <tr>
                                <th>Dates: {{ $from }} - {{ $to }}</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Patients</td>
                                <td>{{ number_format($count_patients) }}</td>
                            </tr>
                            <tr>
                                <td>OPD Income</td>
                                <td>{{ number_format($sumOPD) }}</td>
                            </tr>
                            <tr>
                                <td>Other Incomes</td>
                                <td>{{ number_format($other_incomes) }}</td>
                            </tr>
                            <tr>
                                <td>IPD Income</td>
                                <td>{{ number_format($totalProfitIPD) }}</td>
                            </tr>
                            <tr>
                                <td>Pharmacy</td>
                                <td>{{ number_format($sumPharmacy) }}</td>
                            </tr>
                            <tr>
                                <td>Labratory</td>
                                <td>{{ number_format($sumLabratory) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total (Patients Not Calculated): </strong></td>
                                <td>
                                    <strong>{{ number_format(floor($sumOPD) + floor($other_incomes) + floor($totalProfitIPD) + floor($sumPharmacy) + floor($sumLabratory)) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Date Wise General income Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('cumulative_report') }}" method="GET" enctype="multipart/form-data"
                        id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from"
                                value="{{ $from != null ? $from : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to"
                                value="{{ $to != null ? $to : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Registerd By:</label>
                            <select class="form-control" name="registered_by">
                                <option value="0" {{ $registered_by == 0 ? 'selected' : '' }}>All</option>
                                @foreach ($patientsRegisteredBy as $key => $registeredBy)
                                    <option value="{{ $key }}" {{ $registered_by == $key ? 'selected' : '' }}>
                                        {{ $registeredBy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control" name="doctor_id">
                                <option value="0" {{ $doctor_id == 0 ? 'selected' : '' }}>All</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ $doctor_id == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
