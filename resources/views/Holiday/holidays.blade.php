@extends('layouts.master')

@section('page_title')
    Holidays List
@endsection

@section('page-action')
@if(in_array('holiday_add', $user_permissions))
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal">
        Add New Holiday
    </button>
    @endif
@endsection
@section('styles')

    <!-- Data Tables -->
    <link rel="stylesheet" href="{{asset('assets/vendor/datatables/dataTables.bs4.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/datatables/dataTables.bs4-custom.css')}}" />
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
        }
        thead input {
            width: 100%;
        }
    </style>
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
                <table  id="datatable" class="table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Holiday tile</th>
                        <th>Holiday Date</th>
                        <th>Holiday Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($holidays as $holiday)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ucfirst($holiday->holiday_title)}}</td>
                        <td>{{$holiday->holiday_date}}</td>
                        <td>{{$holiday->holiday_description}}</td>
                        <td>
                            @if(in_array('holiday_delete', $user_permissions))
                            <form method="POST" action="{{ route('holiday.destroy', $holiday->id) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                                </div>
                            </form>
                            @endif
                        </td>
                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('holiday.store') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Holiday Title</label>
                            <input type="text" class="form-control" name="holiday_title" required>
                        </div>
                        <div class="form-group">
                            <label>Holiday Date</label>
                            <input type="date" class="form-control" name="holiday_date" value={{ date('Y-m-d')}} required>
                        </div>
                        <div class="form-group">
                            <label>Holiday Description</label>
                            <textarea  class="form-control" name="holiday_description"></textarea>
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary submit-btn btn-sm" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src="{{asset('assets/vendor/datatables/dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>

        $('#datatable').DataTable();

    </script>
@endsection
