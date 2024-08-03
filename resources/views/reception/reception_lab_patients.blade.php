@extends('layouts.master')

@section('page_title')
    Print Patient Lab Tests
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
                    <form action="{{url('search_reception_lab_patient')}}" method="post">
                        @csrf
                        <input type="text" name="search_patient" class="search-query"
                               value="{{(Request::is('search_reception_lab_patient') ? $patientSearchDetail : '')}}" placeholder="Search Patient By Id, Name or Phone...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>

                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection
@section('page-action')
    @if(\Request::is('search_reception_lab_patient'))
        <a type="button" class="btn btn-danger btn-sm" href="{{url('search_reception_lab_patient')}}">
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
                        <th>Age</th>
                        <th>Doctor</th>
                         <th>Reg Date</th>
                        <th>Registered By</th>
                        <th>Grand Total</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($labPatients  as $patient)
                        <?php $grandTotal = 0?>
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$patient->patient_generated_id}}</td>
                            <td>{{ucfirst($patient->patient_name)}}</td>
                            <td>{{$patient->patient_fname}}</td>
                            <td>{{$patient->age}}</td>
                            <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>
                            <td>{{$patient->created_at}}</td>
                            <td>{{($patient->created_by != NULL) ? $patient->createdBy->name : 'Not Added'}}</td>
                            <td>
                            @foreach($patient->laboratoryTests as $labTest)
                                <?php $grandTotal += $labTest->price?>
                            @endforeach
                            {{$grandTotal}}
                            <td>
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
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {{$labPatients->links()}}
            </div>
        </div>
    </div>



    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Patient Laboratory Test <span id="sell_medicine_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="preview_modal_body">
                    <iframe  id="page_iframe" height="300" width="100%" frameBorder="0"></iframe>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script>

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
