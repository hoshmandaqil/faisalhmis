@extends('layouts.master')

@section('page_title')
    My Patient List
@endsection

@section('styles')
    <!-- Bootstrap Select CSS -->
    <link href="{{ asset('assets/vendor/bs-select/bs-select.css') }}" rel="stylesheet" />
    <style>
        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        .readonly-dropdown {
            pointer-events: none;
            background-color: #e9ecef;
        }
    </style>
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{ url('search_my_patient') }}" method="post">
                        @csrf
                        <input class="search-query" name="search_patient" type="text"
                            value="{{ Request::is('search_my_patient') ? $patientSearchDetail : '' }}"
                            placeholder="Search Patient By Id, Name or Phone..." required>
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection
@section('page-action')
    @if (\Request::is('search_my_patient'))
        <a class="btn btn-danger btn-sm" type="button" href="{{ route('my_patients') }}">
            Clear Search
        </a>
    @endif
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
                <table class="table" id="scrollVertical">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Mobile</th>
                            <th>Age</th>
                            <th>Register Date</th>
                            <th>Register By</th>
                            <th>Doctor</th>
                            <th>Blood Group</th>
                            <th>Diagnose</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients  as $patient)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $patient->patient_generated_id }}</td>
                                <td>{{ $patient->patient_name }}</td>
                                <td>{{ $patient->patient_fname }}</td>
                                <td>{{ $patient->patient_phone }}</td>
                                <td>{{ $patient->age }}</td>
                                <td>{{ $patient->created_at }}</td>
                                <td>{{ $patient->created_by != null ? $patient->createdBy->name : 'Not Added' }}</td>
                                <td>{{ $patient->doctor_id != null ? $patient->doctor->name : 'Not Added' }}</td>
                                <td>{{ $patient->blood_group }}</td>
                                <td>{{ $patient->medical_history ?? 'Not Added' }}</td>
                                <td>
                                    @if (!$patient->medicines->isEmpty() && in_array('doctor_edit_sale_medicine', $user_permissions))
                                        <button class="btn btn-sm btn-dark" data-patient-id="{{ $patient->id }}"
                                            data-toggle="modal" data-target="#editPatientMedicine"
                                            data-patient-name="{{ $patient->patient_name }}"
                                            onclick="editMedicine({{ $patient->id }})">View Medicine
                                        </button>
                                    @else
                                        @if (in_array('doctor_sale_medicine', $user_permissions))
                                            <button class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#exampleModal" data-patient-id="{{ $patient->id }}"
                                                data-patient-name="{{ $patient->patient_name }}">Set Medicine
                                            </button>
                                        @endif
                                    @endif

                                    @if ($patient->ipd != null)
                                        <button class="btn btn-sm btn-gplus" data-toggle="modal" data-target="#editIPDModal"
                                            data-patient-id="{{ $patient->id }}"
                                            data-patient-name="{{ $patient->patient_name }}"
                                            data-floor="{{ $patient->ipd->floor->floor_name }}"
                                            data-room="{{ $patient->ipd->floor->room }}"
                                            data-bed="{{ $patient->ipd->floor->bed }}"
                                            data-remark="{{ $patient->ipd->remark }}">View IPD
                                        </button>
                                    @endif

                                    @if (in_array('doctor_sale_ipd', $user_permissions))
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#IPDModal"
                                            data-patient-id="{{ $patient->id }}"
                                            data-patient-name="{{ $patient->patient_name }}">Set IPD
                                        </button>
                                    @endif

                                    @if (!$patient->labs->isEmpty())
                                        <button class="btn btn-sm btn-secondary" data-patient-id="{{ $patient->id }}"
                                            data-toggle="modal" data-target="#editPatientLab"
                                            data-patient-name="{{ $patient->patient_name }}"
                                            onclick="editLab({{ $patient->id }})">View Lab
                                        </button>
                                    @else
                                        @if (in_array('doctor_set_lab', $user_permissions))
                                            <button class="btn btn-sm btn-warning" data-toggle="modal"
                                                data-target="#labModal" data-patient-id="{{ $patient->id }}"
                                                data-patient-name="{{ $patient->patient_name }}"
                                                data-no-discount="{{ $patient->no_discount }}">Set Lab
                                            </button>
                                        @endif
                                    @endif

                                    <button class="btn btn-sm btn-light mt-1" data-toggle="modal"
                                        data-target="#vitalSignsModal" data-patient-id="{{ $patient->id }}"
                                        data-patient-name="{{ $patient->patient_name }}"
                                        data-blood-pressure="{{ $patient->blood_pressure }}"
                                        data-respiration="{{ $patient->respiration_rate }}"
                                        data-pulse="{{ $patient->pulse_rate }}" data-heart="{{ $patient->heart_rate }}"
                                        data-temperature="{{ $patient->temperature }}"
                                        data-weight="{{ $patient->weight }}" data-height="{{ $patient->height }}"
                                        data-mental-state="{{ $patient->mental_state }}"
                                        data-medical-history="{{ $patient->medical_history }}"
                                        data-va-1="{{ $patient->va_1 }}" data-va-2="{{ $patient->va_2 }}"
                                        data-iop-1="{{ $patient->iop_1 }}" data-iop-2="{{ $patient->iop_2 }}"
                                        data-chief-complaint="{{ $patient->chief_complaint }}"
                                        data-dx="{{ $patient->dx }}">Vital Signs</button>

                                    <button class="btn btn-sm btn-light mt-1" data-toggle="modal"
                                        data-target="#editPatientModal" data-id="{{ $patient->id }}"
                                        data-generate-id="{{ $patient->patient_generated_id }}"
                                        data-name="{{ $patient->patient_name }}"
                                        data-fname="{{ $patient->patient_fname }}"
                                        data-mobile="{{ $patient->patient_phone }}"
                                        data-doctor="{{ $patient->doctor_id }}" data-gender="{{ $patient->gender }}"
                                        data-blood="{{ $patient->blood_group }}" data-age="{{ $patient->age }}"
                                        data-marital-status="{{ $patient->marital_status }}"
                                        data-advance="{{ $patient->advance_pay }}"
                                        data-blood-pressure="{{ $patient->blood_pressure }}"
                                        data-respiration="{{ $patient->respiration_rate }}"
                                        data-pulse="{{ $patient->pulse_rate }}" data-heart="{{ $patient->heart_rate }}"
                                        data-temperature="{{ $patient->temperature }}"
                                        data-weight="{{ $patient->weight }}" data-height="{{ $patient->height }}"
                                        data-mental-state="{{ $patient->mental_state }}"
                                        data-medical-history="{{ $patient->medical_history }}"
                                        data-default-discount="{{ $patient->no_discount }}" href="#">
                                        Diagnose</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center font-weight-bold" colspan="100%">
                                    <h5>No Data Available</h5>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                {{ $patients->appends(Request::all())->links() }}
            </div>
        </div>
    </div>

    {{-- Sell Medicine Modal --}}
    <div class="modal fade" id="exampleModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Medicine to Patient<span
                            id="medicine_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <datalist id="datalist">
                        @foreach ($medicine_dosage as $dose)
                            <option>{{ $dose->remarks }}</option>
                        @endforeach
                    </datalist>

                    <form id="medicineForm" action="{{ route('patient_medicine.store') }}" method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input id="medicine_patient_id" name="patient_id" type="hidden">

                        <div class="form-group">
                            <label>Select Medicine</label>
                            <div class="input-group">
                                <select class="form-control selectpicker medicineItems" name="medicine_id[]"
                                    data-live-search="true" required>
                                    <option value="" selected disabled hidden>Please select</option>
                                    @foreach ($selectPharmacy as $key => $medicine)
                                        @if ($medicine->thisMedicinePharmacy->sum('quantity'))
                                            <?php
                                            $maxSalePrice = 0;
                                            $i = 1;
                                            foreach ($medicine->thisMedicinePharmacy as $medicineSalePrice) {
                                                if ($medicineSalePrice->sale_price > $maxSalePrice && $i < 2) {
                                                    $maxSalePrice = $medicineSalePrice->sale_price;
                                                }
                                            
                                                $i++;
                                            }
                                            ?>
                                            <option value="{{ $medicine->id }}" sale_price="{{ $maxSalePrice }}">
                                                {{ ucfirst($medicine->medicine_name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input class="form-control medicineQTY" name="quantity[]" type="number"
                                    style="height: 38px !important;" placeholder="Quantity">
                                <input class="form-control" name="remark[]" data-ms-editor="true" type="text"
                                    style="height: 38px !important;" placeholder="Remark" list="datalist"
                                    spellcheck="false">
                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>

                        </div>
                        <div id="add_more">
                        </div>
                        <div>
                            <b>Total: <span id="medicine_total_sale_price">0</span></b>
                        </div>
                        <div class="submit-section">
                            <br>
                            <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-primary submit-btn btn-sm" type="submit">Submit</button>

                            @if (in_array('doctor_request_medicine', $user_permissions))
                                <a class="text text-right text-danger pull-right float-right" data-toggle="modal"
                                    data-target="#requestMedicineModal" href=""
                                    style="text-decoration: underline">
                                    Request New Medicine</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--    IPD Modal --}}
    <div class="modal fade" id="IPDModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Patient to IPD <span
                            id="ipd_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm" action="{{ route('patient_ipd.store') }}" method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input id="ipd_patient_id" name="patient_id" type="hidden">
                        <div class="form-group">
                            <label>Select Floor</label>
                            <select class="form-control selectpicker floor_id" name="floor_id" data-live-search="true"
                                required>
                                <option hidden>Please select</option>
                                @foreach ($floors as $key => $floor)
                                    <option value="{{ $floor }}">{{ ucfirst($floor) }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Room</label>
                            <select class="form-control" id="room_id" name="room_id" required>
                                <option value="">Please select</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Bed</label>
                            <select class="form-control" id="bed_id" name="bed_id" required>
                                <option value="">Please select</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control" name="remark"></textarea>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit IPD Modal --}}
    <div class="modal fade" id="editIPDModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Patient IPD Modal <span
                            id="ipd_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editIPDForm" action="" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input name="_method" type="hidden" value="put">
                        <input id="edit_ipd_patient_id" name="patient_id" type="hidden">
                        <div class="form-group">
                            <label>Select Floor</label>
                            <select class="form-control selectpicker floor_id" name="floor_id" data-live-search="true"
                                required>
                                <option hidden>Please select</option>
                                @foreach ($floors as $key => $floor)
                                    <option value="{{ $floor }}">{{ ucfirst($floor) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Room</label>
                            <select class="form-control" id="room_id" name="room_id" required>
                                @foreach ($rooms as $key => $room)
                                    <option value="{{ $room }}">{{ ucfirst($room) }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Bed</label>
                            <select class="form-control" id="bed_id" name="bed_id" required>
                                @foreach ($beds as $key => $bed)
                                    <option value="{{ $key }}">Bed-{{ ucfirst($bed) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control" name="remark"></textarea>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Lab Modal --}}
    <div class="modal fade" id="labModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Patient to Laboratory<span
                            id="lab_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm" action="{{ route('patient_lab.store') }}" method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input id="lab_patient_id" name="patient_id" type="hidden">

                        <div class="form-group">
                            <label>Select Department</label>
                            <div class="input-group ">
                                <select class="form-control col-md-8 offse-2 selectpicker labDepsName"
                                    data-live-search="true">
                                    <option value="general">General</option>
                                    @foreach ($mainLabDepartments as $mainLabDep)
                                        <option value="{{ $mainLabDep->id }}">{{ $mainLabDep->dep_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="newLabDiv">
                            <div class="row gutters">

                                <div class="col-6 offset-3 text-center">
                                    <p class="title" style="font-size: 1.3rem">Ministry of Health</p>
                                    <p class="title" style="font-size: 1.2rem">Bayazid Rokhan Hospital</p>
                                    <p class="title" style="font-size: 1rem">Patient Laboratory</p>
                                </div>

                            </div>
                            <div class="form-group">
                                <label>
                                    <b>Patient Name: <span id="lab_patient_name_show"></span></b>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Select Test</label>
                                <div class="input-group">
                                    <select class="form-control selectpicker col-md-3 labTestsSelect" name="labDeps[]"
                                        data-live-search="true" required>
                                        <option value="" selected disabled hidden>Please select</option>
                                        @foreach ($selectLab as $lab)
                                            <option value="{{ $lab->id }}" normal_range="{{ $lab->normal_range }}"
                                                test_price="{{ $lab->price }}" test_main_dep="{{ $lab->main_dep_id }}"
                                                test_discount="{{ $lab->mainDepartment->discount }}">
                                                {{ ucfirst($lab->dep_name) }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control col-md-3 normal-range" type="text"
                                        style="height: 38px !important;" placeholder="Normal Range" readonly>
                                    <input class="form-control col-md-6" name="remark[]" type="text"
                                        style="height: 38px !important;" placeholder="Remark">

                                    <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer"
                                        onclick="addnewLabTest()"></i>
                                </div>

                            </div>
                            <div id="add_more_lab_test">
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td><b>Total: <span id="dep_lab_total">0</span></b></td>
                                        <td><b>Discount: <span id="dep_lab_discount">0</span></b></td>
                                        <td><b>Total After Discount: <span id="dep_lab_total_discount">0</span></b></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="submit-section">
                            <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-warning btn-sm" type="button"
                                onclick="printDiv('newLabDiv')">Print</button>

                            <button class="btn btn-primary submit-btn btn-sm" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Request Medicine Modal --}}
    <div class="modal fade" id="requestMedicineModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="exampleModalLabel">Please add your requested Medicine Here:
                    </h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data">
                        <input class="form-control" name="requestedMedicine[]" type="text">
                        <input class="form-control mt-1" name="requestedMedicine[]" type="text">
                        <input class="form-control mt-1" name="requestedMedicine[]" type="text">
                        <div class="submit-section">
                            <br>
                            <button class="btn btn-primary btn-danger btn-sm float-right" id="requestedMedicineBtn"
                                type="button">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Patient Medicine Modal --}}
    <div class="modal fade" id="editPatientMedicine" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Medicine Patient <span
                            id="medicine_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editPatientMedicineBody">
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Patient Medicine Modal --}}
    <div class="modal fade" id="editPatientLab" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Lab for Patient <span
                            id="edit_lab_patient_name"></span></h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editPatientLabBody">
                </div>
            </div>
        </div>
    </div>

    {{-- Vital Signs Modal --}}
    <div class="modal fade" id="vitalSignsModal" data-backdrop="static" data-keyboard="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Vital Signs of <span id="vital_patient_name"></span>
                    </h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row gutters">
                        <div class="col-lg-12 col-md-12 col-sm-12 order-last">
                            <form action="{{ route('patient_vital_sign') }}" method="POST">
                                {!! csrf_field() !!}
                                <input id="vital_signs_patient_id" name="patient_id" type="hidden">
                                <table class=" no-border m-0">
                                    <tbody>

                                        <tr>
                                            <td>
                                                <p><b>Blood Pressure:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_blood_input" name="blood_pressure" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Respiration Rate:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_respiratin_input" name="respiration_rate"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Pulse Rate:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_pulse_input" name="pulse_rate" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>PSO2:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_heart_input" name="heart_rate" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Temperature:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_temperature_input" name="temperature" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Weight:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_weight_input" name="weight" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Height:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_height_input" name="height" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Mental State:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_mental_input" name="mental_state" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Medical History:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_history_input" name="medical_history" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>VA:</b></p>
                                            </td>
                                            <td>
                                                <input id="va1_input" name="va_1" type="text">
                                            </td>
                                            <td>
                                                <input id="va2_input" name="va_2" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>IOP:</b></p>
                                            </td>
                                            <td>
                                                <input id="iop1_input" name="iop_1" type="text">
                                            </td>
                                            <td>
                                                <input id="iop2_input" name="iop_2" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Chief Complaint:</b></p>
                                            </td>
                                            <td>
                                                <input id="chiefComplaint_input" name="chief_complaint"
                                                    data-ms-editor="true" type="text" list="chief_complaint_data"
                                                    spellcheck="false">
                                            </td>
                                        </tr>

                                        <datalist id="chief_complaint_data">
                                            <option>Tearing</option>
                                            <option>Discharge</option>
                                            <option>Pain</option>
                                            <option>Foreign body sensation</option>
                                            <option>Photobia</option>
                                            <option>Headaque</option>
                                            <option>VA Down Arrow</option>
                                        </datalist>

                                        <tr>
                                            <td>
                                                <p><b>DX:</b></p>
                                            </td>
                                            <td>
                                                <input id="dx_input" name="dx" data-ms-editor="true"
                                                    type="text" list="dx_data" spellcheck="false">
                                            </td>
                                        </tr>

                                        <datalist id="dx_data">
                                            <option>Viral Conjectivitis</option>
                                            <option>NLDO</option>
                                            <option>CDC</option>
                                            <option>ADC</option>
                                            <option>Galucoma</option>
                                            <option>Alergic conjectivitis</option>
                                            <option>Contracat</option>
                                            <option>RD</option>
                                            <option>DM Retiropathy</option>
                                            <option>HTN Retiropathy</option>
                                            <option>Referactive Error</option>
                                            <option>Hypermetropia</option>
                                            <option>Myopia</option>
                                            <option>Nystigmus</option>
                                        </datalist>

                                        <tr>
                                            <td>
                                                <button class="btn btn-primary" type="submit">Save</button>
                                            </td>
                                            <td>
                                                <a class="btn btn-warning" id="printPatientVitalSignButton"
                                                    href="#" onclick="printPatientVitalSign(this)"
                                                    patient_id="0">Print</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit  patient Modal -->
    <div class="modal fade" id="editPatientModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Patient</h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPatientForm" action="" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input name="_method" type="hidden" value="PUT">

                        <div class="row">

                            <div class="form-group col-4">
                                <label>Patient Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_name" type="text" required readonly>
                            </div>
                            <div class="form-group col-4">
                                <label>Patient F/Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_fname" type="text" required readonly>
                            </div>

                            <div class="form-group col-4">
                                <label>Patient Mobile</label>
                                <input class="form-control" name="patient_phone" type="text" readonly>
                            </div>
                            <div class="form-group col-4">
                                <label>Select Doctor</label>
                                <select class="form-control selectpicker readonly-dropdown" name="doctor_id"
                                    data-live-search="true">
                                    @foreach ($doctors as $key => $doctor)
                                        <option value="{{ $doctor->id }}">{{ ucfirst($doctor->name) }}
                                            <b>({{ $doctor->OPD_fee }})</b>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Gender</label>
                                <select class="form-control" id="" name="gender" disabled>
                                    <option></option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Marital Status</label>
                                <select class="form-control" name="marital_status" disabled>
                                    <option></option>
                                    <option value="0">Single</option>
                                    <option value="1">Married</option>
                                </select>
                            </div>

                            <div class="form-group col-4">
                                <label>Blood Group</label>
                                <select class="form-control" name="blood_group">
                                    <option></option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>AB</option>
                                    <option>O</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Age</label>
                                <input class="form-control" name="age" type="text">
                            </div>

                            {{-- <div class="form-group col-4">
                                <label>Advance Payment?</label>
                                <input class="form-control" name="advance_pay" type="number" value="0">
                            </div> --}}
                            <div class="form-group col-4">
                                <label>Register Date</label>
                                <input class="form-control" name="reg_date" type="date"
                                    value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="form-group col-4">
                                <label>Blood Pressure</label>
                                <input class="form-control" name="blood_pressure" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Respiration Rate</label>
                                <input class="form-control" name="respiration_rate" type="text">
                            </div>

                            <div class="form-group col-4">
                                <label>Pulse Rate</label>
                                <input class="form-control" name="pulse_rate" type="text">
                            </div>

                            <div class="form-group col-4">
                                <label>SPO2</label>
                                <input class="form-control" name="heart_rate" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Temperature</label>
                                <input class="form-control" name="temperature" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Weight</label>
                                <input class="form-control" name="weight" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Height</label>
                                <input class="form-control" name="height" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Mental State</label>
                                <input class="form-control" name="mental_state" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Default Discount</label>
                                <select class="form-control" name="default_discount"
                                    @if (!in_array('Patient Default Discount', $user_permissions)) disabled @endif>
                                    <option value="1">No</option>
                                    <option value="0">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Medical History</label>
                                <textarea class="form-control" name="medical_history"></textarea>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/bs-select/bs-select.min.js') }}"></script>
    <script>
        function addnew() {
            $('#add_more').append(`
             <div class="form-group"> <div class="input-group"><select class="form-control selectpicker medicineItems" data-live-search="true" name="medicine_id[]">
                <option value="" selected disabled hidden>Please select</option>
                @foreach ($selectPharmacy as $key => $medicine)
                
                    <?php $maxSalePrice = 0; ?>
                    
                    @if ($medicine->thisMedicinePharmacy->sum('quantity'))
                        <?php
                        $i = 1;
                        foreach ($medicine->thisMedicinePharmacy as $medicineSalePrice) {
                            if ($medicineSalePrice->sale_price > $maxSalePrice && $i < 2) {
                                $maxSalePrice = $medicineSalePrice->sale_price;
                            }
                            $i++;
                        }
                        ?>
                    <option value="{{ $medicine->id }}" sale_price="{{ $maxSalePrice }}">{{ ucfirst($medicine->medicine_name) }}</option>
                    @endif
                @endforeach
            </select>
            <input type="number" class="form-control medicineQTY" name="quantity[]" placeholder="Quantity" style="height: 38px !important;">
             <input type="text" class="form-control" name="remark[]" placeholder="Remark" style="height: 38px !important;" list="datalist" data-ms-editor="true" spellcheck="false">
            <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
        </div>
        </div>
`);
            $(".selectpicker").selectpicker('refresh')

        }

        function addnewLabTest() {
            $('#add_more_lab_test').append(`
       <div class="form-group">
       <div class="input-group">
     <select class="form-control selectpicker col-md-3 labTestsSelect" data-live-search="true" name="labDeps[]">
         <option value="" selected disabled hidden>Please select</option>

                                    @foreach ($selectLab as $lab)
            <option value="{{ $lab->id }}" normal_range="{{ $lab->normal_range }}" test_price="{{ $lab->price }}" test_main_dep="{{ $lab->main_dep_id }}" test_discount="{{ $lab->mainDepartment->discount }}">{{ ucfirst($lab->dep_name) }}</option>
                                    @endforeach
            </select>
            <input type="text" class="form-control col-md-3 normal-range" placeholder="Normal Range" readonly style="height: 38px !important;">
            <input type="text" class="form-control col-md-6" name="remark[]" placeholder="Remark" style="height: 38px !important;">

            <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnewLabTest()"></i>
        </div>
</div>
`);
            $(".selectpicker").selectpicker().trigger("change");

        }

        function clearInput() {
            $('#medicineForm').find(
                'input[type=text], input[type=password], input[type=number], input[type=email], input[type=checkbox],textarea'
            ).val('');
        };

        function clearInputOfRequestModal() {
            $('#requestMedicineModal').find(
                'input[type=text], input[type=password], input[type=number], input[type=email], input[type=checkbox],textarea'
            ).val('');
        };

        $('#exampleModal').on('hidden.bs.modal', function() {
            clearInput();
            $('#add_more').empty();
            $('#medicine_total_sale_price').html('<b>' + '0' + '</b>');
        });
    </script>
    <script>
        $('.floor_id').change(function() {
            var floor_id = encodeURIComponent($(this).val());
            if (floor_id != '') {
                $('#room_id').html('<option>Loading rooms ...</option>');
                $('#room_id').load('{{ url('getRooms') }}' + "?floor_id=" + floor_id);
            }
        });

        $('.floor_id').change(function() {
            var floor_id = encodeURIComponent($(this).val());
            if (floor_id != '') {
                $('#room_id').html('<option>Loading rooms ...</option>');
                $('#room_id').load('{{ url('getRooms') }}' + "?floor_id=" + floor_id);
            }
        });

        $('#room_id').change(function() {
            var room_id = encodeURIComponent($(this).val());
            if (room_id != '') {
                $('#bed_id').html('<option>Loading beds ...</option>');
                $('#bed_id').load('{{ url('getBeds') }}' + "?room_id=" + room_id);
            }
        });
    </script>
    <script>
        $('#exampleModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            $("#medicine_patient_id").val(patient_id);
            modal.find('.modal-content #medicine_patient_name').html('<b class="text text-danger"> (' +
                patient_name + ')</b>');
        })


        $('#IPDModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            $("#ipd_patient_id").val(patient_id);
            modal.find('.modal-content #ipd_patient_name').html('<b class="text text-danger"> (' + patient_name +
                ')</b>');
        })

        $('#editIPDModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var floor = button.data('floor');
            var room = button.data('room');
            var bed = button.data('bed');
            var remark = button.data('remark');
            var modal = $(this)

            // Set values in edit popup
            $("#edit_ipd_patient_id").val(patient_id);
            modal.find('.modal-content #ipd_patient_name').html('<b class="text text-danger"> (' + patient_name +
                ')</b>');
            modal.find('.modal-body [name=floor_id]').val(floor);
            modal.find('.modal-body [name=room_id]').val(room);
            modal.find('.modal-body [name=bed_id]').val(bed);
            modal.find('.modal-body [name=remark]').val(remark);
            $(".selectpicker").selectpicker('refresh');
        })



        $('#labModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal

            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');

            //Set no_disocunt to the patient value
            no_discount = button.data('no-discount');

            var modal = $(this)

            // Set values in edit popup
            $("#lab_patient_id").val(patient_id);
            $('#add_more_lab_test').empty();

            //Call again for updating the value
            setTotalPriceOfLab()

            modal.find('.modal-content #lab_patient_name').html('<b class="text text-danger"> (' + patient_name +
                ')</b>');
            modal.find('.modal-content #lab_patient_name_show').html('<b class="text text-danger"> (' +
                patient_name + ')</b>');
        });

        $('#editPatientLab').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            modal.find('.modal-content #edit_lab_patient_name').html('<b class="text text-danger"> (' +
                patient_name + ')</b>');
        });
        $('#requestMedicineModal').on('show.bs.modal', function(event) {
            clearInputOfRequestModal();
            $('#exampleModal div.modal-content').css('background-color', 'lightgray')
        });
        $('#requestMedicineModal').on('hide.bs.modal', function(event) {
            $('#exampleModal div.modal-content').css('background-color', 'white');

        })
    </script>

    <script>
        var spanValue = 0;
        var remarkTitle = '';
        var labId;
        var no_discount = 0;
        $('.dep_inputs').click(function() {
            if ($(this).is(':checked')) {
                spanValue = parseInt(spanValue) + parseInt($(this).attr("lab-price"));
                remarkTitle = $(this).attr("lab-name");
                labId = $(this).attr("lab-id");
                $('div#' + labId + '').remove();
                $('#add-lab-remark').append(`
                 <div class="form-group" id=` + labId + `>
                            <label>Any comments for ` + remarkTitle + ` ?</label>
                            <input class="form-control" type="text" name="remark[]">
                        </div>
                `);
            }
            if (!$(this).is(':checked')) {
                spanValue = parseInt(spanValue) - parseInt($(this).attr("lab-price"));
                $('div#' + labId + '').remove();
            }

            $('#dep_lab_total').html(spanValue);

        });

        $('#requestedMedicineBtn').click(function() {
            var requestedMedicines = $("input[name='requestedMedicine[]']")
                .map(function() {
                    return $(this).val();
                }).get();
            $.ajax({
                type: "POST",
                url: '{{ route('save_requested_medicine') }}',
                data: {
                    values: requestedMedicines,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#requestMedicineModal').modal('hide');
                },
                error: function() {
                    alert("An Error Occured, Please try again!");
                }
            });
        })

        function editMedicine(id) {
            if (id != '') {
                $('#editPatientMedicineBody').empty();
                $('#editPatientMedicineBody').load('{{ url('getPatientMedicinesForEdit/') }}' + '/' + id, function() {
                    $(".selectpicker").selectpicker('refresh');
                    setTotalPriceOfMedicine();
                });

            }
        }

        function editLab(id) {
            if (id != '') {
                $('#editPatientLabBody').empty();
                $('#editPatientLabBody').load('{{ url('getPatientLabsForEdit/') }}' + '/' + id, function() {
                    $(".selectpicker").selectpicker('refresh')
                });

            }
        }

        $(document).on('change', '.labTestsSelect', function() {
            var normal_range = $('option:selected', this).attr('normal_range');
            $(this).parent('.input-group').children('input.normal-range').val(normal_range);
            setTotalPriceOfLab();
        });

        function setTotalPriceOfLab() {
            var grandTotalPrice = 0;
            var grandTotalAfterDiscount = 0;
            var grandTotalDiscount = 0;

            var totalValues = $(".labTestsSelect :selected").map((i, el) => $(el).attr("test_price")).toArray();
            var totalDiscounts = $(".labTestsSelect :selected").map((i, el) => $(el).attr("test_discount")).toArray();

            for (var i = 0; i < totalValues.length; i++) {
                grandTotalPrice += totalValues[i] << 0;

                if (no_discount == 1) {
                    grandTotalAfterDiscount += totalValues[i] * 1
                } else {
                    grandTotalAfterDiscount += (totalValues[i] * (100 - totalDiscounts[i]) / 100)
                    grandTotalDiscount += (totalValues[i] * (totalDiscounts[i]) / 100)
                }
            }

            $('#dep_lab_total').html('<b>' + grandTotalPrice.toLocaleString() + '</b>');
            $('#dep_lab_discount').html('<b>' + grandTotalDiscount.toLocaleString() + '</b>');
            $('#dep_lab_total_discount').html('<b>' + grandTotalAfterDiscount.toLocaleString() + '</b>');
        }

        $(document).on('input', '.medicineQTY', function() {
            setTotalPriceOfMedicine();
        });
        $(document).on('change', '.medicineItems', function() {
            setTotalPriceOfMedicine();
        });

        function setTotalPriceOfMedicine() {
            var grandTotalPrice = 0;
            var totalValues = $(".medicineItems :selected").map((i, el) => $(el).attr("sale_price")).toArray();
            var totalQuantities = $(".medicineQTY").map((i, el) => $(el).val()).toArray();
            console.log(totalQuantities, totalValues);
            for (var i = 0; i < totalValues.length; i++) {
                grandTotalPrice += totalValues[i] * totalQuantities[i] << 0;
            }
            $('#medicine_total_sale_price').html('<b>' + grandTotalPrice + '</b>');

        }

        $(document).on('change', '.labDepsName', function() {

            var dep_id = $(this).val();
            if (dep_id !== 'general') {

                $("select.labTestsSelect option").show();
                $("select.labTestsSelect option[test_main_dep!=" + dep_id + "]").hide();
                $(".selectpicker").selectpicker('refresh');
            } else {
                $("select.labTestsSelect option").show();
                $(".selectpicker").selectpicker('refresh');
            }
        });

        $('#vitalSignsModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var blood = button.data('blood-pressure');
            var respiration = button.data('respiration');
            var pulse = button.data('pulse');
            var heart = button.data('heart');
            var temperature = button.data('temperature');
            var weight = button.data('weight');
            var height = button.data('height');
            var mental = button.data('mental-state');
            var history = button.data('medical-history');
            var va1 = button.data('va-1');
            var va2 = button.data('va-2');
            var iop1 = button.data('iop-1');
            var iop2 = button.data('iop-2');
            var chiefComplaint = button.data('chief-complaint');
            var dx = button.data('dx');
            var modal = $(this)

            // Set values in edit popup
            $("#vital_patient_name").html('<b class="text text-danger"> (' + patient_name + ')</b>');;
            modal.find('.modal-content #vital_blood').html(blood);
            modal.find('.modal-content #vital_respiratin').html(respiration);
            modal.find('.modal-content #vital_pulse').html(pulse);
            modal.find('.modal-content #vital_heart').html(heart);
            modal.find('.modal-content #vital_temperature').html(temperature);
            modal.find('.modal-content #vital_weight').html(weight);
            modal.find('.modal-content #vital_height').html(height);
            modal.find('.modal-content #vital_mental').html(mental);
            modal.find('.modal-content #vital_history').html(history);
            modal.find('.modal-content #va1').html(va1);
            modal.find('.modal-content #va2').html(va2);
            modal.find('.modal-content #iop1').html(iop1);
            modal.find('.modal-content #iop2').html(iop2);
            modal.find('.modal-content #chiefComplaint').html(chiefComplaint);
            modal.find('.modal-content #dx').html(dx);

            // Update Input
            modal.find('.modal-content #vital_blood_input').val(blood);
            modal.find('.modal-content #vital_respiratin_input').val(respiration);
            modal.find('.modal-content #vital_pulse_input').val(pulse);
            modal.find('.modal-content #vital_heart_input').val(heart);
            modal.find('.modal-content #vital_temperature_input').val(temperature);
            modal.find('.modal-content #vital_weight_input').val(weight);
            modal.find('.modal-content #vital_height_input').val(height);
            modal.find('.modal-content #vital_mental_input').val(mental);
            modal.find('.modal-content #vital_history_input').val(history);
            modal.find('.modal-content #vital_signs_patient_id').val(patient_id);
            modal.find('.modal-content #va1_input').val(va1);
            modal.find('.modal-content #va2_input').val(va2);
            modal.find('.modal-content #iop1_input').val(iop1);
            modal.find('.modal-content #iop2_input').val(iop2);
            modal.find('.modal-content #chiefComplaint_input').val(chiefComplaint);
            modal.find('.modal-content #dx_input').val(dx);

            modal.find('.modal-content #printPatientVitalSignButton').attr('patient_id', patient_id);
        });

        function printPatientVitalSign() {
            let patient_vital_id = $(event.target).attr('patient_id');
            console.log(patient_vital_id);
            printExternal('{{ url('printVitalSignOfPatient') }}' + "?patient_id=" + patient_vital_id);
        }

        function printExternal(url) {
            var printWindow = window.open(url, 'Print');

            printWindow.addEventListener('load', function() {
                if (Boolean(printWindow.chrome)) {
                    printWindow.print();
                    setTimeout(function() {
                        printWindow.close();
                    }, 500);
                } else {
                    printWindow.print();
                    printWindow.close();
                }
            }, true);
        }
    </script>

    <script>
        $('#editPatientModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            // var generated_id = button.data('generated-id');
            var name = button.data('name');
            var fname = button.data('fname');
            var mobile = button.data('mobile');
            var doctor = button.data('doctor');
            var gender = button.data('gender');
            var blood = button.data('blood');
            var age = button.data('age');
            var marital_status = button.data('marital-status');
            var advance = button.data('advance');
            var blood_pressure = button.data('blood-pressure');
            var respiration = button.data('respiration');
            var pulse = button.data('pulse');
            var heart = button.data('heart');
            var temperature = button.data('temperature');
            var weight = button.data('weight');
            var height = button.data('height');
            var mental_state = button.data('mental-state');
            var medical_history = button.data('medical-history');
            var default_discount = button.data('default-discount');

            var modal = $(this)

            // Set values in edit popup
            var action = '/patient/' + id;

            $("#editPatientForm").attr("action", action);


            modal.find('.modal-body [name="patient_name"]').val(name);
            modal.find('.modal-body [name="patient_fname"]').val(fname);
            modal.find('.modal-body [name="patient_phone"]').val(mobile);
            modal.find('.modal-body [name="doctor_id"]').val(doctor);
            modal.find('.modal-body [name="gender"]').val(gender);
            modal.find('.modal-body [name="marital_status"]').val(marital_status);
            modal.find('.modal-body [name="blood_group"]').val(blood);
            modal.find('.modal-body [name="age"]').val(age);
            modal.find('.modal-body [name="advance_pay"]').val(advance);
            modal.find('.modal-body [name="blood_pressure"]').val(blood_pressure);
            modal.find('.modal-body [name="respiration_rate"]').val(respiration);
            modal.find('.modal-body [name="pulse_rate"]').val(pulse);
            modal.find('.modal-body [name="heart_rate"]').val(heart);
            modal.find('.modal-body [name="temperature"]').val(temperature);
            modal.find('.modal-body [name="weight"]').val(weight);
            modal.find('.modal-body [name="height"]').val(height);
            modal.find('.modal-body [name="mental_state"]').val(mental_state);
            modal.find('.modal-body [name="medical_history"]').val(medical_history);
            modal.find('.modal-body [name="default_discount"]').val(default_discount);

            $('.selectpicker').selectpicker('refresh');
        })
    </script>
@endsection
