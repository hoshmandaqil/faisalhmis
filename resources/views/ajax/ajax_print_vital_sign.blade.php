@extends('layouts.master')

@section('page_title')
    Patients Invoice
@endsection

@section('page-action')
@endsection
@section('styles')
@endsection
@section('content')
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row gutters">

                <div class="col-6 offset-3 text-center">
                    <p class="title"
                        style="font-size: 1.3rem">Ministry of Public Health</p>
                    <p class="title"
                        style="font-size: 1.2rem">Faisal Curative Hospital</p>
                    <p class="title"
                        style="font-size: 1rem">Patient Vital Sign</p>
                </div>

            </div>
            <div class="invoice-container">
                <hr>
                <div class="invoice-body col-12">

                    <!-- Row start -->
                    <div class="row gutters">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row gutters">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="row ml-2">
                                        <h6>Patient Name: </h6>
                                        <h5 class="ml-1"> {{ ucfirst($patient->patient_name) }}</h5>
                                    </div>
                                    <div class="row ml-2">
                                        <h6>Patient ID: </h6>
                                        <h5 class="ml-1"> {{ $patient->patient_generated_id }}</h5>
                                    </div>
                                    <div class="row ml-2">
                                        <h6>Doctor: </h6>
                                        <h5 class="ml-1"> {{ $patient->doctor->name }}</h5>
                                    </div>
                                    <div class="row ml-2">
                                        <h6>Registered By: </h6>
                                        <h5 class="ml-1"> {{ $patient->createdBy->name }}</h5>
                                    </div>

                                    <div class="row ml-2">
                                        <h6>Updated By: </h6>
                                        <h5 class="ml-1">
                                            @if ($patient->updated_by != null)
                                                {{ $patient->updatedBy->name }}
                                            @endif
                                        </h5>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- Row end -->

                    <!-- Row start -->
                    <div class="invoice-payment">
                        <div class="row gutters">
                            <div class="col-lg-12 col-md-12 col-sm-12 order-last">
                                <table class="table no-border m-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><b>Blood Pressure:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->blood_pressure }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Respiration Rate:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->respiration_rate }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Pulse Rate:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->pulse_rate }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Heart Rate:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->heart_rate }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Temperature:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->temperature }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Weight:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->weight }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Height:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->height }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Mental State:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->mental_state }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>Medical History:</b></p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->medical_history }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>VA < </b>
                                                </p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->va_1 }} <strong>|</strong> {{ $patient->va_2 }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>IOP < </b>
                                                </p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->iop_1 }} <strong>|</strong> {{ $patient->iop_2 }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>CHIEF COMPLAINT:</b>
                                                </p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->chief_complaint }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><b>DX:</b>
                                                </p>
                                            </td>
                                            <td>
                                                <p>{{ $patient->dx }}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Row end -->

                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
