@extends('layouts.master')

@section('page_title')
    Referral Report {{($department_id ==0 ) ? "( General Report )" :  '( '.$labMainDepartmentName.' Report )'}}
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
        <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

@endsection
@section('styles')
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
        }
        .lab_tests_table tr td {
         border: none !important;
        }
        .lab_tests_td {
            width: 30%;
        }
        .lab_tests_tr {
            display:inline-block;
            height:30px;
        }
    </style>
@endsection

@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'Laboratory Sales Report', 'from' => $from, 'to' => $to])
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
            @if(empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Patient Name</th>
                            <th>Patient Id</th>
                            <th>Laboratory Tests</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Payable</th>
                            <th>Registered By</th>
                            <th>Reg. to</th>
                            <th>Referred to</th>
                            <th>Date</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $grandTotalLabPrices = 0; $grandTotalLabDiscount = 0; $grandTotalLabPayable = 0;?>
                        @foreach($labSalePatients  as $labTest)
                            <tr>
                                <td class="serial_number">{{$loop->iteration}}</td>
                                <td>{{$labTest->patient_name}}</td>
                                <td>{{$labTest->patient_generated_id}}</td>
                                <td class="lab_tests_td">
                                    <?php $totalLabPrice =0; $totalDiscount = 0;?>
                                    <table class="lab_tests_table">
                                        @foreach($labTest->laboratoryTests as $test)

                                            @if($test->testName != NULL)
                                                <tr class="lab_tests_tr">
                                                    <td>  {{$test->testName->dep_name}} ({{$test->testName->price}})</td>
                                                    <?php 
                                                    $originalPrice = $test->testName->price;
                                                    $totalLabPrice += $originalPrice;
                                                    $discountForTest = ($test->discount * $originalPrice)/100;
                                                    $totalDiscount += $discountForTest;
//                                                    DB::table('laboratory_patient_labs')->where('id', $test->id)
//                                                        ->update(['discount' => $test->testName->mainDepartment->discount]);
                                                    ?>
                                                </tr>
                                            @endif
                                        @endforeach

                                    </table>
                                </td>
                                <td>{{$totalLabPrice}}</td>
                                <td>{{$totalDiscount}}</td>
                                <td>{{$totalLabPrice - $totalDiscount}}</td>
                                <td>{{($labTest->createdBy != NULL) ? $labTest->createdBy->name : 'No Name'}}</td>
                                <td>{{($labTest->doctor != NULL) ? $labTest->doctor->name : 'No Doctor'}}</td>
                                <td>

                                    <?php $labSavedBy = []; ?>

                                    @foreach ($labTest->laboratoryTests as $test)

                                        @if (!in_array($test->createdBy->name, $labSavedBy, true))
                                        @if($doctor_id != 0)
                                        @if($test->createdBy->id == $doctor_id)
                                        <?php array_push($labSavedBy, $test->createdBy->name); ?>
                                        @endif
                                        @else
                                        <?php array_push($labSavedBy, $test->createdBy->name); ?>
                                        @endif
                                        @endif

                                    @endforeach

                                    @foreach ( $labSavedBy as $savedBy)
                                    <span class="badge text-small">{{ $savedBy }}</span><br>
                                    @endforeach
                                </td>
                                <td>{{($labTest->laboratoryTests[0] != NULL) ? date("Y-m-d H:i:s", strtotime($labTest->laboratoryTests[0]->created_at)) : ''}}</td>

                                <?php
                                $grandTotalLabPrices += $totalLabPrice;
                                $grandTotalLabDiscount += $totalDiscount;
                                $grandTotalLabPayable += $totalLabPrice - $totalDiscount;
                                ?>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"></td>
                            <td class="font-weight-bold">Total:</td>
                            <td class="font-weight-bold">{{number_format($grandTotalLabPrices)}}</td>
                            <td class="font-weight-bold">{{number_format($grandTotalLabDiscount)}}</td>
                            <td class="font-weight-bold">{{number_format($grandTotalLabPayable)}}</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Referral Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('referral_report')}}" method="GET" enctype="multipart/form-data" id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from" value="{{($from != NULL) ? $from : date('Y-m-d')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to" value="{{($to != NULL) ? $to : date('Y-m-d')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Department:</label>
                            <select class="form-control" name="department">
                                <option value="0" {{($department_id ==0) ? 'selected': ''}}>General</option>
                                @foreach($labMainDepartments as $department)
                                    <option value="{{$department->id}}" {{($department_id == $department->id) ? 'selected': ''}}>{{$department->dep_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control" name="doctor">
                                <option value="0" {{($doctor_id ==0) ? 'selected': ''}}>General</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{$doctor->id}}" {{($doctor_id == $doctor->id) ? 'selected': ''}}>{{$doctor->name}}</option>
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
    <script>
        $('.zeroValueTr').each(function(i){
            if ($(this).text() == 0){
                $(this).parent().remove();
            }
        });

        $('.serial_number').each(function(idx, elem){
            $(elem).text(idx+1);
        });
    </script>
@endsection
