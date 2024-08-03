@extends('layouts.master')

@section('page_title')
    Expired Medicine Report
@endsection

@section('page-action')
    <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

@endsection
@section('styles')
@endsection

@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'Expired Medicines Report', 'from' => 'Beginning', 'to' => 'today'])
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
                        <th>Expired On</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expiredMedicines  as $pharmacy)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$pharmacy->medicineName->medicine_name}}</td>
                            <td> {{ $pharmacy->exp_date }}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
@section('scripts')
@endsection
