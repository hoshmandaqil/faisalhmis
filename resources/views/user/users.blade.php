@extends('layouts.master')

@section('page_title')
    Users List
@endsection

@section('page-action')
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal">
        Add User
    </button>
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
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>Type</th>
                         <th>Attendance ID</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ucfirst($user->name)}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->getUserTypeName($user->type)}}</td>
                             <td>{{$user->attendance_id}}</td>
                        <td>{{date('h:i A', strtotime($user->check_in))}}</td>
                        <td>{{date('h:i A', strtotime($user->check_out))}}</td>
                        <td>@php echo ($user->status == 1) ?  '<span class="badge badge-success">active</span>' : '<span class="badge badge-danger">Deactive</span>'; @endphp </td>

                        <td>

                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item text-dark"  href="{{url('set_permission', $user->id)}}"><i class="icon icon-report"></i> Set Permission</a>
                                            <a class="dropdown-item text-success" href="#"><i class="icon icon-explore"></i> Details</a>
                                            <a class="dropdown-item text-info"  href="#" data-toggle="modal" data-target="#editUserModal" data-id="{{$user->id}}"
                                               data-name ="{{$user->name}}"    data-email ="{{$user->email}}"  data-type ="{{$user->type}}"  data-phone="{{$user->phone}}" data-opd="{{ $user->OPD_fee }}"
                                                data-attendance="{{ $user->attendance_id }}" data-check-in = "{{ $user->check_in }}" data-check-out="{{ $user->check_out }}">
                                                <i class="icon icon-edit"></i> Edit</a>
                                            @if($user->status == 0)
                                                <a class="dropdown-item text-warning"  href="{{url('activate_user', $user->id)}}" onclick="return confirm('Are you sure You want to Activate this User?')" ><i class="icon icon-check"></i> Activate</a>
                                            @else
                                                <a class="dropdown-item text-warning"  href="{{url('deactivate_user', $user->id)}}" onclick="return confirm('Are you sure You want to Deactive this User?')" ><i class="icon icon-x"></i> Deactive</a>
                                            @endif
                                            <a class="dropdown-item text-danger" onclick="return confirm('Are you sure You want to delete this user?')" href="{{url('delete_user', $user->id)}}"><i class="icon icon-delete"></i> Delete</a>
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
                    <form action="{{ route('register_user') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>User Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="1">Admin</option>
                                <option value="2">Pharmacist</option>
                                <option value="3">Doctor</option>
                                <option value="4">Reception</option>
                                <option value="5">Restricted User</option>
                            </select>
                        </div>

                          <div class="form-group">
                            <label>OPD Fee</label>
                            <input type="number" name="OPD_fee" class="form-control" required value="0">
                        </div>
<div class="form-group">
                            <label>Attendance ID</label>
                            <input type="number" name="attendance_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Check In</label>
                            <input type="time" name="check_in" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Check Out</label>
                            <input type="time" name="check_out" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editUserForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>User Name</label>
                            <input type="text" class="form-control" name="name"  required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                                <input id="email" type="email" class="form-control" name="email" required autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="1">Admin</option>
                                <option value="2">Pharmacist</option>
                                <option value="3">Doctor</option>
                                <option value="4">Restricted User</option>
                            </select>
                        </div>
                           <div class="form-group">
                            <label>OPD Fee</label>
                            <input type="number" name="OPD_fee" class="form-control" value="0">
                        </div>
                         <div class="form-group">
                            <label>Attendance ID</label>
                            <input type="number" name="attendance_id" id="attendance_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Check In</label>
                            <input type="time" name="check_in" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Check Out</label>
                            <input type="time" name="check_out" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Change Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary submit-btn btn-sm" type="submit">Save</button>
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

        $(document).ready(function () {
            // Setup - add a text input to each footer cell
            $('#datatable thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#datatable thead');

            var table = $('#datatable').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                "pageLength": 100,
                initComplete: function () {
                    var api = this.api();

                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');

                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('keyup change', function (e) {
                                    e.stopPropagation();

                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();

                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        });


        $('#editUserModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var user_name = button.data('name');
            var email = button.data('email');
            var user_type = button.data('type');
            var OPDFee = button.data('opd');
            var phone = button.data('phone');
               var attendance_id = button.data('attendance');
            var checkIn = button.data('check-in');
            var checkOut = button.data('check-out');
            var modal = $(this)

            // Set values in edit popup
            var action = '/edit_user/'+id;
            $("#editUserForm").attr("action", action);
            modal.find('.modal-body [name="name"]').val(user_name);
            modal.find('.modal-body [name="email"]').val(email);
            modal.find('.modal-body [name="type"]').val(user_type);
            modal.find('.modal-body [name="phone"]').val(phone);
            modal.find('.modal-body [name="OPD_fee"]').val(OPDFee);
           modal.find('.modal-body [name="attendance_id"]').val(attendance_id);
            modal.find('.modal-body [name="check_in"]').val(checkIn);
            modal.find('.modal-body [name="check_out"]').val(checkOut);

        })

    </script>
@endsection
