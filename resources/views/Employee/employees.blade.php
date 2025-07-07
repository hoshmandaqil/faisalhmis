@extends('layouts.master')

@section('page_title')
    Employees
@endsection

@section('page-action')
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal">
        Add New Employee
    </button>
@endsection
@section('styles')
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bs4.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bs4-custom.css') }}" />
    <style>
        .modal-body input,
        .modal-body select {
            height: 35px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        thead input {
            width: 100%;
        }
    </style>
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
                <table id="datatable" class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>F/Name</th>
                            <th>Position</th>
                            <th>Gender</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->employee_id }}</td>
                                <td>{{ ucfirst($employee->first_name) . ' ' . ucfirst($employee->last_name) }}</td>
                                <td>{{ ucfirst($employee->father_name) }}</td>
                                <td>{{ ucfirst($employee->position) }}</td>
                                <td>{{ $employee->gender == 0 ? 'Male' : 'Female' }}</td>
                                <td>{{ $employee->phone_number }}</td>
                                <td>@php echo ($employee->status == 1) ?  '<span class="badge badge-success">active</span>' : '<span class="badge badge-danger">Deactive</span>'; @endphp </td>

                                <td>

                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button"
                                                class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item text-dark" href="#" data-toggle="modal"
                                                    data-target="#viewProfile"
                                                    onclick="viewProfile({{ $employee->id }})"><i
                                                        class="icon icon-user"></i> View Profile</a>

                                                <a class="dropdown-item text-success" href="#" data-toggle="modal"
                                                    data-target="#setPercentage"
                                                    onclick="setPercentage({{ $employee->id }})"><i
                                                        class="icon icon-percent"></i> Set
                                                    Percentage</a>
                                                <a class="dropdown-item text-info" href="#" data-toggle="modal"
                                                    data-target="#editEmpModal" data-id="{{ $employee->id }}"
                                                    data-emp-id="{{ $employee->employee_id }}"
                                                    data-name="{{ $employee->first_name }}"
                                                    data-last="{{ $employee->last_name }}"
                                                    data-father="{{ $employee->father_name }}"
                                                    data-dob="{{ $employee->dob }}"
                                                    data-position="{{ $employee->position }}"
                                                    data-email="{{ $employee->email }}"
                                                    data-phone="{{ $employee->phone_number }}"
                                                    data-nationality={{ $employee->nationality }}
                                                    data-gender="{{ $employee->gender }}"
                                                    data-marital="{{ $employee->marital_status }}"
                                                    data-native="{{ $employee->native_language }}"
                                                    data-tazkira="{{ $employee->tazkira_number }}"
                                                    data-current-add="{{ $employee->current_address }}"
                                                    data-permanent-add="{{ $employee->permanent_address }}"
                                                    data-contract-start="{{ $employee->contract_start }}"
                                                    data-contract-end="{{ $employee->contract_end }}"
                                                    data-status="{{ $employee->status }}"
                                                    data-comment="{{ $employee->comment }}"
                                                    data-empsalary="{{ $employee->employeeCurrentSalary->salary_amount }}"
                                                    data-attendance-id="{{ $employee->attendance_id }}"
                                                    data-dep-name={{ $employee->department }}
                                                    data-check-in={{ $employee->check_in }}
                                                    data-check-out={{ $employee->check_out }}
                                                    data-user="{{ $employee->user_id }}">
                                                    <i class="icon icon-edit"></i> Edit</a>
                                                @if ($employee->status == 0)
                                                    <a class="dropdown-item text-warning"
                                                        href="{{ url('activate_employee', $employee->id) }}"
                                                        onclick="return confirm('Are you sure You want to Activate this User?')"><i
                                                            class="icon icon-check"></i> Activate</a>
                                                @else
                                                    <a class="dropdown-item text-warning"
                                                        href="{{ url('deactivate_employee', $employee->id) }}"
                                                        onclick="return confirm('Are you sure You want to Deactive this Employee?')"><i
                                                            class="icon icon-x"></i> Deactive</a>
                                                @endif


                                                <form method="POST"
                                                    action="{{ route('employee.destroy', $employee->id) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure You want to delete this user?')"><i
                                                            class="icon icon-delete"></i> Delete</button>
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('employee.store') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Employee ID <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="employee_id" required>
                            </div>
                            <div class="form-group col-4">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" required>
                            </div>
                            <div class="form-group col-4">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Father Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="father_name" required>
                            </div>
                            <div class="form-group col-4">
                                <label>DOB <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="dob" value={{ date('Y-m-d') }}
                                    required>
                            </div>
                            <div class="form-group col-4">
                                <label>Position <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="position" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Department <span class="text-danger">*</span></label>
                                <select class="form-control" name="department_name" required>
                                    <option value="Specialist">Specialist</option>
                                    <option value="Doctor">Doctor</option>
                                    <option value="Nurse">Nurse</option>
                                    <option value="Receptionist">Receptionist</option>
                                    <option value="Guard">Guard</option>
                                    <option value="Cleaner">Cleaner</option>
                                    <option value="Pharmacist">Pharmacist</option>

                                    <option value="Admin Staff">Admin Staff</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Email</label>
                                <input class="form-control" type="email" name="email">
                            </div>
                            <div class="form-group col-4">
                                <label>Phone Number</label>
                                <input class="form-control" type="text" name="phone_number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Martial Status</label>
                                <select class="form-control" name="marital_status">
                                    <option value="0">Single</option>
                                    <option value="1">Married</option>
                                    <option value="2">Widowed</option>
                                    <option value="3">Divorced</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Nationality</label>
                                <input class="form-control" type="text" name="nationality" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Native Language</label>
                                <input class="form-control" type="text" name="native_language">
                            </div>
                            <div class="form-group col-4">
                                <label>Tazkira Number</label>
                                <input class="form-control" type="text" name="tazkira_number">
                            </div>
                            <div class="form-group col-4">
                                <label>Current Address</label>
                                <input class="form-control" type="text" name="current_address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Permanent Address</label>
                                <input class="form-control" type="text" name="permanent_address">
                            </div>
                            <div class="form-group col-4">
                                <label>Contract Start Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="contract_start"
                                    value={{ date('Y-m-d') }} required>
                            </div>
                            <div class="form-group col-4">
                                <label>Contract End Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="contract_end" value={{ date('Y-m-d') }}
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label>Contract Files</label>
                                <input class="form-control" type="file" name="contract_files[]" multiple>
                            </div>
                            <div class="form-group col-3">
                                <label>Salary Amount <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="salary" required>
                            </div>
                            <div class="form-group col-3">
                                <label>Status<span class="text-danger">*</span></label>
                                <select class="form-control" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>

                                </select>
                            </div>
                            <div class="form-group col-3">
                                <label>User<span class="text-danger">*</span></label>
                                <select class="form-control" name="user">
                                    <option value="">No User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label>Attendance ID</label>
                                <input class="form-control" type="number" name="attendance_id">
                            </div>
                            <div class="form-group col-3">
                                <label>Check In</label>
                                <input class="form-control" type="time" name="check_in">
                            </div>
                            <div class="form-group col-3">
                                <label>Check Out</label>
                                <input class="form-control" type="time" name="check_out">
                            </div>
                            <div class="form-group col-3">
                                <label>Staff Photo</label>
                                <input class="form-control" type="file" name="image">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Additional Comments</label>
                                <textarea class="form-control" name="comment"></textarea>
                            </div>
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

    <!--Edit Modal -->
    <div class="modal fade" id="editEmpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="edit_emp_form" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="emp_id" id="edit_emp_id">
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Employee ID <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="employee_id" required>
                            </div>
                            <div class="form-group col-4">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" required>
                            </div>
                            <div class="form-group col-4">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Father Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="father_name" required>
                            </div>
                            <div class="form-group col-4">
                                <label>DOB <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="dob" value={{ date('Y-m-d') }}
                                    required>
                            </div>
                            <div class="form-group col-4">
                                <label>Position <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="position" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Department <span class="text-danger">*</span></label>
                                <select class="form-control" name="department_name" required>
                                    <option value="Specialist">Specialist</option>
                                    <option value="Doctor">Doctor</option>
                                    <option value="Nurse">Nurse</option>
                                    <option value="Receptionist">Receptionist</option>
                                    <option value="Guard">Guard</option>
                                    <option value="Cleaner">Cleaner</option>
                                    <option value="Pharmacist">Pharmacist</option>
                                    <option value="Admin Staff">Admin Staff</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Email</label>
                                <input class="form-control" type="email" name="email">
                            </div>
                            <div class="form-group col-4">
                                <label>Phone Number</label>
                                <input class="form-control" type="text" name="phone_number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Martial Status</label>
                                <select class="form-control" name="marital_status">
                                    <option value="0">Single</option>
                                    <option value="1">Married</option>
                                    <option value="2">Widowed</option>
                                    <option value="3">Divorced</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Nationality</label>
                                <input class="form-control" type="text" name="nationality" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Native Language</label>
                                <input class="form-control" type="text" name="native_language">
                            </div>
                            <div class="form-group col-4">
                                <label>Tazkira Number</label>
                                <input class="form-control" type="text" name="tazkira_number">
                            </div>
                            <div class="form-group col-4">
                                <label>Current Address</label>
                                <input class="form-control" type="text" name="current_address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Permanent Address</label>
                                <input class="form-control" type="text" name="permanent_address">
                            </div>
                            <div class="form-group col-4">
                                <label>Contract Start Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="contract_start"
                                    value={{ date('Y-m-d') }} required>
                            </div>
                            <div class="form-group col-4">
                                <label>Contract End Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="contract_end" value={{ date('Y-m-d') }}
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label>Contract Files</label>
                                <input class="form-control" type="file" name="contract_files[]" multiple>
                            </div>
                            <div class="form-group col-3">
                                <label>Salary Amount <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="salary" required>
                            </div>
                            <div class="form-group col-3">
                                <label>Status<span class="text-danger">*</span></label>
                                <select class="form-control" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                            <div class="form-group col-3">
                                <label>User<span class="text-danger">*</span></label>
                                <select class="form-control" name="user">
                                    <option>No User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label>Attendance ID</label>
                                <input class="form-control" type="number" name="attendance_id">
                            </div>
                            <div class="form-group col-3">
                                <label>Check In</label>
                                <input class="form-control" type="time" name="check_in">
                            </div>
                            <div class="form-group col-3">
                                <label>Check Out</label>
                                <input class="form-control" type="time" name="check_out">
                            </div>
                            <div class="form-group col-3">
                                <label>Staff Photo</label>
                                <input class="form-control" type="file" name="image">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Additional Comments</label>
                                <textarea class="form-control" name="comment"></textarea>
                            </div>
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

    <div class="modal fade" id="viewProfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Profile </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewProfileBody">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="setPercentage" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        aria-labelledby="exampleModalLabel1" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Set Percentage </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="setPercentageBody">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#datatable thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#datatable thead');

            var table = $('#datatable').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                "pageLength": 100,
                initComplete: function() {
                    var api = this.api();

                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function(colIdx) {
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
                                .on('keyup change', function(e) {
                                    e.stopPropagation();

                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr =
                                        '({search})'; //$(this).parents('th').find('select').val();

                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != '' ?
                                            regexr.replace('{search}', '(((' + this.value +
                                                ')))') :
                                            '',
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


        $('#editEmpModal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var empId = button.data('emp-id');
            var first_name = button.data('name');
            var last_name = button.data('last');
            var father_name = button.data('father');
            var dob = button.data('dob');
            var position = button.data('position');
            var email = button.data('email');
            var phone = button.data('phone');
            var nationality = button.data('nationality');
            var gender = button.data('gender');
            var marital_status = button.data('marital');
            var department_name = button.data('dep-name');
            var native_language = button.data('native');
            var tazkira_number = button.data('tazkira');
            var current_address = button.data('current-add');
            var permanent_address = button.data('permanent-add');
            var contract_start = button.data('contract-start');
            var contract_end = button.data('contract-end');
            var attendanceId = button.data('attendance-id');
            var checkIn = button.data('check-in');
            var checkOut = button.data('check-out');
            var status = button.data('status');
            var comment = button.data('comment');
            var empSalary = button.data('empsalary');
            var user = button.data('user')
            var modal = $(this)

            // Set values in edit popup
            var action = '/employee/' + id;
            $("#edit_emp_form").attr("action", action);
            modal.find('.modal-body [name="emp_id"]').val(id);
            modal.find('.modal-body [name="employee_id"]').val(empId);
            modal.find('.modal-body [name="first_name"]').val(first_name);
            modal.find('.modal-body [name="last_name"]').val(last_name);
            modal.find('.modal-body [name="father_name"]').val(father_name);
            modal.find('.modal-body [name="dob"]').val(dob);
            modal.find('.modal-body [name="position"]').val(position);
            modal.find('.modal-body [name="email"]').val(email);
            modal.find('.modal-body [name="phone_number"]').val(phone);
            modal.find('.modal-body [name="nationality"]').val(nationality);
            modal.find('.modal-body [name="gender"]').val(gender);
            modal.find('.modal-body [name="marital_status"]').val(marital_status);
            modal.find('.modal-body [name="department_name"]').val(department_name);
            modal.find('.modal-body [name="native_language"]').val(native_language);
            modal.find('.modal-body [name="tazkira_number"]').val(tazkira_number);
            modal.find('.modal-body [name="current_address"]').val(current_address);
            modal.find('.modal-body [name="permanent_address"]').val(permanent_address);
            modal.find('.modal-body [name="contract_start"]').val(contract_start);
            modal.find('.modal-body [name="contract_end"]').val(contract_end);
            modal.find('.modal-body [name="status"]').val(status);
            modal.find('.modal-body [name="salary"]').val(empSalary);
            modal.find('.modal-body [name="attendance_id"]').val(attendanceId);
            modal.find('.modal-body [name="check_in"]').val(checkIn);
            modal.find('.modal-body [name="check_out"]').val(checkOut);
            modal.find('.modal-body [name="comment"]').val(comment);
            modal.find('.modal-body [name="user"]').val(user);
        })

        function viewProfile(id) {
            if (id != '') {
                $('#viewProfileBody').empty();
                $('#viewProfileBody').load('{{ url('getEmployeeProfile/') }}' + '/' + id, function() {});
            }
        }

        function setPercentage(id) {
            if (id != '') {
                $('#setPercentageBody').empty();
                $('#setPercentageBody').load('{{ url('getPercentage/') }}' + '/' + id, function() {});
            }
        }
    </script>
@endsection
