<!doctype html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/style.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        .print-header {
            display: none;
        }

        @media print {

            .page-title {
                display: none;
            }

            .print-header {
                display: block;
            }

            .content-wrapper {
                padding-top: 0 !important;
            }
        }

    </style>

</head>

<body>

    <div class="content-wrapper">
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                <header class="custom-banner">
                    <div class="row gutters">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="welcome-msg">
                                <div class="welcome-user-thumb">
                                    <img src="{{ asset('EmpImages') }}\{{ $employee->image }}"
                                        alt='Employee Profile' style="width: 120px !important; height: 120px !important" />
                                </div>
                                <div class="welcome-title">
                                    {{ ucfirst($employee->first_name) }} {{ $employee->last_name }}
                                </div>
                                <div class="welcome-designation">
                                   {{ ucfirst($employee->employee_id) }}
                                </div>
                                <div class="welcome-designation">
                                    {{ ucfirst($employee->position) }}
                                </div>
                                <div class="welcome-designation">
                                    {{ ucfirst($employee->department) }}
                                </div>
                                <div class="welcome-designation">
                                    {{ $employee->employeeCurrentSalary->salary_amount }} (Salary)
                                </div>
                                <div class="welcome-email" >
                                    {{ $employee->email }}
                                </div>
                                <div class="welcome-email">
                                    {{ $employee->phone_number }}
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Father Name:</td>
                                    <td>{{ $employee->father_name }}</td>
                                </tr>
                                <tr>
                                    <td>DOB:</td>
                                    <td>{{ $employee->dob }}</td>
                                </tr>
                                <tr>
                                    <td>Gender:</td>
                                    <td>{{ ($employee->gender == 0) ? 'Male' : 'Female' }}</td>
                                </tr>
                                <tr>
                                    <td>Tazkira Number:</td>
                                    <td>{{ $employee->tazkira_number }}</td>
                                </tr>
                                <tr>
                                    <td>Contract Start Date:</td>
                                    <td>{{ $employee->contract_start }}</td>
                                </tr>
                                <tr>
                                    <td>Contract End Date:</td>
                                    <td>{{ $employee->contract_end }}</td>
                                </tr>
                                <tr>
                                    <td>Attendance ID:</td>
                                    <td>{{ $employee->attendance_id }}</td>
                                </tr>
                                <tr>
                                    <td>Daily Attendance Time:</td>
                                    <td>{{ $employee->check_in .'-'.$employee->check_out}}</td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </header>
            </div>
        </div>

    </div>



    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/nav.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <!-- Main Js Required -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
