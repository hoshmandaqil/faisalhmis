@extends('layouts.master')

@section('page_title')
    Select Medicine to Patient
@endsection

@section('styles')
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{url('search_medicine_patient')}}" method="post">
                        @csrf
                        <input type="text" name="search_patient" class="search-query"
                               value="{{(Request::is('search_medicine_patient') ? $patientSearchDetail : '')}}"
                               placeholder="Search Patient By Id, Name or Phone...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection

@section('page-action')
    @if(\Request::is('search_medicine_patient'))
        <a type="button" class="btn btn-danger btn-sm" href="{{url('patient_pharmacy_medicine')}}">
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
                        <th>Doctor</th>
                           <th>Reg. Date</th>
                        <th>Registered By</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($medicinePatients  as $patient)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$patient->patient_generated_id}}</td>
                            <td>{{$patient->patient_name}}</td>
                            <td>{{$patient->patient_fname}}</td>
                            <td>{{$patient->patient_phone}}</td>
                            <td>{{$patient->age}}</td>
                            <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>
                             <td>{{$patient->created_at}}</td>
                            <td>{{($patient->created_by != NULL) ? $patient->createdBy->name : 'Not Added'}}</td>
                            <td>

                                @if($patient->pharmacyMedicines()->exists())
                                    @if($patient->pharmacyMedicines['0']->status == 0)
                                        @if(in_array('pharmacy_preview_medicine', $user_permissions))

                                            <button class="btn btn-sm btn-info" data-patient-id="{{$patient->id}}"
                                                    data-toggle="modal" data-target="#previewModal"
                                                    data-patient-name="{{$patient->patient_name}}"
                                                    onclick="previewMedicine({{$patient->id}})">Preview
                                            </button>

                                        @endif
                                        <!--@if(in_array('pharmacy_edit_medicine', $user_permissions))-->
                                            <button class="btn btn-sm btn-warning"
                                                    data-patient-id="{{$patient->id}}"
                                                    data-toggle="modal" data-target="#editMedicineModal"
                                                    data-patient-name="{{$patient->patient_name}}"
                                                    onclick="editPatientMedicine({{$patient->id}})">Edit
                                            </button>

                                        <!--@endif-->
                                        
                                         <button class="btn btn-sm btn-warning" data-patient-id="{{$patient->id}}"
                                            data-toggle="modal" data-target="#saleMedicineModal"
                                            data-patient-name="{{$patient->patient_name}}"
                                            onclick="showPatientMedicine({{$patient->id}})"><i class="icon icon-plus-circle"></i>
                                        </button>
                                        
                                        @if(in_array('pharmacy_complete_medicine', $user_permissions))
                                            <a type="button" class="btn btn-success btn-sm"
                                               onclick="return confirm('Are you sure You want to complete this item?')"
                                               href="{{url('complete_medicine', $patient->id)}}">complete</a>
                                        @endif
                                    @else
                                        @if(in_array('pharmacy_preview_medicine', $user_permissions))
                                        <button class="btn btn-sm btn-info" data-patient-id="{{$patient->id}}"
                                                data-toggle="modal" data-target="#previewModal"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="previewMedicine({{$patient->id}})">Preview
                                        </button>
                                        @endif
                                        
                                         <button class="btn btn-sm btn-warning" data-patient-id="{{$patient->id}}"
                                            data-toggle="modal" data-target="#saleMedicineModal"
                                            data-patient-name="{{$patient->patient_name}}"
                                            onclick="showPatientMedicine({{$patient->id}})"><i class="icon icon-plus-circle"></i>
                                        </button>

                                        @if (in_array('pharmacy_uncomplete', $user_permissions))
                                            <a class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure You want to Uncomplete this item?')"
                                                href="{{ url('uncomplete_medicine/' . $patient->id) }}">X Uncomplete
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-danger">Completed <i
                                                    class="icon icon-check"></i>
                                            </button>
                                        @endif

                                    @endif
                                @else

                                    @if(in_array('pharmacy_sale_medicine', $user_permissions))
                                        <button class="btn btn-sm btn-danger" data-patient-id="{{$patient->id}}"
                                                data-toggle="modal" data-target="#saleMedicineModal"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="showPatientMedicine({{$patient->id}})">Sale Medicine
                                        </button>

                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {{$medicinePatients->links()}}
            </div>
        </div>
    </div>


    <div class="modal fade" id="saleMedicineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sell Medicine To <span
                            id="sell_medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="sale_medicine_modal_body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sell Medicine To <span
                            id="sell_medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="preview_modal_body">
                    <iframe id="page_iframe" height="300" width="100%" frameBorder="0">

                    </iframe>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Patient Medicine <span
                            id="sell_medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit_medicine_modal_body">
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        function showPatientMedicine(id) {
            if (id != '') {
                $('#sale_medicine_modal_body').empty();
                $('#sale_medicine_modal_body').load('{{ url("getPatientMedicines") }}' + "?patient_id=" + id, function () {
                    setTotalPrice();
                });
            }
        }

        function previewMedicine(id) {

            if (id != '') {
                {{--$('#preview_modal_body').empty();--}}
                {{--$('#preview_modal_body').load('{{ url("previewPatientMedicines") }}'+"?patient_id="+id, function () {--}}
                {{--}); --}}
                $('#page_iframe').empty();
                $('#page_iframe').attr('src', '{{ url("previewPatientMedicines") }}' + "?patient_id=" + id);
            }
        }

        function editPatientMedicine(id) {
            if (id != '') {
                $('#edit_medicine_modal_body').empty();
                $('#edit_medicine_modal_body').load('{{ url("pharmacyEditPatientMedicines") }}' + "?patient_id=" + id, function () {
                    setTotalPrice();
                });
            }
        }
    </script>
    <script>
        function removeMedicine(id) {
            $('div#' + id + '').remove();
            setTotalPrice();
        }

        function calculateTotalPrice(salePrice, divId, quantityValue) {
            var newValue = quantityValue * salePrice;
            var totalPrice = $('div#' + divId + '').find('input.totalPrice').val(newValue.toFixed(2));
            setTotalPrice();
        }

        function setTotalPrice() {
            var grandTotalPrice = 0;
            var totalValues = $('.totalPrice').map((_, el) => el.value).get();
            for (var i = 0; i < totalValues.length; i++) {
                grandTotalPrice += totalValues[i] << 0;
            }
            $('#patientTotalPrice').html('<b>Total: ' + grandTotalPrice + '</b>');
        }
    </script>
@endsection
