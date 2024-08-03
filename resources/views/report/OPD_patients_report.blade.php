@extends('layouts.master')

@section('page_title')
    OPD Patients Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
        <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

@endsection
@section('styles')
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
        }
        .lab_tests_table tr td {
            border: none !important;
        }
        .lab_tests_td {
            width: 30%;
        }
        .lab_tests_tr {
            display:inline-block;
            height:30px;
        }
        .ipd-total-row {
            border-bottom: 1px solid black;
        }
    </style>
@endsection



@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'OPD Patients Report', 'from' => $from, 'to' => $to])
@endsection

@section('content')

    <!-- Row start -->
    @if(session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            @if(empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Phone Number</th>
                            <th>Age</th>
                            <th>Blood Group</th>
                            <th>Doctor</th>
                            <th>OPD Fee</th>
                            <th>Registered By</th>
                            <th>Registeration Date</th>

                        </tr>
                        </thead>
                        <tbody>
                            <?php $totalOPDFee = 0;?>
                        @foreach($OPDPatients  as $patient)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$patient->patient_generated_id}}</td>
                                <td>{{ucfirst($patient->patient_name)}}</td>
                                <td>{{$patient->patient_fname}}</td>
                                <td>{{$patient->patient_phone}}</td>
                                <td>{{$patient->age}}</td>
                                <td>{{$patient->blood_group}}</td>
                                <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>
                                <td>{{$patient->OPD_fee}}
                                    <?php $totalOPDFee += $patient->OPD_fee?>
                                </td>
                                <td>{{($patient->createdBy != NULL) ? $patient->createdby->name : 'Not Added'}}</td>
                                <td>{{date('Y-m-d H:i:s', strtotime($patient->created_at))}}</td>

                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="8" class="text-right"><b>Total OPD:</b></td>
                            <td><b>{{ $totalOPDFee }}</b></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Date Wise OPD Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('OPD_fee_report')}}" method="GET" enctype="multipart/form-data" id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from" value="{{($from != NULL) ? $from : date('Y-m-d')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to" value="{{($to != NULL) ? $to : date('Y-m-d')}}" required>
                        </div>

                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control" name="doctor_id">
                                <option value="0" {{($doctor_id ==0) ? 'selected': ''}}>All</option>
                               @foreach ($doctors as $doctor)
                               <option value="{{ $doctor->id }}" {{ ($doctor->id == $doctor_id) ? 'selected' : ''}}>{{ $doctor->name }}</option>
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
