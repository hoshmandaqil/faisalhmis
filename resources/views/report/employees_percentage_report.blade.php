@extends('layouts.master')

@section('page_title')
    Employees Percentage Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
    <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>
@endsection
@section('styles')
    <style>
        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        .lab_tests_table tr td {
            border: none !important;
        }

        .lab_tests_td {
            width: 30%;
        }

        .lab_tests_tr {
            display: inline-block;
            height: 30px;
        }
    </style>
@endsection

@section('on_print_page_header')
    @include('layouts.page_header_print', [
        'reportName' => 'Employees Percentage Report',
        'from' => $from,
        'to' => $to,
    ])
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
            @if (empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>OPD</th>
                                <th>IPD</th>
                                @foreach ($labMainDepartments as $dep)
                                    <th>{{ $dep->dep_name }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                @php
                                    //OPD
                                    $opd_sum_based_on_percentage = ($employee->opd_sum * (float) $employee->opd_percentage) / 100;
                                    $opd_sum_based_on_amount = $employee->opd_count * (float) $employee->opd_amount;
                                    
                                    //IPD
                                    $ipd_sum_based_on_percentage = ($employee->ipd_sum * (float) $employee->ipd_percentage) / 100;
                                    $ipd_sum_based_on_amount = $employee->ipd_count * (float) $employee->ipd_amount;
                                @endphp
                                <tr>
                                    <td>{{ $employee->first_name . ' ' . $employee->last_name }}</td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>Total:</td>
                                                <td>{{ $employee->opd_sum }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Percentage:</td>
                                                <td>{{ (float) $employee->opd_percentage }}</td>
                                            </tr>
                                            <tr>
                                                <td>Amount:</td>
                                                <td>{{ (float) $employee->opd_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td>Payable:</td>
                                                <td>{{ $total_opd = number_format($opd_sum_based_on_percentage + $opd_sum_based_on_amount, 2, '.', '') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>Total:</td>
                                                <td>{{ $employee->ipd_sum }}</td>
                                            </tr>
                                            <tr>
                                                <td>Percentage:</td>
                                                <td>{{ (float) $employee->ipd_percentage }}</td>
                                            </tr>
                                            <tr>
                                                <td>Amount:</td>
                                                <td>{{ (float) $employee->ipd_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td>Payable:</td>
                                                <td>{{ $total_ipd = number_format($ipd_sum_based_on_percentage + $ipd_sum_based_on_amount, 2, '.', '') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @php
                                        $employee_all_lab = 0;
                                    @endphp
                                    @foreach ($labMainDepartments as $dep)
                                        @php
                                            //Calculating sum of current lab
                                            $sum_total_lab = 0;
                                            
                                            if($dep->id != 7){
                                                if (!empty($employee->laboratoryTests)) {
                                                    foreach ($employee->laboratoryTests as $lab_test) {
                                                        if ($lab_test->testName->main_dep_id == $dep->id) {
                                                            $sum_total_lab += ($lab_test->price*(100-$lab_test->discount)/100);
                                                        }
                                                    }
                                                }
                                            }else{
                                                $ultrasound = App\Models\LaboratoryPatientLab::whereDate('created_at', '>=', $from)
                                                    ->whereDate('created_at', '<=', $to)
                                                    ->where('created_by', $employee->user_id)
                                                    ->get();
                                                    
                                                foreach ($ultrasound as $lab_test) {
                                                    if ($lab_test->testName->main_dep_id == $dep->id) {
                                                        $sum_total_lab += ($lab_test->price*(100-$lab_test->discount)/100);
                                                    }
                                                }
                                            }
                                            
                                            //Calculating doctor's percentage
                                            $employee_lab = $employee
                                                ->labPercentage()
                                                ->where('main_lab_department_id', $dep->id)
                                                ->first();
                                            
                                            $percentage = $employee_lab ? $employee_lab->pivot->percentage : 0;
                                        @endphp
                                        <td>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td>Department:</td>
                                                    <td>{{ $dep->dep_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Total:</td>
                                                    <td>{{ $sum_total_lab }}</td>
                                                </tr>
                                                <tr>
                                                    <td>%age:</td>
                                                    <td>{{ (float) $percentage }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Payable:</td>
                                                    <td>{{ $current_lab_total = number_format(($sum_total_lab * (float) $percentage) / 100, 2, '.', '') }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        @php
                                            //Sum all labs for a specific employee
                                            $employee_all_lab += $current_lab_total;
                                        @endphp
                                    @endforeach
                                    <td>{{ $employee_all_lab + $total_opd + $total_ipd }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Employees Percentage Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('employee_percentage_report') }}" method="GET" enctype="multipart/form-data"
                        id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from"
                                value="{{ $from != null ? $from : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to"
                                value="{{ $to != null ? $to : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Department:</label>
                            <select class="form-control" name="department">
                                <option>General</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control" name="doctor">
                                <option>General</option>
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
    </div>
@endsection
@section('scripts')
    <script>
        $('.zeroValueTr').each(function(i) {
            if ($(this).text() == 0) {
                $(this).parent().remove();
            }
        });

        $('.serial_number').each(function(idx, elem) {
            $(elem).text(idx + 1);
        });
    </script>
@endsection
