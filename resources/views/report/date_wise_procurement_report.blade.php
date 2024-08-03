@extends('layouts.master')

@section('page_title')
    Purchase Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm d-print-none" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
        <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

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

@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'Purchase Report', 'from' => $from, 'to' => $to])
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
            @if(empty($theads))
                <div class="alert alert-danger">Please Create report!</div>
                @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table">
                        <thead>
                        <tr>
                            <th>S.NO</th>
                            @foreach($theads as $thead)
                            <th>{{$thead}}</th>
                                @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalPurchasePrice = 0; $totalQuantity = 0; $totalSellPrice = 0; $grandTotalPurchase = 0; $grandTotalSale = 0;?>
                        @foreach($pharmacies  as $pharmacy)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                @if($type == "invoice_base")
                                    <td>{{$pharmacy->invoice_no}}</td>
                                    <td>{{$pharmacy->medicineName->medicine_name}}</td>
                                    <td>{{$pharmacy->purchase_price}}</td>
                                    <td>{{$pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->purchase_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->sale_price}}</td>
                                    <td>{{$pharmacy->sale_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                                    <td>{{$pharmacy->user->name}}</td>
                                @elseif ($type == "user_base")
                                    <td>{{$pharmacy->user->name}}</td>
                                    <td>{{$pharmacy->medicineName->medicine_name}}</td>
                                    <td>{{$pharmacy->purchase_price}}</td>
                                    <td>{{$pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->purchase_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->sale_price}}</td>
                                    <td>{{$pharmacy->sale_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                                    <td>{{$pharmacy->invoice_no}}</td>

                                @elseif ($type == "vendor_base")
                                    <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                                    <td>{{$pharmacy->medicineName->medicine_name}}</td>
                                    <td>{{$pharmacy->purchase_price}}</td>
                                    <td>{{$pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->purchase_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->sale_price}}</td>
                                    <td>{{$pharmacy->sale_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->invoice_no}}</td>
                                    <td>{{$pharmacy->user->name}}</td>
                                    @else
                                    <td>{{$pharmacy->medicineName->medicine_name}}</td>
                                    <td>{{$pharmacy->purchase_price}}</td>
                                    <td>{{$pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->purchase_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->sale_price}}</td>
                                    <td>{{$pharmacy->sale_price * $pharmacy->purchase_qty}}</td>
                                    <td>{{$pharmacy->invoice_no}}</td>
                                    <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                                    <td>{{$pharmacy->user->name}}</td>
                                @endif
                                <?php
                                $totalPurchasePrice += $pharmacy->purchase_price;
                                $totalQuantity += $pharmacy->purchase_qty;
                                $grandTotalPurchase += $pharmacy->purchase_qty * $pharmacy->purchase_price;
                                $totalSellPrice += $pharmacy->sale_price;
                                $grandTotalSale += $pharmacy->purchase_qty * $pharmacy->sale_price;
                                ?>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="font-weight-bold text-right" colspan="2">Total:</td>
                            <td>{{number_format($totalPurchasePrice)}}</td>
                            <td>{{number_format($totalQuantity)}}</td>
                            <td>{{number_format($grandTotalPurchase)}}</td>
                            <td>{{number_format($totalSellPrice)}}</td>
                            <td>{{number_format($grandTotalSale)}}</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Date Wise Report<span id="lab_patient_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('date_wise_procurement_report')}}" method="GET" enctype="multipart/form-data" id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from" value="{{($from != NULL) ? $from : date('Y-m-d')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to" value="{{($to != NULL) ? $to : date('Y-m-d')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Report Type:</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="general">General</option>
                                <option value="user_base">User Base</option>
                                <option value="invoice_base">Invoice Base</option>
                                <option value="vendor_base">Vendor Base</option>
                            </select>
                        </div>
                        <div class="form-group d-none" id="user_base">
                            <label class="label">User:</label>
                            <select class="form-control selectpicker" data-live-search="true" name="user_base">
                                @foreach($created_users as $user)
                                    @if($user['user'] != null)
                                <option value="{{$user['user']['id']}}">{{$user['user']['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group d-none"  id="invoice_base">
                            <label class="label">Invoice Number:</label>
                            <select class="form-control selectpicker" data-live-search="true" name="invoice_base">
                                @foreach($pharmacyInvoiceNumbers as $invoice)
                                    @if($user['user'] != null)
                                        <option value="{{$invoice['invoice_no']}}">{{$invoice['invoice_no']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group d-none" id="vendor_base">
                            <label class="label">Vendor:</label>
                            <select class="form-control selectpicker" data-live-search="true" name="vendor_base" >
                                @foreach($pharmacyVendors as $vendor)
                                    @if($vendor['supplier'] != null)
                                        <option value="{{$vendor['supplier']['id']}}">{{$vendor['supplier']['supplier_name']}}</option>
                                    @endif
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
    <!-- Bootstrap Select JS -->
    <script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>
    <script>
        $('#type').change(function () {
            var value = $(this).val();
            if (value == "user_base"){
                $('#vendor_base').addClass('d-none');
                $('#invoice_base').addClass('d-none');
                $('#user_base').removeClass('d-none');
            }
            else if (value == "vendor_base"){
                $('#user_base').addClass('d-none');
                $('#invoice_base').addClass('d-none');
                $('#vendor_base').removeClass('d-none');
            }
            else if (value == "invoice_base"){
                $('#user_base').addClass('d-none');
                $('#vendor_base').addClass('d-none');
                $('#invoice_base').removeClass('d-none');
            }
            else{
                $('#user_base').addClass('d-none');
                $('#vendor_base').addClass('d-none');
                $('#invoice_base').addClass('d-none');
            }
        });
    </script>
@endsection
