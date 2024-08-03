@extends('layouts.master')

@section('page_title')
    Laboratory Departments
@endsection

@section('styles')
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
        }
    </style>
@endsection

@section('page-action')
    <button  class="btn btn-sm btn-info" data-toggle="modal" data-target="#depModal">Add New Department</button>
    <a  class="btn btn-sm btn-success" href="{{route('lab_department.index')}}">View Tests List</a>
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
                        <th>Department Name</th>
                        <th>Discount Percentage</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mainDepartments  as $department)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$department->dep_name}}</td>
                            <td>{{$department->discount}}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item text-info" href="#" data-toggle="modal"
                                               data-target="#editLabModal" data-id="{{$department->id}}"
                                               data-main-dep="{{$department->dep_name}}"
                                               data-discount="{{$department->discount}}" >
                                                <i class="icon icon-edit"></i> Edit</a>
                                            <form action="{{route('main_department.destroy', $department->id)}}" method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="_method" value="Delete">
                                                <button class="dropdown-item text-danger" type="submit"
                                                        onclick="return confirm('Are you sure You want to delete this Test?')">
                                                    <i class="icon icon-delete"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editLabModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="editlabform">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="dep_id" id="edit_dep_id">
                        <div class="form-group">
                            <label>Department Name</label>
                            <input type="text" class="form-control" name="dep_name" id="edit_dep_name">
                        </div>
                        <div class="form-group">
                            <label>Discount</label>
                            <input type="number" class="form-control" name="discount" id="edit_discount">
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
    <div class="modal fade" id="depModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('main_department.store')}}" method="post" enctype="multipart/form-data" id="medicineForm">
                        {!! csrf_field() !!}

                        <div class="form-group">
                            <label>Department Name</label>
                            <input type="text" class="form-control" name="dep_name">
                        </div>
                        <div class="form-group">
                            <label>Discount</label>
                            <input type="number" class="form-control" name="discount" value="0">
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
    <script>
        $('#editLabModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var main_dep = button.data('main-dep');
            var discount = button.data('discount');
            var modal = $(this)

            // Set values in edit popup
            var action = '/main_department/' + id;
            $("#editlabform").attr("action", action);
            // modal.find('.modal-body [name="patient_generated_id"]').val(generated_id);
            modal.find('.modal-body [name="dep_id"]').val(id);
            modal.find('.modal-body [name="dep_name"]').val(main_dep);
            modal.find('.modal-body [name="discount"]').val(discount);

        })

    </script>
@endsection
