@extends('layouts.master')

@section('page_title')
    Patients List
@endsection

@section('page-action')
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal" type="button">
        Add patient
    </button>
    @if (\Request::is('search_patient_list'))
        <a class="btn btn-danger btn-sm" type="button" href="{{ route('patient.index') }}">
            Clear Search
        </a>
    @endif
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
    </style>
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                <div class="search-box">
                    <form action="{{ url('search_patient_list') }}" method="post">
                        @csrf
                        <input class="search-query" name="search_patient" type="text"
                            value="{{ Request::is('search_patient_list') ? $patientSearchDetail : '' }}"
                            placeholder="Search Patient By Id, Name or Phone...">
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
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table class="table" id="scrollVertical">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Mobile</th>
                            <th>Age</th>
                            <th>Blood Group</th>
                            <th>Doctor</th>
                            <th>Register By</th>
                            <th>Register Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $patient)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $patient->patient_generated_id }}</td>
                                <td>{{ $patient->patient_name }}</td>
                                <td>{{ $patient->patient_fname }}</td>
                                <td>{{ $patient->patient_phone }}</td>
                                <td>{{ $patient->age }}</td>
                                <td>{{ $patient->blood_group }}</td>
                                <td>{{ $patient->doctor_id != null ? $patient->doctor->name : 'Not Added' }}</td>
                                <td>{{ $patient->createdBy->name }}</td>
                                <td>{{ $patient->created_at }}</td>
                                <td>

                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm dropdown-toggle" id="btnGroupDrop1"
                                                data-toggle="dropdown" type="button" aria-haspopup="true"
                                                aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item text-info" data-toggle="modal"
                                                    data-target="#editPatientModal" data-id="{{ $patient->id }}"
                                                    data-generate-id="{{ $patient->patient_generated_id }}"
                                                    data-name="{{ $patient->patient_name }}"
                                                    data-fname="{{ $patient->patient_fname }}"
                                                    data-mobile="{{ $patient->patient_phone }}"
                                                    data-doctor="{{ $patient->doctor_id }}"
                                                    data-gender="{{ $patient->gender }}"
                                                    data-blood="{{ $patient->blood_group }}"
                                                    data-age="{{ $patient->age }}"
                                                    data-marital-status="{{ $patient->marital_status }}"
                                                    data-advance="{{ $patient->advance_pay }}"
                                                    data-blood-pressure="{{ $patient->blood_pressure }}"
                                                    data-respiration="{{ $patient->respiration_rate }}"
                                                    data-pulse="{{ $patient->pulse_rate }}"
                                                    data-heart="{{ $patient->heart_rate }}"
                                                    data-temperature="{{ $patient->temperature }}"
                                                    data-weight="{{ $patient->weight }}"
                                                    data-height="{{ $patient->height }}"
                                                    data-mental-state="{{ $patient->mental_state }}"
                                                    data-medical-history="{{ $patient->medical_history }}"
                                                    data-default-discount="{{ $patient->no_discount }}" href="#">
                                                    <i class="icon icon-edit"></i> Edit</a>
                                                <form action="{{ route('patient.destroy', $patient->id) }}" method="post">
                                                    {!! csrf_field() !!}
                                                    <input name="_method" type="hidden" value="Delete">
                                                    <button class="dropdown-item text-danger" type="submit"
                                                        onclick="return confirm('Are you sure You want to delete this Patient?')">
                                                        <i class="icon icon-delete"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="btn btn-sm btn-dark"
                                        href="{{ url('patient_invoice', ['patient' => $patient->id]) }}">Invoice</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Patient</h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('patient.store') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Patient ID <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_generated_id" type="text"
                                    value="{{ 'BRH-' . ($previousPatientId + 1) }}" readonly required>
                            </div>
                            <div class="form-group col-6">
                                <label>Patient Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_name" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Patient F/Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_fname" type="text" required>
                            </div>
                            <div class="form-group col-6">
                                <label>Patient Mobile</label>
                                <input class="form-control" name="patient_phone" type="text">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-6">
                                <label>Select Doctor</label>
                                <select class="form-control selectpicker" name="doctor_id" data-live-search="true">
                                    @foreach ($doctors as $key => $doctor)
                                        <option value="{{ $doctor->id }}">{{ ucfirst($doctor->name) }}
                                            <b>({{ $doctor->OPD_fee }})</b>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label>Gender</label>
                                <select class="form-control" id="" name="gender">
                                    <option></option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-6">
                                <label>Marital Status</label>
                                <select class="form-control" name="marital_status">
                                    <option></option>
                                    <option value="0">Single</option>
                                    <option value="1">Married</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label>Blood Group</label>
                                <select class="form-control" name="blood_group">
                                    <option></option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>AB</option>
                                    <option>O</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Age</label>
                                <input class="form-control" name="age" type="text">
                            </div>
                            <div class="form-group col-6">
                                <label>Advance Payment?</label>
                                <input class="form-control" name="advance_pay" type="number" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Register Date</label>
                                <input class="form-control" name="reg_date" type="date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-6">
                                <label>Default Discount</label>
                                <select class="form-control" name="default_discount">
                                    <option value="1" selected>No</option>
                                    @if (in_array('Patient Default Discount', $user_permissions))
                                        <option value="0">Yes</option>
                                    @endif
                                </select>
                            </div>
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

    <!-- Edit Modal -->
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
                                <input class="form-control" name="patient_name" type="text" required>
                            </div>
                            <div class="form-group col-4">
                                <label>Patient F/Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="patient_fname" type="text" required>
                            </div>

                            <div class="form-group col-4">
                                <label>Patient Mobile</label>
                                <input class="form-control" name="patient_phone" type="text">
                            </div>
                            <div class="form-group col-4">
                                <label>Select Doctor</label>
                                <select class="form-control selectpicker" name="doctor_id" data-live-search="true">
                                    @foreach ($doctors as $key => $doctor)
                                        <option value="{{ $doctor->id }}">{{ ucfirst($doctor->name) }}
                                            <b>({{ $doctor->OPD_fee }})</b>
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-4">
                                <label>Gender</label>
                                <select class="form-control" id="" name="gender">
                                    <option></option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Marital Status</label>
                                <select class="form-control" name="marital_status">
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

                            <div class="form-group col-4">
                                <label>Advance Payment?</label>
                                <input class="form-control" name="advance_pay" type="number" value="0">
                            </div>
                            <div class="form-group col-4">
                                <label>Register Date</label>
                                <input class="form-control" name="reg_date" type="date" value="{{ date('Y-m-d') }}">
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
                            <div class="form-group col-3">
                                <label>Temperature</label>
                                <input class="form-control" name="temperature" type="text">
                            </div>
                            <div class="form-group col-3">
                                <label>Weight</label>
                                <input class="form-control" name="weight" type="text">
                            </div>
                            <div class="form-group col-3">
                                <label>Height</label>
                                <input class="form-control" name="height" type="text">
                            </div>
                            <div class="form-group col-3">
                                <label>Mental State</label>
                                <input class="form-control" name="mental_state" type="text">
                            </div>
                            <div class="form-group col-3">
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
    <!-- Bootstrap Select JS -->
    <script src="{{ asset('assets/vendor/bs-select/bs-select.min.js') }}"></script>

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
