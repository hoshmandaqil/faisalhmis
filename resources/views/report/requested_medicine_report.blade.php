@extends('layouts.master')

@section('page_title')
    Requested Medicine Report
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
@include('layouts.page_header_print', ['reportName' => 'Requested Medicine Report', 'from' => 'Beginning', 'to' => 'Today'])
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
                    <table id="scrollVertical" class="table">
                        <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Medicine Name</th>
                            <th>Requested By</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($requestedMedicine  as $pharmacy)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$pharmacy->medicine_name}}</td>
                                <td>{{$pharmacy->user->name}}</td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sell Percentage Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('requested_medicine_report')}}" method="GET" enctype="multipart/form-data" id="medicineForm">
                        <div class="form-group">
                            <label class="label">Requested By:</label>
                            <select class="form-control selectpicker" data-live-search="true" name="doctor_id">
                                @foreach($requestedUsers as $user)
                                    <option value="{{$user['user']['id']}}">{{$user['user']['name']}}</option>
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
    </div
    >

@endsection
@section('scripts')
    <!-- Bootstrap Select JS -->
    <script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>
@endsection
