@extends('layouts.master')

@section('page_title')
    Doctors List
@endsection

@section('page-action')
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
       Add Doctor
    </button>
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
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Mobile</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($doctors as $doctor)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$doctor->doctor_name}}</td>
                            <td>{{$doctor->doctor_fname}}</td>
                            <td>{{$doctor->doctor_phone}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Doctor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('doctor.store')}}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Doctor Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="doctor_name" required>
                        </div>
                        <div class="form-group">
                            <label>Doctor F/Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="doctor_fname" required>
                        </div>
                        <div class="form-group">
                            <label>Doctor Mobile</label>
                            <input class="form-control" type="text" name="doctor_phone">
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
