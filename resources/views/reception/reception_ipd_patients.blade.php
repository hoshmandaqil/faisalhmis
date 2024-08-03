@extends('layouts.master')

@section('page_title')
    IPD Patients
@endsection

@section('styles')
    <style>
        .ipd-total-row {
            border-bottom: 1px solid black;
        }
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
                    <form action="{{url('search_reception_ipd_patient')}}" method="post">
                        @csrf
                        <input type="text" name="search_patient" class="search-query"
                               value="{{(Request::is('search_reception_ipd_patient') ? $patientSearchDetail : '')}}" placeholder="Search Patient By Id, Name or Phone...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>

                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection
@section('page-action')
    @if(\Request::is('search_reception_ipd_patient'))
        <a type="button" class="btn btn-danger btn-sm" href="{{route('patient_ipd.index')}}">
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
                        <th>Phone Number</th>
                        <th>Marital Stauts</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                         <th>Registered By</th>
                        <th>Doctor</th>
                        <th>Admission Date</th>
                        <th>Discharge Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ipdPatients  as $patient)
                        <?php $totalPrice = 0; $totalDiscount = 0?>
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$patient->patient_generated_id}}</td>
                            <td>{{ucfirst($patient->patient_name)}}</td>
                            <td>{{$patient->patient_fname}}</td>
                            <td>{{$patient->patient_phone}}</td>
                            <td>@if ($patient->marital_status != NULL)
                                {{ ($patient->marital_status == 0) ? 'Single' : 'Married' }}
                                @else
                                --
                            @endif</td>
                            <td>{{$patient->age}}</td>
                            <td>{{$patient->blood_group}}</td>
                             <td>{{$patient->createdBy->name}}</td>
                            <td>{{($patient->doctor_id != NULL) ? $patient->doctor->name : 'Not Added'}}</td>
                            <td>{{date('Y-m-d', strtotime($patient->ipd->created_at))}}</td>
                            <td>{{($patient->ipd->discharge_date != NULL) ?  $patient->ipd->discharge_date : date('Y-m-d')}}</td>
                            <?php
                            $ipdDays = 1;
                            $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($patient->ipd->created_at)));
                            $to = \Carbon\Carbon::parse(date('Y-m-d'));
                            $ipdDays = $register_date->diffInDays($to);
                            ?>
                            <td>
                                <table class="ipd_table">
                                    @for($i= 1; $i <= $ipdDays; $i++)
                                        <tr class="ipd_tr">
                                            <td>  {{$i}} Day</td>
                                            <td>  {{$patient->ipd->price}}</td>
                                            <?php $totalPrice += $patient->ipd->price;
                                            $discountForTest = ($patient->ipd->discount * $patient->ipd->price)/100;
                                            $totalDiscount += $discountForTest;
                                            ?>
                                        </tr>
                                    @endfor
                                    <tr class="ipd-total-row">
                                        <td><b>Total: <br> Discount:</b></td>
                                        <td><b>{{$totalPrice}} <br> {{$totalDiscount}}</b></td>
                                    </tr>
                                        <tr>
                                            <td><b>Payable: </b></td>
                                            <td><b>{{$totalPrice - $totalDiscount}} AFN</b></td>
                                        </tr>

                                </table>
                            <td>
                                <a class="btn btn-danger btn-sm text-white" href="{{url('dischargePatient/'.$patient->ipd->id)}}"
                                   onclick="return confirm('Are you sure you want to discharge this patient?')"><b>Discharge</b></a>
                                @if(in_array('reception_IPD_edit', $user_permissions))
                                <a class="btn btn-warning btn-sm text-white"  href="#" data-toggle="modal" data-target="#editIPDModal" data-id="{{$patient->ipd->id}}"
                                   data-price ="{{$patient->ipd->price}}"    data-discount ="{{$patient->ipd->discount}}"
                                   data-admit-date ="{{date('Y-m-d', strtotime($patient->ipd->created_at))}}"
                                   data-discharge-date="{{($patient->ipd->discharge_date != NULL) ? $patient->ipd->discharge_date : ''}}">
                                    <i class="icon icon-edit"></i> Edit</a>
                                    @endif

                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {{$ipdPatients->links()}}
            </div>
        </div>
        <div class="modal fade" id="editIPDModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit IPD Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="editIPDForm" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" class="form-control" name="price"  required>
                            </div>
                            <div class="form-group">
                                <label>Discount (%)</label>
                                <input id="discount" type="number" class="form-control" name="discount" required>
                            </div>
                            <div class="form-group">
                                <label>Admitted Date</label>
                                <input type="date" class="form-control" name="admitted_date" required>
                            </div>

                            <div class="form-group">
                                <label>Discharge Date</label>
                                <input type="date" class="form-control" name="discharge_date">
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

    </div>
@endsection
@section('scripts')
    <script>
        $('#editIPDModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var price = button.data('price');
            var discount = button.data('discount');
            var admitted = button.data('admit-date');
            var discharge = button.data('discharge-date');
            var modal = $(this)

            // Set values in edit popup
            var action = '/patient_ipd/'+id;
            $("#editIPDForm").attr("action", action);
            modal.find('.modal-body [name="price"]').val(price);
            modal.find('.modal-body [name="discount"]').val(discount);
            modal.find('.modal-body [name="admitted_date"]').val(admitted);
            modal.find('.modal-body [name="discharge_date"]').val(discharge);
        })
    </script>
    @endsection
