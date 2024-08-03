@extends('layouts.master')

@section('page_title')
   Medication Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
        <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

@endsection
@section('styles')
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendor/bs-select/bs-select.css')}}" />
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
        }
    </style>
@endsection

@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'Medication Report', 'from' => 'Beginning', 'to' => 'Today'])
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
            <div class="table-responsive">
                <table id="scrollVertical" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Patient Name</th>
                        <th>Patient Id</th>
                        <th>Doctor Medicine</th>
                        <th>Sold Medicine</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($editedMedicines  as $medicine)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$medicine->patient_name}}</td>
                            <td>{{$medicine->patient_generated_id}}</td>
                            <td>
                                <table class="">
                                    @foreach($medicine->medicines as $doctorMedicine)
                                    <tr>
                                        <td>  {{$doctorMedicine->medicine->medicine_name}} ({{$doctorMedicine->quantity}})</td>
                                    </tr>
                                    @endforeach

                                </table>
                            </td>


                                <td>
                                    <table>
                                        @foreach($medicine->pharmacyMedicines as $soldMedicine)
                                            <tr>
                                                <td>  {{$soldMedicine->medicine->medicine_name}} ({{$soldMedicine->quantity}})</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>


    <!-- Modal -->

@endsection
@section('scripts')
    <!-- Bootstrap Select JS -->
    <script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>
@endsection
