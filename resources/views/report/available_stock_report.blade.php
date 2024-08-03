@extends('layouts.master')

@section('page_title')
    Available Stock Report
@endsection

@section('page-action')
    <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>

@endsection
@section('styles')
@endsection

@section('on_print_page_header')
@include('layouts.page_header_print', ['reportName' => 'Avaialbe Stock Report', 'from' => 'Beginning', 'to' => 'Today'])
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
                            <th>Medicine Name</th>
                            <th>Purchase QTY</th>
                            <th>Total Purchase Price</th>
                            <th>Sale QTY</th>
                            <th>Total Sale Price</th>
                            <th>Available</th>
                            <th>Available Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $pageTotalPurchaseQTY = 0;
                            $pageTotalPurchasePrice = 0;
                            $pageTotalSaleQTY = 0;
                            $pageTotalSalePrice = 0;
                            $pageTotalAvailable = 0;
                            $pageTotalAvailablePrice = 0;
                        @endphp
                        
                        @foreach($display as $pharmacy)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$pharmacy->medicineName->medicine_name}}
                                    @if($pharmacy->returned == 1)
                                        <span class="badge badge-danger text-small">returned</span>
                                    @endif
                                    @if($pharmacy->expired == 1)
                                        <span class="badge badge-danger text-small">expired</span>
                                    @endif
                                </td>
                                <td>{{$pharmacy->totalPurchaseQTY}}</td>
                                <td>{{$pharmacy->totalPurchasePrice}}</td>
                                <td>{{$pharmacy->medicineName->patientPharmacyMedicines->sum('quantity')}}</td>
                                <td>
                                    <?php $totalSoldPrice = 0?>
                                    @foreach($pharmacy->medicineName->patientPharmacyMedicines as $soldMedicine)
                                       <?php $totalSoldPrice += $soldMedicine->quantity * $soldMedicine->unit_price?>
                                    @endforeach
                                    {{$totalSoldPrice}}
                                </td>
                                @php
                                    if($pharmacy->returned == 1 || $pharmacy->expired == 1){
                                        $available_qty = 0;
                                        $availablePrice =0;
                                    }
                                    else{
                                        $available_qty = $pharmacy->totalPurchaseQTY - $pharmacy->medicineName->patientPharmacyMedicines->sum('quantity');
                                        $availablePrice = ($pharmacy->totalPurchaseQTY - $pharmacy->medicineName->patientPharmacyMedicines->sum('quantity')) * $pharmacy->sale_price;
                                    }
                                @endphp
                                <td>{{$available_qty}}</td>
                                <td>{{$availablePrice}}</td>
                                <?php
                                    $pageTotalPurchaseQTY += $pharmacy->totalPurchaseQTY;
                                    $pageTotalPurchasePrice += $pharmacy->totalPurchasePrice;
                                    $pageTotalSaleQTY += $pharmacy->medicineName->patientPharmacyMedicines->sum('quantity');
                                    $pageTotalSalePrice += $totalSoldPrice;
                                    $pageTotalAvailable += $available_qty;
                                    $pageTotalAvailablePrice += $availablePrice;
                                ?>
                            </tr>
                        @endforeach
                            <tr>
                                <td colspan="2" class="font-weight-bold">Page Total: </td>
                                <td class="font-weight-bold">{{number_format($pageTotalPurchaseQTY)}}</td>
                                <td class="font-weight-bold">{{number_format($pageTotalPurchasePrice)}}</td>
                                <td class="font-weight-bold">{{number_format($pageTotalSaleQTY)}}</td>
                                <td class="font-weight-bold">{{number_format($pageTotalSalePrice)}}</td>
                                <td class="font-weight-bold">{{number_format($pageTotalAvailable)}}</td>
                                <td class="font-weight-bold">{{number_format($pageTotalAvailablePrice)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <div class="row gutters">
        <div class="col-md-12">
            
        </div>
    </div>
@endsection
@section('scripts')
@endsection
