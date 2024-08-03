@extends('layouts.master')

@section('page_title')
    Pharmacy Percentage Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
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
@include('layouts.page_header_print', ['reportName' => 'Pharmacy Percentage Report', 'from' => 'Beginning', 'to' => 'Today'])
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
            @if(empty($salePercentage))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table">
                        <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Medicine Name</th>
                            <th>Invoice No#</th>
                            <th>Purchase QTY</th>
                            <th>Purchase Price</th>
                            <th>Total Purchase</th>
                            <th>Sale Price</th>
                            <th>Total Sale</th>
                            <th>Sell Percentage</th>
                            <th>Vendor</th>
                            <th>Created By</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalPurchaseQTY = 0; $totalPurchasePrice = 0; $grandTotalPurchasePrice = 0;$totalSellPrice = 0; $grandTotalSalePrice = 0;?>
                        @foreach($pharmacies  as $pharmacy)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$pharmacy->medicineName->medicine_name}}</td>
                                <td>{{$pharmacy->invoice_no}}</td>
                                 <td>{{$pharmacy->purchase_qty}}</td>
                                <td>{{$pharmacy->purchase_price}}</td>
                                <td>{{$pharmacy->purchase_price * $pharmacy->purchase_qty}}</td>
                                <td>{{$pharmacy->sale_price}}</td>
                                <td>{{$pharmacy->sale_price * $pharmacy->purchase_qty}}</td>
                                <td>{{$pharmacy->sale_percentage}}%</td>
                                <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                                <td>{{$pharmacy->user->name}}</td>
                                <?php
                                $totalPurchaseQTY += $pharmacy->purchase_qty;
                                $totalPurchasePrice += $pharmacy->purchase_price;
                                $grandTotalPurchasePrice += $pharmacy->purchase_price * $pharmacy->purchase_qty;
                                $totalSellPrice += $pharmacy->sale_price;
                                $grandTotalSalePrice += $pharmacy->sale_price * $pharmacy->purchase_qty;
                                ?>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <td class="font-weight-bold">Total:</td>
                            <td class="font-weight-bold">{{number_format($totalPurchaseQTY)}}</td>
                            <td class="font-weight-bold">{{number_format($totalPurchasePrice)}}</td>
                            <td class="font-weight-bold">{{number_format($grandTotalPurchasePrice)}}</td>
                            <td class="font-weight-bold">{{number_format($totalSellPrice)}}</td>
                            <td class="font-weight-bold">{{number_format($grandTotalSalePrice)}}</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Sell Percentage Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('pharmacy_percentage_report')}}" method="GET" enctype="multipart/form-data" id="medicineForm">
                        <div class="form-group">
                            <label class="label">Sell Percentage</label>
                            <select class="form-control selectpicker" data-live-search="true" name="percentage">
                                @foreach($pharmacySalePercentages as $percentage)
                                        <option value="{{$percentage->sale_percentage}}">{{$percentage->sale_percentage}}%</option>
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
@endsection
