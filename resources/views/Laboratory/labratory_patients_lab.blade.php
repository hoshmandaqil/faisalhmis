@extends('layouts.master')

@section('page_title')
   Laboratory Patient List
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
                    <form action="{{url('search_laboratory_lab_patients')}}" method="post">
                        @csrf
                        <input type="text" name="search_patient" class="search-query"
                               value="{{(Request::is('search_laboratory_lab_patients') ? $patientSearchDetail : '')}}" placeholder="Search Patient By Id, Name or Phone...">
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
                        <th>Blood Group</th>
                        <th>Reg Date</th>
                        <th>Registered By</th>
                        <th>Doctor</th>
                         <th>Departments</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($labPatients  as $patient)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$patient->patient_generated_id}}</td>
                            <td>{{$patient->patient_name}}</td>
                            <td>{{$patient->patient_fname}}</td>
                            <td>{{$patient->patient_phone}}</td>
                            <td>{{$patient->age}}</td>
                            <td>{{$patient->blood_group}}</td>
                            <td>{{$patient->created_at}}</td>
                            <td>{{($patient->created_by != NULL) ? $patient->createdBy->name : 'Not Added'}}</td>
                            <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>

                              <td>
                                    <?php $mainDepartmentNames = []; ?>

                                    @foreach ($patient->labs as $labIndividualTests)

                                        @if (!in_array($labIndividualTests->lab->mainDepartment->dep_name, $mainDepartmentNames, true))
                                            <?php array_push($mainDepartmentNames, $labIndividualTests->lab->mainDepartment->dep_name); ?>
                                        @endif

                                    @endforeach

                                    @foreach ( $mainDepartmentNames as $mainDepName)
                                    <span class="badge text-small">{{ $mainDepName }}</span><br>
                                    @endforeach

                                </td>

                            <td>
                                @if($patient->laboratoryTests->isEmpty())
                                <button class="btn btn-sm btn-danger" data-patient-id="{{$patient->id}}"
                                        data-toggle="modal" data-target="#laboratorySetLabModal"
                                        data-patient-name="{{$patient->patient_name}}"
                                        onclick="showPatientLabs({{$patient->id}})">Set Tests & Results
                                </button>
                                    @else
                                    @if($patient->laboratoryTests()->exists())
                                        <button class="btn btn-sm btn-secondary" data-patient-id="{{$patient->id}}"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="printPreviewLabs({{$patient->id}})">Print <i
                                                class="icon icon-print"></i></button>

                                        <button class="btn btn-sm btn-info" data-patient-id="{{$patient->id}}"
                                                data-toggle="modal" data-target="#previewModal"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="previewLabs({{$patient->id}})">Preview
                                        </button>



                                            @if($patient->labs->count() > $patient->laboratoryTests->count())
                                                <button class="btn btn-sm btn-danger" data-patient-id="{{$patient->id}}"
                                                data-toggle="modal" data-target="#laboratorySetLabModal"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="showPatientLabs({{$patient->id}})" title="Set New Tests">
                                                <i class="icon icon-plus-circle"></i>
                                            @else
                                                <button class="btn btn-sm btn-warning" data-patient-id="{{$patient->id}}"
                                                data-toggle="modal" data-target="#laboratorySetLabModal"
                                                data-patient-name="{{$patient->patient_name}}"
                                                onclick="showPatientLabs({{$patient->id}})" title="Set New Tests">
                                                <i class="icon icon-plus-circle"></i>
                                            @endif
                                            
                                        </button>

                                            <a href="{{ url('/download_lab_files', $patient->id) }}" class="btn btn-success btn-sm" title="Download Tests Result"><i
                                            class="icon icon-download"></i></a>

                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center font-weight-bold" ><h5>No Data Available</h5></td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
                {{ $labPatients->links() }}
            </div>
        </div>

        <div class="modal fade" id="laboratorySetLabModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Patient Test & Results <span
                                id="lab_patient_name"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="laboratory_set_lab_modal_body">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Patient Laboratory Test <span id="sell_medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="preview_modal_body">
                    <iframe  id="page_iframe" height="480" width="100%" frameBorder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        function showPatientLabs(id) {
            if (id != '') {
                $('#laboratory_set_lab_modal_body').empty();
                $('#laboratory_set_lab_modal_body').load('{{ url("laboratoryGetPatientLabs") }}' + "?patient_id=" + id, function () {
                    // setTotalPrice();
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
               function printPreviewLabs(id) {

            if (id != '') {
                {{--$('#preview_modal_body').empty();--}}
                {{--$('#preview_modal_body').load('{{ url("previewPatientMedicines") }}'+"?patient_id="+id, function () {--}}
                {{--}); --}}
                // $('#page_iframe').empty();
                {{--$('#page_iframe').attr('src','{{ url("previewPatientMedicines") }}'+"?patient_id="+id);--}}
                printExternal('{{ url("previewPatientLabTests") }}'+"?patient_id="+id);
            }
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

        function previewLabs(id) {

            if (id != '') {
                {{--$('#preview_modal_body').empty();--}}
                {{--$('#preview_modal_body').load('{{ url("previewPatientMedicines") }}'+"?patient_id="+id, function () {--}}
                {{--}); --}}
                $('#page_iframe').empty();
                $('#page_iframe').attr('src','{{ url("previewPatientLabTests") }}'+"?patient_id="+id);
            }
        }
    </script>

@endsection
