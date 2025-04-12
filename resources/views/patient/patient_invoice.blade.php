@extends('layouts.master')

@section('page_title')
    Patients Invoice
@endsection

@section('page-action')
@endsection

@section('styles')
<style>
    @media print {
        .d-print-none {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-container {
            padding: 10px;
            font-size: 14px;
        }
    }
</style>
@endsection

@section('content')

<div class="submit-section d-flex justify-content-end px-4">
    <button class="btn btn-dark btn-sm d-print-none" type="button"
        onclick="printSpecificDiv('print-preview')">
        Print
        <i class="icon icon-print"></i>
    </button>
</div>
<br class="d-print-none">

<div id="print-preview">
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row gutters">
                <div class="col-6 offset-3 text-center">
                    <p class="title" style="font-size: 1.3rem">Ministry of Health</p>
                    <p class="title" style="font-size: 1.2rem">Bayazid Rokhan Hospital</p>
                    <p class="title" style="font-size: 1.1rem">Finance Department</p>
                    <p class="title" style="font-size: 1rem">Patient Invoice</p>
                </div>
            </div>

            <div class="invoice-container">
                <hr>
                <div class="invoice-body col-12 offset-3">

                    <div class="row gutters">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row gutters">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="row ml-2">
                                        <h6>Patient Name:</h6>
                                        <h5 class="ml-1">{{ ucfirst($patient->patient_name) }}</h5>
                                    </div>
                                    <div class="row ml-2">
                                        <h6>Patient ID:</h6>
                                        <h5 class="ml-1">{{ $patient->patient_generated_id }}</h5>
                                    </div>
                                    <div class="row ml-2">
                                        <h6>Doctor:</h6>
                                        <h5 class="ml-1">{{ $patient->doctor->name }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-payment">
                        <div class="row gutters">
                            <div class="col-lg-6 col-md-6 col-sm-12 order-last">
                                <table class="table no-border m-0">
                                    <tbody>
                                        <tr>
                                            <th></th>
                                            <th>Original Price</th>
                                            <th>Discount</th>
                                            <th>Payable</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>
                                                    OPD Fee<br>
                                                    IPD Fee<br>
                                                    Pharmacy Charges<br><br>
                                                    <b>Diagnose Fee</b><br>
                                                    @foreach ($patient->laboratoryTests as $labTest)
                                                        {{ $labTest->testName->dep_name }} <br>
                                                    @endforeach
                                                </p>
                                                <h5 class="text-danger"><strong>Grand Total</strong></h5>
                                            </td>
                                            @php
                                                $totalIPD = 0;
                                                $totalIPD_discount = 0;
                                                $totalPharmacy = 0;
                                                $totalLab = 0;
                                                $totalLabDiscount = 0;

                                                if ($patient->ipd != NULL) {
                                                    $totalPrice = 0;
                                                    $totalDiscount = 0;
                                                    if ($patient->ipd->discharge_date != NULL) {
                                                        $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($patient->ipd->created_at)));
                                                        $discharge_date = $patient->ipd->discharge_date;
                                                        $ipdDays = $register_date->diffInDays($discharge_date);

                                                        for ($i = 1; $i <= $ipdDays; $i++) {
                                                            $totalPrice += $patient->ipd->price;
                                                            $discountForTest = ($patient->ipd->discount * $patient->ipd->price) / 100;
                                                            $totalDiscount += $discountForTest;
                                                        }
                                                    }
                                                    $totalIPD += $totalPrice - $totalDiscount;
                                                    $totalIPD_discount += $totalDiscount;
                                                }

                                                if ($patient->pharmacyMedicines != NULL) {
                                                    foreach ($patient->pharmacyMedicines as $medicine) {
                                                        $totalPharmacy += $medicine->quantity * $medicine->unit_price;
                                                    }
                                                }
                                            @endphp
                                            <td>
                                                <p>
                                                    {{ number_format($patient->OPD_fee) }} AF<br>
                                                    {{ number_format($totalIPD + $totalIPD_discount) }} AF<br>
                                                    {{ number_format($totalPharmacy) }} AF<br><br><br>
                                                    @foreach ($patient->laboratoryTests as $labTest)
                                                        {{ number_format($labTest->price) }} <br>
                                                        @php $totalLab += $labTest->price; @endphp
                                                    @endforeach
                                                </p>
                                                <h5 class="text-danger"><strong>{{ number_format($patient->OPD_fee + $totalIPD + $totalIPD_discount + $totalPharmacy + $totalLab) }}</strong></h5>
                                            </td>
                                            <td>
                                                <p>
                                                    0 AF<br>
                                                    {{ number_format($totalIPD_discount) }} AF<br>
                                                    0 AF<br><br><br>
                                                    @foreach ($patient->laboratoryTests as $labTest)
                                                        {{ number_format(($labTest->discount * $labTest->price) / 100) }} <br>
                                                        @php $totalLabDiscount += ($labTest->discount * $labTest->price / 100); @endphp
                                                    @endforeach
                                                </p>
                                                <h5 class="text-danger"><strong>{{ number_format($totalIPD_discount + $totalLabDiscount) }}</strong></h5>
                                            </td>
                                            <td>
                                                <p>
                                                    {{ number_format($patient->OPD_fee) }} AF<br>
                                                    {{ number_format($totalIPD) }} AF<br>
                                                    {{ number_format($totalPharmacy) }} AF<br><br><br>
                                                    @foreach ($patient->laboratoryTests as $labTest)
                                                        {{ number_format($labTest->price - ($labTest->discount * $labTest->price) / 100) }} <br>
                                                    @endforeach
                                                </p>
                                                <h5 class="text-danger"><strong>{{ number_format($patient->OPD_fee + $totalIPD + $totalPharmacy + $totalLab - $totalLabDiscount) }}</strong></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- invoice-payment -->
                </div><!-- invoice-body -->
            </div><!-- invoice-container -->
        </div>
    </div>
</div><!-- end #print-preview -->

@endsection

@section('scripts')
<script>
    function printSpecificDiv(divId) {
        const printContents = document.getElementById(divId).cloneNode(true);
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        printWindow.document.open();
        printWindow.document.write(`
            <html>
            <head>
                <title>Print</title>
                <link rel="stylesheet" href="{{ asset('css/app.css') }}">
                <style>
                    @media print {
                        body {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                    }
                </style>
            </head>
            <body onload="window.print(); window.onafterprint = function() { window.close(); }">
                ${printContents.outerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endsection
