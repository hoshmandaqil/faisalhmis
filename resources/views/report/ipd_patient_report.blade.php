@extends('layouts.master')

@section('page_title')
    IPD Patients Report
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

        .ipd-total-row {
            border-bottom: 1px solid black;
        }
    </style>
@endsection

@section('on_print_page_header')
    @include('layouts.page_header_print', ['reportName' => 'IPD Patients Report', 'from' => $from, 'to' => $until])
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
                                <th>#ID</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>Age</th>
                                <th>Marital Stauts</th>
                                <th>Blood Group</th>
                                <th>Doctor</th>
                                <th>Admission Date</th>
                                <th>Discharge Date</th>
                                <th>Amount</th>
                                <th>Registered By</th>
                                <th>Actions</th>
                                <th>Discharged By</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotalIPDPrices = 0; ?>
                            @foreach ($ipdPatients as $patient)
                                <?php $totalPrice = 0;
                                $totalDiscount = 0; ?>
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $patient->patient_generated_id }}</td>
                                    <td>{{ ucfirst($patient->patient_name) }}</td>
                                    <td>{{ $patient->patient_fname }}</td>
                                    <td>{{ $patient->age }}</td>
                                    <td>
                                        @if ($patient->marital_status != null)
                                            {{ $patient->marital_status == 0 ? 'Single' : 'Married' }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>{{ $patient->blood_group }}</td>
                                    <td>{{ $patient->doctor_id != null ? $patient->doctor->name : 'Not Added' }}</td>
                                    <td>
                                        @foreach ($patient->ipds as $ipd)
                                            {{ date('Y-m-d', strtotime($ipd->created_at)) }} <br />
                                        @endforeach

                                    </td>
                                    <td>
                                        @foreach ($patient->ipds as $ipd)
                                            {{ $ipd->discharge_date != null ? $ipd->discharge_date : 'Not Yet' }} <br />
                                        @endforeach
                                    </td>

                                    <td>
                                        @foreach ($patient->ipds as $ipd)
                                            <?php
                                            $totalPrice = 0;
                                            $totalDiscount = 0;
                                            $ipdDays = 1;
                                            $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipd->created_at)));
                                            if ($ipd->discharge_date != null) {
                                                $to = $ipd->discharge_date;
                                            } else {
                                                $to = \Carbon\Carbon::parse(date('Y-m-d'));
                                            }
                                            $ipdDays = $register_date->diffInDays($to);
                                            ?>
                                            <table class="ipd_table">
                                                @for ($i = 1; $i <= $ipdDays; $i++)
                                                    <tr class="ipd_tr">
                                                        <td> {{ $i }} Day</td>
                                                        <td> {{ $ipd->price }}</td>
                                                        <?php $totalPrice += $ipd->price;
                                                        $discountForTest = ($ipd->discount * $ipd->price) / 100;
                                                        $totalDiscount += $discountForTest;
                                                        ?>
                                                    </tr>
                                                @endfor
                                                <tr class="ipd-total-row">
                                                    <td><b>Total: <br> Discount:</b></td>
                                                    <td><b>{{ $totalPrice }} <br> {{ $totalDiscount }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Payable: </b></td>
                                                    <td><b>{{ $totalPrice - $totalDiscount }} AFN</b></td>
                                                    <?php $grandTotalIPDPrices += $totalPrice - $totalDiscount; ?>
                                                </tr>

                                            </table>
                                        @endforeach

                                    </td>
                                    <td>{{ $patient->createdBy->name }}</td>
                                    <td>{{ $patient->ipd->discharged_by != null ? $patient->ipd->dischargedBy->name : 'No User' }}</td>

                                    <td>
                                        @if ($patient->ipd->status == 1)
                                            <span class="badge badge-danger">Discharged</span>
                                        @else
                                            <span class="badge badge-success">Admitted</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="9"></td>
                                <td class="font-weight-bold">Total:</td>
                                <td class="font-weight-bold text-center">{{ number_format($grandTotalIPDPrices) }}</td>
                                <td></td>
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
                        id="exampleModalLabel">Date Wise IPD Report</h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm"
                        action="{{ url('ipd_patient_report') }}"
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
                                value="{{ $until != null ? $until : date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="label">Charge Type:</label>
                            <select class="form-control"
                                name="charge_type">
                                <option value="0"
                                    {{ $charge_type == 0 ? 'selected' : '' }}>General</option>
                                <option value="1"
                                    {{ $charge_type == 1 ? 'selected' : '' }}>Admitted</option>
                                <option value="2"
                                    {{ $charge_type == 2 ? 'selected' : '' }}>Discharged</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control"
                                name="doctor_id">
                                <option value="0"
                                    {{ $doctor_id == 0 ? 'selected' : '' }}>All</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ $doctor->id == $doctor_id ? 'selected' : '' }}>{{ $doctor->name }}</option>
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
