@extends('layouts.master')

@section('page_title')
    My Patient List (Lab & IPD)
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

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{ url('search_my_patient') }}"
                        method="post">
                        @csrf
                        <input class="search-query"
                            name="search_patient"
                            type="text"
                            value="{{ Request::is('search_my_patient') ? $patientSearchDetail : '' }}"
                            placeholder="Search Patient By Id, Name or Phone..."
                            required>
                        <i class="icon-search1"
                            onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection
@section('page-action')
    @if (\Request::is('search_my_patient'))
        <a class="btn btn-danger btn-sm"
            type="button"
            href="{{ route('my_patients') }}">
            Clear Search
        </a>
    @endif
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
            <div class="table-responsive">
                <table class="table"
                    id="scrollVertical">
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
                                <td>
                                    @if ($patient->ipd != null)
                                        <button class="btn btn-sm btn-gplus"
                                            data-toggle="modal"
                                            data-target="#editIPDModal"
                                            data-patient-id="{{ $patient->id }}"
                                            data-patient-name="{{ $patient->patient_name }}"
                                            data-floor="{{ $patient->ipd->floor->floor_name }}"
                                            data-room="{{ $patient->ipd->floor->room }}"
                                            data-bed="{{ $patient->ipd->floor->bed }}"
                                            data-remark="{{ $patient->ipd->remark }}">View IPD
                                        </button>
                                    @endif

                                    @if (in_array('doctor_sale_ipd', $user_permissions))
                                        <button class="btn btn-sm btn-info"
                                            data-toggle="modal"
                                            data-target="#IPDModal"
                                            data-patient-id="{{ $patient->id }}"
                                            data-patient-name="{{ $patient->patient_name }}">Set IPD
                                        </button>
                                    @endif

                                    @if (!$patient->labs->isEmpty())
                                        <button class="btn btn-sm btn-secondary"
                                            data-patient-id="{{ $patient->id }}"
                                            data-toggle="modal"
                                            data-target="#editPatientLab"
                                            data-patient-name="{{ $patient->patient_name }}"
                                            onclick="editLab({{ $patient->id }})">View Lab
                                        </button>
                                    @else
                                        @if (in_array('doctor_set_lab', $user_permissions))
                                            <button class="btn btn-sm btn-warning"
                                                data-toggle="modal"
                                                data-target="#labModal"
                                                data-patient-id="{{ $patient->id }}"
                                                data-patient-name="{{ $patient->patient_name }}"
                                                data-no-discount="{{ $patient->no_discount }}"
                                                data-discount-type="{{ $patient->discount_type ?? '' }}">Set Lab
                                            </button>
                                        @endif
                                    @endif

                                    <button class="btn btn-sm btn-light mt-1"
                                        data-toggle="modal"
                                        data-target="#vitalSignsModal"
                                        data-patient-id="{{ $patient->id }}"
                                        data-patient-name="{{ $patient->patient_name }}"
                                        data-blood-pressure="{{ $patient->blood_pressure }}"
                                        data-respiration="{{ $patient->respiration_rate }}"
                                        data-pulse="{{ $patient->pulse_rate }}"
                                        data-heart="{{ $patient->heart_rate }}"
                                        data-temperature="{{ $patient->temperature }}"
                                        data-weight="{{ $patient->weight }}"
                                        data-height="{{ $patient->height }}"
                                        data-mental-state="{{ $patient->mental_state }}"
                                        data-medical-history="{{ $patient->medical_history }}"
                                        data-va-1="{{ $patient->va_1 }}"
                                        data-va-2="{{ $patient->va_2 }}"
                                        data-iop-1="{{ $patient->iop_1 }}"
                                        data-iop-2="{{ $patient->iop_2 }}"
                                        data-chief-complaint="{{ $patient->chief_complaint }}"
                                        data-dx="{{ $patient->dx }}"> Vital Signs
                                    </button>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center font-weight-bold"
                                    colspan="100%">
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

    {{-- IPD Modal --}}
    <div class="modal fade"
        id="IPDModal"
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
                        id="exampleModalLabel">Select Patient to IPD <span id="ipd_patient_name"></span></h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm"
                        action="{{ route('patient_ipd.store') }}"
                        method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input id="ipd_patient_id"
                            name="patient_id"
                            type="hidden">
                        <div class="form-group">
                            <label>Select Floor</label>
                            <select class="form-control selectpicker floor_id"
                                name="floor_id"
                                data-live-search="true"
                                required>
                                <option hidden>Please select</option>
                                @foreach ($floors as $key => $floor)
                                    <option value="{{ $floor }}">{{ ucfirst($floor) }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Room</label>
                            <select class="form-control"
                                id="room_id"
                                name="room_id"
                                required>
                                <option value="">Please select</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Bed</label>
                            <select class="form-control"
                                id="bed_id"
                                name="bed_id"
                                required>
                                <option value="">Please select</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control"
                                name="remark"></textarea>
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

    {{-- Edit IPD Modal --}}
    <div class="modal fade"
        id="editIPDModal"
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
                        id="exampleModalLabel">Edit Patient IPD Modal <span id="ipd_patient_name"></span></h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editIPDForm"
                        action=""
                        method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input name="_method"
                            type="hidden"
                            value="put">
                        <input id="edit_ipd_patient_id"
                            name="patient_id"
                            type="hidden">
                        <div class="form-group">
                            <label>Select Floor</label>
                            <select class="form-control selectpicker floor_id"
                                name="floor_id"
                                data-live-search="true"
                                required>
                                <option hidden>Please select</option>
                                @foreach ($floors as $key => $floor)
                                    <option value="{{ $floor }}">{{ ucfirst($floor) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Room</label>
                            <select class="form-control"
                                id="room_id"
                                name="room_id"
                                required>
                                @foreach ($rooms as $key => $room)
                                    <option value="{{ $room }}">{{ ucfirst($room) }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Select Bed</label>
                            <select class="form-control"
                                id="bed_id"
                                name="bed_id"
                                required>
                                @foreach ($beds as $key => $bed)
                                    <option value="{{ $key }}">Bed-{{ ucfirst($bed) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark</label>
                            <textarea class="form-control"
                                name="remark"></textarea>
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

    {{-- Lab Modal --}}
    <div class="modal fade"
        id="labModal"
        data-backdrop="static"
        data-keyboard="false"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-lg"
            role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Add Patient to Laboratory<span id="lab_patient_name"></span></h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="medicineForm"
                        action="{{ route('patient_lab.store') }}"
                        method="post"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input id="lab_patient_id"
                            name="patient_id"
                            type="hidden">

                        <div id="newLabDiv">
                            <div class="row gutters">

                                <div class="col-6 offset-3 text-center">
                                    <p class="title"
                                        style="font-size: 1.3rem">Ministry of Public Health</p>
                                    <p class="title"
                                        style="font-size: 1.2rem">Bayazid Rokhan Hospital</p>
                                    <p class="title"
                                        style="font-size: 1rem">Patient Laboratory</p>
                                </div>

                            </div>
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
                            <div class="form-group">
                                <label>
                                    <b>Patient Name: <span id="lab_patient_name_show"></span></b>
                                </label>
                            </div>
                            <div class="form-group" id="original_lab_row">
                                <label>Select Test</label>
                                <div class="input-group">
                                    <select class="form-control selectpicker col-md-3 labTestsSelect"
                                        name="labDeps[]"
                                        data-live-search="true"
                                        style="min-weight: 100% !important; height: 38px !important;"
                                        required>
                                        <option value=""
                                            selected
                                            disabled
                                            hidden>Please select</option>
                                        @foreach ($selectLab as $lab)
                                            <option value="{{ $lab->id }}"
                                                normal_range="{{ $lab->normal_range }}"
                                                test_price="{{ $lab->price }}"
                                                test_main_dep="{{ $lab->main_dep_id }}"
                                                test_discount="{{ $lab->mainDepartment->discount }}">{{ ucfirst($lab->dep_name) }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control col-md-3 test-price-display"
                                        type="text"
                                        style="height: 38px !important;"
                                        placeholder="Price"
                                        readonly>
                                    <input class="form-control col-md-2 lab-discount-input"
                                        type="number"
                                        name="discount[]"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="Discount %"
                                        style="height: 38px !important;"
                                        value="0">
                                    <input class="form-control col-md-2 test-total-display"
                                        type="text"
                                        style="height: 38px !important;"
                                        placeholder="Total"
                                        readonly>
                                    <input class="form-control col-md-4"
                                        name="remark[]"
                                        type="text"
                                        style="height: 38px !important;"
                                        placeholder="Remark">

                                    <i class="icon-plus-circle ml-2 mt-2"
                                        style="cursor: pointer"
                                        onclick="addnewLabTest()"></i>
                                </div>

                            </div>
                            <div id="add_more_lab_test">
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td><b>Grand Total Price: <span id="dep_lab_total">0</span></b></td>
                                        <td><b>Total Discount: <span id="dep_lab_discount">0</span></b></td>
                                        <td><b>Payable Amount: <span id="dep_lab_total_discount">0</span></b></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="submit-section">
                            <button class="btn btn-secondary btn-sm"
                                data-dismiss="modal"
                                type="button">Close</button>

                            <button class="btn btn-warning btn-sm"
                                type="button"
                                onclick="printDiv('newLabDiv')">Print</button>

                            <button class="btn btn-primary submit-btn btn-sm pull-right"
                                type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Patient Lab Modal --}}
    <div class="modal fade"
        id="editPatientLab"
        data-backdrop="static"
        data-keyboard="false"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-lg"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Edit Lab for Patient <span id="edit_lab_patient_name"></span></h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"
                    id="editPatientLabBody">
                </div>
            </div>
        </div>
    </div>

    {{-- Vital Signs Modal --}}
    <div class="modal fade"
        id="vitalSignsModal"
        data-backdrop="static"
        data-keyboard="false"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-md"
            role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Vital Signs of <span id="vital_patient_name"></span></h5>
                    <button class="close"
                        data-dismiss="modal"
                        type="button"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row gutters">
                        <div class="col-lg-12 col-md-12 col-sm-12 order-last">
                            <form action="{{ route('patient_vital_sign') }}"
                                method="POST">
                                {!! csrf_field() !!}
                                <input id="vital_signs_patient_id"
                                    name="patient_id"
                                    type="hidden">
                                <table class=" no-border m-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><b>Blood Pressure:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_blood_input"
                                                    name="blood_pressure"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Respiration Rate:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_respiratin_input"
                                                    name="respiration_rate"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Pulse Rate:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_pulse_input"
                                                    name="pulse_rate"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>PSO2:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_heart_input"
                                                    name="heart_rate"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Temperature:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_temperature_input"
                                                    name="temperature"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Weight:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_weight_input"
                                                    name="weight"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Height:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_height_input"
                                                    name="height"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Mental State:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_mental_input"
                                                    name="mental_state"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Medical History:</b></p>
                                            </td>
                                            <td>
                                                <input id="vital_history_input"
                                                    name="medical_history"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>VA:</b></p>
                                            </td>
                                            <td>
                                                <input id="va1_input"
                                                    name="va_1"
                                                    type="text">
                                            </td>
                                            <td>
                                                <input id="va2_input"
                                                    name="va_2"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>IOP:</b></p>
                                            </td>
                                            <td>
                                                <input id="iop1_input"
                                                    name="iop_1"
                                                    type="text">
                                            </td>
                                            <td>
                                                <input id="iop2_input"
                                                    name="iop_2"
                                                    type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Chief Complaint:</b></p>
                                            </td>
                                            <td>
                                                <input id="chiefComplaint_input"
                                                    name="chief_complaint"
                                                    data-ms-editor="true"
                                                    type="text"
                                                    list="chief_complaint_data"
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
                                                <input id="dx_input"
                                                    name="dx"
                                                    data-ms-editor="true"
                                                    type="text"
                                                    list="dx_data"
                                                    spellcheck="false">
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
                                                <button class="btn btn-primary"
                                                    type="submit">Save</button>
                                            </td>
                                            <td>
                                                <a class="btn btn-warning"
                                                    id="printPatientVitalSignButton"
                                                    href="#"
                                                    onclick="printPatientVitalSign(this)"
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
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/bs-select/bs-select.min.js') }}"></script>
    <script>
        function addnewLabTest() {
            var rowId = 'lab_row_' + Date.now();
            $('#add_more_lab_test').append(`
                <div class="form-group" id="${rowId}">
                    <div class="input-group">
                        <select class="form-control selectpicker col-md-3 labTestsSelect" data-live-search="true" name="labDeps[]">
                            <option value="" selected disabled hidden>Please select</option>
                            @foreach ($selectLab as $lab)
                                <option value="{{ $lab->id }}" normal_range="{{ $lab->normal_range }}" test_price="{{ $lab->price }}" test_main_dep="{{ $lab->main_dep_id }}" test_discount="{{ $lab->mainDepartment->discount }}">{{ ucfirst($lab->dep_name) }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control col-md-3 test-price-display" placeholder="Price" readonly style="height: 38px !important;">
                        <input type="number" class="form-control col-md-2 lab-discount-input" name="discount[]" placeholder="Discount %" min="0" max="100" step="0.01" value="0" style="height: 38px !important;">
                        <input type="text" class="form-control col-md-2 test-total-display" placeholder="Total" readonly style="height: 38px !important;">
                        <input type="text" class="form-control col-md-4" name="remark[]" placeholder="Remark" style="height: 38px !important;">

                        <i class="icon-minus-circle ml-2 mt-2" style="cursor: pointer; color: red;" onclick="removeLabRow('${rowId}')"></i>
                    </div>
                </div>
            `);

            $(".selectpicker").selectpicker().trigger("change");
        }

        function removeLabRow(rowId) {
            $('#' + rowId).remove();
            setTotalPriceOfLab();
        }
    </script>
    <script>
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
        $('#IPDModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            $("#ipd_patient_id").val(patient_id);
            modal.find('.modal-content #ipd_patient_name').html('<b class="text text-danger"> (' + patient_name + ')</b>');
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
            modal.find('.modal-content #ipd_patient_name').html('<b class="text text-danger"> (' + patient_name + ')</b>');
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

            // Set discount_type to the patient value
            discount_type = button.data('discount-type') || '';

            var modal = $(this)

            // Reset the form
            $("#lab_patient_id").val(patient_id);
            $('#add_more_lab_test').empty();

            // Reset the original row
            $('#original_lab_row select').val('').selectpicker('refresh');
            $('#original_lab_row input.test-price-display').val('');
            $('#original_lab_row input.lab-discount-input').val('0');
            $('#original_lab_row input.test-total-display').val('');
            $('#original_lab_row input[name="remark[]"]').val('');

            // Reset totals
            $('#dep_lab_total').html('<b>0</b>');
            $('#dep_lab_discount').html('<b>0</b>');
            $('#dep_lab_total_discount').html('<b>0</b>');

            // Debug: Check for any labTestsSelect elements outside the modal
            console.log('=== Modal Opening Debug ===');
            console.log('Total .labTestsSelect elements in document:', $('.labTestsSelect').length);
            console.log('.labTestsSelect elements in labModal:', $('#labModal .labTestsSelect').length);
            console.log('.labTestsSelect elements outside labModal:', $('.labTestsSelect').not('#labModal .labTestsSelect').length);

            // Detailed inspection of each element
            $('.labTestsSelect').each(function(index) {
                console.log('Element ' + index + ':', {
                    id: $(this).attr('id'),
                    classes: $(this).attr('class'),
                    inModal: $(this).closest('#labModal').length > 0,
                    parent: $(this).parent().attr('class')
                });
            });

            // Set patient name in modal title
            $('#lab_patient_name').html('<b class="text text-danger"> (' + patient_name + ')</b>');
        });

        $('#editPatientLab').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            modal.find('.modal-content #edit_lab_patient_name').html('<b class="text text-danger"> (' + patient_name + ')</b>');
        });
    </script>

    <script>
        // Old dep_inputs handler removed - using new labTestsSelect system instead
        // var spanValue = 0;
        // var remarkTitle = '';
        // var labId;
        var no_discount = 0;
        var discount_type = '';

        function editLab(id) {
            if (id != '') {
                $('#editPatientLabBody').empty();
                $('#editPatientLabBody').load('{{ url('getPatientLabsForEdit/') }}' + '/' + id, function() {
                    $(".selectpicker").selectpicker('refresh')
                });

            }
        }

        $(document).on('change', '.labTestsSelect', function() {
            console.log('labTestsSelect change event triggered');
            var test_price = $('option:selected', this).attr('test_price');
            console.log('Selected test price:', test_price);
            $(this).parent('.input-group').children('input.test-price-display').val(test_price);

            // Automatically set discount percentage based on patient discount type
            var discountInput = $(this).closest('.input-group').find('.lab-discount-input');
            var discountPercentage = 0;

            if (discount_type === 'student') {
                discountPercentage = 10; // 10% for students
            } else if (discount_type === 'staff') {
                discountPercentage = 20; // 20% for staff
            } else if (no_discount == 0) {
                // Apply main lab department discount
                var test_discount = $('option:selected', this).attr('test_discount');
                discountPercentage = parseFloat(test_discount) || 0;
            }

            if (discountPercentage > 0) {
                discountInput.val(discountPercentage);
            }

            calculateTestTotal($(this));
            setTotalPriceOfLab();
        });

        $(document).on('input', '.lab-discount-input', function() {
            console.log('lab-discount-input input event triggered');
            console.log('Discount value:', $(this).val());
            calculateTestTotal($(this).closest('.input-group').find('.labTestsSelect'));
            setTotalPriceOfLab();
        });

        function calculateTestTotal(selectElement) {
            var selectedOption = selectElement.find('option:selected');
            var testPrice = parseFloat(selectedOption.attr('test_price')) || 0;
            var discountInput = selectElement.closest('.input-group').find('.lab-discount-input');
            var discountPercentage = parseFloat(discountInput.val()) || 0;
            var totalDisplay = selectElement.closest('.input-group').find('.test-total-display');

            console.log('calculateTestTotal - Price:', testPrice, 'Discount %:', discountPercentage);

            // Calculate discount amount from percentage
            var discountAmount = (testPrice * discountPercentage) / 100;
            var finalPrice = Math.max(0, testPrice - discountAmount);
            totalDisplay.val(finalPrice.toLocaleString());

            console.log('calculateTestTotal - Discount Amount:', discountAmount, 'Final Price:', finalPrice);
        }

        function setTotalPriceOfLab() {
            var grandTotalPrice = 0;
            var grandTotalDiscount = 0;
            var testCount = 0;

            console.log('=== Starting setTotalPriceOfLab calculation ===');

            // Use a more specific selector to avoid Bootstrap Select wrappers
            var labTestSelects = $("#labModal select.labTestsSelect");
            var totalElements = labTestSelects.length;
            console.log('Total .labTestsSelect elements found:', totalElements);

            // Loop through all lab test rows - only within the lab modal
            labTestSelects.each(function(index) {
                var selectedOption = $(this).find('option:selected');
                var testValue = selectedOption.val();
                var elementId = $(this).attr('id') || 'no-id';
                var elementClass = $(this).attr('class');

                console.log('Row ' + index + ': Element ID = "' + elementId + '", Classes = "' + elementClass + '", Test value = "' + testValue + '"');

                // Only calculate if a test is selected and has a valid value
                if (testValue && testValue !== '' && testValue !== 'Please select' && testValue !== undefined) {
                    var testPrice = parseFloat(selectedOption.attr('test_price')) || 0;
                    var discountInput = $(this).closest('.input-group').find('.lab-discount-input');
                    var discountPercentage = parseFloat(discountInput.val()) || 0;

                    console.log('Row ' + index + ' - Price:', testPrice, 'Discount %:', discountPercentage);

                    // Calculate discount amount from percentage
                    var discountAmount = (testPrice * discountPercentage) / 100;

                    // Ensure we have valid numbers
                    if (!isNaN(testPrice) && !isNaN(discountAmount)) {
                        grandTotalPrice += testPrice;
                        grandTotalDiscount += discountAmount;
                        testCount++;

                        // Debug logging
                        console.log('Test ' + testCount + ':', selectedOption.text(), 'Price:', testPrice, 'Discount %:', discountPercentage, 'Discount Amount:', discountAmount, 'Element ID:', elementId);
                    }
                }
            });

            var payableAmount = Math.max(0, grandTotalPrice - grandTotalDiscount);

            console.log('Final totals - Price:', grandTotalPrice, 'Discount:', grandTotalDiscount, 'Payable:', payableAmount, 'Test count:', testCount);

            $('#dep_lab_total').html('<b>' + grandTotalPrice.toLocaleString() + '</b>');
            $('#dep_lab_discount').html('<b>' + grandTotalDiscount.toLocaleString() + '</b>');
            $('#dep_lab_total_discount').html('<b>' + payableAmount.toLocaleString() + '</b>');
        }

        $(document).on('input', '.medicineQTY', function() {
            setTotalPriceOfMedicine();
        });
        $(document).on('change', '.medicineItems', function() {
            setTotalPriceOfMedicine();
        });

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
@endsection
