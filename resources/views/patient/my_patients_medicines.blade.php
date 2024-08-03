@extends('layouts.master')

@section('page_title')
   My Patient List (Pharmacy)
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

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{url('search_my_patient')}}" method="post">
                        @csrf
                    <input type="text" name="search_patient" class="search-query"
                          value="{{(Request::is('search_my_patient') ? $patientSearchDetail : '')}}" placeholder="Search Patient By Id, Name or Phone..." required>
                    <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
    @endsection
@section('page-action')
    @if(\Request::is('search_my_patient'))
    <a type="button" class="btn btn-danger btn-sm" href="{{route('my_patients')}}">
        Clear Search
    </a>
    @endif
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
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Mobile</th>
                        <th>Age</th>
                        <th>Register Date</th>
                        <th>Register By</th>
                        <th>Doctor</th>
                        <th>Blood Group</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($patients  as $patient)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$patient->patient_generated_id}}</td>
                            <td>{{$patient->patient_name}}</td>
                            <td>{{$patient->patient_fname}}</td>
                            <td>{{$patient->patient_phone}}</td>
                            <td>{{$patient->age}}</td>
                            <td>{{$patient->created_at}}</td>
                            <td>{{($patient->created_by != NULL) ? $patient->createdBy->name : 'Not Added'}}</td>
                            <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>
                            <td>{{$patient->blood_group}}</td>
                            <td>
                                @if(!$patient->medicines->isEmpty() && in_array('doctor_edit_sale_medicine', $user_permissions))

                                    <button class="btn btn-sm btn-dark" data-patient-id="{{$patient->id}}"
                                            data-toggle="modal" data-target="#editPatientMedicine"
                                            data-patient-name="{{$patient->patient_name}}"
                                            onclick="editMedicine({{$patient->id}})">View Medicine
                                    </button>
                                @else
                                    @if(in_array('doctor_sale_medicine', $user_permissions))
                                    <button class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#exampleModal"
                                            data-patient-id="{{$patient->id}}"
                                            data-patient-name="{{$patient->patient_name}}">Set Medicine
                                    </button>
                                        @endif
                                @endif


                               
                                
                                <button class="btn btn-sm btn-light mt-1" data-toggle="modal"
                                    data-target="#vitalSignsModal"
                                    data-patient-id="{{$patient->id}}"
                                    data-patient-name="{{$patient->patient_name}}"
                                    data-blood-pressure="{{ $patient->blood_pressure }}"
                                    data-respiration="{{ $patient->respiration_rate }}"
                                    data-pulse="{{ $patient->pulse_rate }}"
                                    data-heart="{{ $patient->heart_rate }}"
                                    data-temperature="{{ $patient->temperature }}"
                                    data-weight="{{ $patient->weight }}"
                                    data-height="{{ $patient->height }}"
                                    data-mental-state="{{ $patient->mental_state }}"
                                    data-medical-history="{{ $patient->medical_history }}"
                                    data-va-1="{{ $patient->va_1 }}"
                                    data-va-2="{{ $patient->va_2 }}"
                                    data-iop-1="{{ $patient->iop_1 }}"
                                    data-iop-2="{{ $patient->iop_2 }}"
                                    data-chief-complaint="{{ $patient->chief_complaint }}"
                                    data-dx="{{ $patient->dx }}"> Vital Signs
                                </button>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center font-weight-bold" ><h5>No Data Available</h5></td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
                {{ $patients->appends(Request::all())->links()}}
            </div>
        </div>
    </div>

    {{--Sell Medicine Modal--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Medicine to Patient<span id="medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <datalist id="datalist">
                        @foreach ($medicine_dosage as $dose)
                            <option>{{ $dose->remarks }}</option>
                        @endforeach
                    </datalist>

                    <form action="{{route('patient_medicine.store')}}" method="post" enctype="multipart/form-data" id="medicineForm">
                        {!! csrf_field() !!}
                        <input type="hidden" name="patient_id" id="medicine_patient_id">

                        <div class="form-group">
                            <label>Select Medicine</label>
                            <div class="input-group">
                                <select class="form-control selectpicker medicineItems"  data-live-search="true" name="medicine_id[]" required>
                                    <option value="" selected disabled hidden>Please select</option>
                                    @foreach($selectPharmacy as $key => $medicine)
                                        <?php $maxSalePrice = 0; ?>
                                        @if($medicine->thisMedicinePharmacy->sum('quantity'))
                                            <?php
                                                $i=1;
                                                foreach ($medicine->thisMedicinePharmacy as $medicineSalePrice){
                                                    if($medicineSalePrice->sale_price > $maxSalePrice && $i < 2){
                                                        $maxSalePrice = $medicineSalePrice->sale_price;
                                                    }
                                                    $i++;
                                                }
                                            ?>
                                            <option value="{{$medicine->id}}" sale_price="{{$maxSalePrice}}">{{ucfirst($medicine->medicine_name)}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="number" class="form-control medicineQTY" name="quantity[]" placeholder="Quantity" style="height: 38px !important;">
                                <input type="text" class="form-control" name="remark[]" placeholder="Remark" style="height: 38px !important;" list="datalist" data-ms-editor="true" spellcheck="false">

                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>

                        </div>
                        <div id="add_more">
                        </div>
                        <div>
                            <b>Total: <span id="medicine_total_sale_price">0</span></b>
                        </div>
                        <div class="submit-section">
                            <br>
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn btn-sm" type="submit">Submit</button>

                            @if(in_array('doctor_request_medicine', $user_permissions))

                                <a href="" class="text text-right text-danger pull-right float-right"
                                   data-toggle="modal" data-target="#requestMedicineModal"
                                   style="text-decoration: underline">
                                    Request New Medicine</a>
                                @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--Request Medicine Modal--}}
    <div class="modal fade" id="requestMedicineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="exampleModalLabel">Please add your requested Medicine Here:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data">
                        <input type="text"  class="form-control" name="requestedMedicine[]">
                        <input type="text"   class="form-control mt-1" name="requestedMedicine[]">
                        <input type="text"   class="form-control mt-1" name="requestedMedicine[]">
                        <div class="submit-section">
                            <br>
                            <button class="btn btn-primary btn-danger btn-sm float-right" type="button" id="requestedMedicineBtn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--Edit Patient Medicine Modal--}}
    <div class="modal fade" id="editPatientMedicine" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Medicine Patient <span id="medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editPatientMedicineBody">
                </div>
            </div>
        </div>
    </div>
        
        {{-- Vital Signs Modal --}}
        <div class="modal fade" id="vitalSignsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Vital Signs of <span id="vital_patient_name"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row gutters">
                            <div class="col-lg-12 col-md-12 col-sm-12 order-last">
                                <form action="{{ route('patient_vital_sign') }}" method="POST">
                                    {!! csrf_field() !!}
                                    <input type="hidden" id="vital_signs_patient_id" name="patient_id">
                                <table class=" no-border m-0">
                                    <tbody>
                                        <tr>
                                            <td><p><b>Blood Pressure:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_blood_input" name="blood_pressure">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><b>Respiration Rate:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_respiratin_input" name="respiration_rate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Pulse Rate:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_pulse_input" name="pulse_rate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>PSO2:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_heart_input" name="heart_rate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>  <p><b>Temperature:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_temperature_input" name="temperature">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Weight:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_weight_input" name="weight">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Height:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_height_input" name="height">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Mental State:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_mental_input" name="mental_state">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Medical History:</b></p></td>
                                            <td>
                                                <input type="text" id="vital_history_input" name="medical_history">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><b>VA:</b></p></td>
                                            <td>
                                                <input type="text" id="va1_input" name="va_1">
                                            </td>
                                            <td>
                                                <input type="text" id="va2_input" name="va_2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>IOP:</b></p></td>
                                            <td>
                                                <input type="text" id="iop1_input" name="iop_1">
                                            </td>
                                            <td>
                                                <input type="text" id="iop2_input" name="iop_2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <p><b>Chief Complaint:</b></p></td>
                                            <td>
                                                <input type="text" id="chiefComplaint_input" name="chief_complaint" list="chief_complaint_data" data-ms-editor="true" spellcheck="false">
                                            </td>
                                        </tr>

                                        <datalist id="chief_complaint_data">
                                            <option>Tearing</option>
                                            <option>Discharge</option>
                                            <option>Pain</option>
                                            <option>Foreign body sensation</option>
                                            <option>Photobia</option>
                                            <option>Headaque</option>
                                            <option>VA Down Arrow</option>
                                        </datalist>

                                        <tr>
                                            <td> <p><b>DX:</b></p></td>
                                            <td>
                                                <input type="text" id="dx_input" name="dx" list="dx_data" data-ms-editor="true" spellcheck="false">
                                            </td>
                                        </tr>

                                        <datalist id="dx_data">
                                            <option>Viral Conjectivitis</option>
                                            <option>NLDO</option>
                                            <option>CDC</option>
                                            <option>ADC</option>
                                            <option>Galucoma</option>
                                            <option>Alergic conjectivitis</option>
                                            <option>Contracat</option>
                                            <option>RD</option>
                                            <option>DM Retiropathy</option>
                                            <option>HTN Retiropathy</option>
                                            <option>Referactive Error</option>
                                            <option>Hypermetropia</option>
                                            <option>Myopia</option>
                                            <option>Nystigmus</option>
                                        </datalist>

                                        <tr>
                                            <td>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-warning" id="printPatientVitalSignButton" onclick="printPatientVitalSign(this)" patient_id= "0">Print</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>
    <script>
        function addnew(){
            $('#add_more').append(`
             <div class="form-group"> <div class="input-group"><select class="form-control selectpicker medicineItems" data-live-search="true" name="medicine_id[]">
                <option value="" selected disabled hidden>Please select</option>
                @foreach($selectPharmacy as $key => $medicine)

                @if($medicine->thisMedicinePharmacy->sum('quantity'))
                <?php
                $maxSalePrice = 0;
                $i=1;
                foreach ($medicine->thisMedicinePharmacy->take(2) as $medicineSalePrice){
                    if($medicineSalePrice->sale_price > $maxSalePrice && $i < 2){
                       $maxSalePrice = $medicineSalePrice->sale_price;
                    }
                    $i++;
                }
                ?>
                <option value="{{$medicine->id}}" sale_price="{{$maxSalePrice}}">{{ucfirst($medicine->medicine_name)}}</option>
                               @endif
                       @endforeach
            </select>
            <input type="number" class="form-control medicineQTY" name="quantity[]" placeholder="Quantity" style="height: 38px !important;">
             <input type="text" class="form-control" name="remark[]" placeholder="Remark" style="height: 38px !important;" list="datalist" data-ms-editor="true" spellcheck="false">
            <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
        </div>
        </div>
`);
            $(".selectpicker").selectpicker('refresh')

        }
        
        function clearInput() {
            $('#medicineForm').find('input[type=text], input[type=password], input[type=number], input[type=email], input[type=checkbox],textarea').val('');
        };
        function clearInputOfRequestModal() {
            $('#requestMedicineModal').find('input[type=text], input[type=password], input[type=number], input[type=email], input[type=checkbox],textarea').val('');
        };

        $('#exampleModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id = button.data('patient-id');
            var patient_name = button.data('patient-name');
            var modal = $(this)

            // Set values in edit popup
            $("#medicine_patient_id").val(patient_id);
            modal.find('.modal-content #medicine_patient_name').html('<b class="text text-danger"> ('+patient_name+')</b>');
        })
        
        $('#exampleModal').on('hidden.bs.modal', function(){
            clearInput();
            $('#add_more').empty();
            $('#medicine_total_sale_price').html('<b>' + '0' + '</b>');
        });

    </script>
    
    <script>
        var spanValue = 0;
        var remarkTitle = '';
        var no_discount = 0;

        $('#requestedMedicineBtn').click(function () {
            var requestedMedicines = $("input[name='requestedMedicine[]']")
                .map(function(){return $(this).val();}).get();
            $.ajax({
                type: "POST",
                url: '{{route('save_requested_medicine')}}',
                data: {values: requestedMedicines, '_token':'{{csrf_token()}}'},
                success: function (response) {
                    $('#requestMedicineModal').modal('hide');
                },
                error: function () {
                    alert("An Error Occured, Please try again!");
                }
            });
        })

        function editMedicine(id) {
            if (id != '') {
                $('#editPatientMedicineBody').empty();
                $('#editPatientMedicineBody').load('{{ url("getPatientMedicinesForEdit/") }}'+'/'+id, function () {
                    $(".selectpicker").selectpicker('refresh');
                    setTotalPriceOfMedicine();
                });

            }
        }

        

        
        $(document).on('input','.medicineQTY',function () {
            setTotalPriceOfMedicine();
        });
        $(document).on('change','.medicineItems',function () {
            setTotalPriceOfMedicine();
        });

        function setTotalPriceOfMedicine() {
            var grandTotalPrice = 0;
            var totalValues =  $(".medicineItems :selected").map((i, el) => $(el).attr("sale_price")).toArray();
            var totalQuantities =  $(".medicineQTY").map((i, el) => $(el).val()).toArray();
            console.log(totalQuantities, totalValues);
            for (var i = 0; i < totalValues.length; i++) {
                grandTotalPrice += totalValues[i] * totalQuantities[i] << 0;
            }
            $('#medicine_total_sale_price').html('<b>' + grandTotalPrice + '</b>');

        }
        
    $('#vitalSignsModal').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var patient_id= button.data('patient-id');
            var patient_name = button.data('patient-name');
            var blood = button.data('blood-pressure');
            var respiration = button.data('respiration');
            var pulse = button.data('pulse');
            var heart = button.data('heart');
            var temperature = button.data('temperature');
            var weight = button.data('weight');
            var height = button.data('height');
            var mental = button.data('mental-state');
            var history = button.data('medical-history');
            var va1 = button.data('va-1');
            var va2 = button.data('va-2');
            var iop1 = button.data('iop-1');
            var iop2 = button.data('iop-2');
            var chiefComplaint = button.data('chief-complaint');
            var dx = button.data('dx');
            var modal = $(this)

            // Set values in edit popup
            $("#vital_patient_name").html('<b class="text text-danger"> ('+patient_name+')</b>');;
            modal.find('.modal-content #vital_blood').html(blood);
            modal.find('.modal-content #vital_respiratin').html(respiration);
            modal.find('.modal-content #vital_pulse').html(pulse);
            modal.find('.modal-content #vital_heart').html(heart);
            modal.find('.modal-content #vital_temperature').html(temperature);
            modal.find('.modal-content #vital_weight').html(weight);
            modal.find('.modal-content #vital_height').html(height);
            modal.find('.modal-content #vital_mental').html(mental);
            modal.find('.modal-content #vital_history').html(history);
            modal.find('.modal-content #va1').html(va1);
            modal.find('.modal-content #va2').html(va2);
            modal.find('.modal-content #iop1').html(iop1);
            modal.find('.modal-content #iop2').html(iop2);
            modal.find('.modal-content #chiefComplaint').html(chiefComplaint);
            modal.find('.modal-content #dx').html(dx);

            // Update Input
            modal.find('.modal-content #vital_blood_input').val(blood);
            modal.find('.modal-content #vital_respiratin_input').val(respiration);
            modal.find('.modal-content #vital_pulse_input').val(pulse);
            modal.find('.modal-content #vital_heart_input').val(heart);
            modal.find('.modal-content #vital_temperature_input').val(temperature);
            modal.find('.modal-content #vital_weight_input').val(weight);
            modal.find('.modal-content #vital_height_input').val(height);
            modal.find('.modal-content #vital_mental_input').val(mental);
            modal.find('.modal-content #vital_history_input').val(history);
            modal.find('.modal-content #vital_signs_patient_id').val(patient_id);
            modal.find('.modal-content #va1_input').val(va1);
            modal.find('.modal-content #va2_input').val(va2);
            modal.find('.modal-content #iop1_input').val(iop1);
            modal.find('.modal-content #iop2_input').val(iop2);
            modal.find('.modal-content #chiefComplaint_input').val(chiefComplaint);
            modal.find('.modal-content #dx_input').val(dx);

            modal.find('.modal-content #printPatientVitalSignButton').attr('patient_id', patient_id);
        });

        function printPatientVitalSign(){
            let patient_vital_id = $(event.target).attr('patient_id');
            console.log(patient_vital_id);
            printExternal('{{ url("printVitalSignOfPatient") }}'+"?patient_id="+patient_vital_id);
        }
        function printExternal(url) {
            var printWindow = window.open( url, 'Print');

            printWindow.addEventListener('load', function() {
                if (Boolean(printWindow.chrome)) {
                    printWindow.print();
                    setTimeout(function(){
                        printWindow.close();
                    }, 500);
                } else {
                    printWindow.print();
                    printWindow.close();
                }
            }, true);
        }
    </script>
@endsection
