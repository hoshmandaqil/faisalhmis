@extends('layouts.master')

@section('page_title')
    Patients Invoice
@endsection

@section('page-action')
@endsection

<style>
    @media print {
        body * {
            visibility: hidden;
        }
    
        #print-me, #print-me * {
            visibility: visible;
        }
    
        #print-me {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
            font-size: 11px;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
        }
    
        th, td {
            text-align: left;
            padding: 2px 4px;
            font-size: 11px;
            white-space: nowrap;
        }
    
        .title {
            font-size: 13px !important;
            font-weight: bold;
        }
    
        h5, p {
            margin: 2px 0;
            font-size: 11px;
        }
    
        .no-border {
            border: none !important;
        }
    }
    </style>
    

@section('content')
    <div id="print-preview">
        <div>
            <button class="btn btn-primary" onclick="printDiv('print-me')">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div id="print-me">
                    <div class="row gutters">
                        <div class="col-12 text-center">
                            <p class="title" style="font-size: 1.3rem">Ministry of Health</p>
                            <p class="title" style="font-size: 1.2rem">Bayazid Rokhan Hospital</p>
                            <p class="title" style="font-size: 1.1rem">Finance Department</p>
                            <p class="title" style="font-size: 1rem">Patient Invoice</p>
                        </div>
                    </div>
                    <div class="invoice-container">
                        <hr>
                        <div class="invoice-body col-12">
                            <div class="row gutters">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5><strong>Patient Name: </strong> {{ ucfirst($patient->patient_name) }}</h5>
                                        </div>
                                        <div>
                                            <h5><strong>Patient ID: </strong> {{ $patient->patient_generated_id }}</h5>
                                        </div>
                                        <div>
                                            <h5>Doctor: {{ $patient->doctor->name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-payment">
                                <div class="row gutters">
                                    <div class="col-lg-12 col-md-12 col-sm-12 order-last">
                                        <table class="table no-border m-0">
                                            <tbody>
                                                <tr>
                                                    <th style="color: #000;">Categories</th>
                                                    <th style="color: #000;">Original Price</th>
                                                    <th style="color: #000;">Discount</th>
                                                    <th style="color: #000;">Payable</th>
                                                </tr>
                                                <tr>
                                                    <td style="color: #000;">
                                                        <p>
                                                            OPD Fee<br>
                                                            IPD Fee<br>
                                                            Pharmacy Charges<br><br>
                                                            <b>Diagnose Fee</b><br>
                                                            @foreach ($patient->laboratoryTests as $labTest)
                                                                {{ $labTest->testName->dep_name }} <br>
                                                            @endforeach
                                                        </p>
                                                        <h5 class="text-danger" style="color: #000;"><strong>Grand
                                                                Total</strong></h5>
                                                    </td>
                                                    <?php
                                                    $totalIPD = 0;
                                                    $totalIPD_discount = 0;
                                                    $totalPharmacy = 0;
                                                    $totalLab = 0;
                                                    $totalLabDiscount = 0;
                                                    
                                                    if ($patient->ipd != null) {
                                                        $totalPrice = 0;
                                                        $totalDiscount = 0;
                                                        if ($patient->ipd->discharge_date != null) {
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
                                                    
                                                    if ($patient->pharmacyMedicines != null) {
                                                        foreach ($patient->pharmacyMedicines as $medicine) {
                                                            $totalPharmacy += $medicine->quantity * $medicine->unit_price;
                                                        }
                                                    }
                                                    ?>
                                                    <td style="color: #000;">
                                                        <p>
                                                            {{ number_format($patient->OPD_fee) }} AF<br>
                                                            {{ number_format($totalIPD + $totalIPD_discount) }} AF<br>
                                                            {{ number_format($totalPharmacy) }} AF<br><br><br>
                                                            @foreach ($patient->laboratoryTests as $labTest)
                                                                {{ number_format($labTest->price) }} <br>
                                                                <?php $totalLab += $labTest->price; ?>
                                                            @endforeach
                                                        </p>
                                                        <h5 class="text-danger">
                                                            <strong>{{ number_format($patient->OPD_fee + $totalIPD + $totalIPD_discount + $totalPharmacy + $totalLab) }}</strong>
                                                        </h5>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            0 AF<br>
                                                            {{ number_format($totalIPD_discount) }} AF<br>
                                                            0 AF<br><br><br>
                                                            @foreach ($patient->laboratoryTests as $labTest)
                                                                {{ number_format(($labTest->discount * $labTest->price) / 100) }}
                                                                <br>
                                                                <?php $totalLabDiscount += ($labTest->discount * $labTest->price) / 100; ?>
                                                            @endforeach
                                                        </p>
                                                        <h5 class="text-danger">
                                                            <strong>{{ number_format($totalIPD_discount + $totalLabDiscount) }}</strong>
                                                        </h5>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            {{ number_format($patient->OPD_fee) }} AF<br>
                                                            {{ number_format($totalIPD) }} AF<br>
                                                            {{ number_format($totalPharmacy) }} AF<br><br><br>
                                                            @foreach ($patient->laboratoryTests as $labTest)
                                                                {{ number_format($labTest->price - ($labTest->discount * $labTest->price) / 100) }}
                                                                <br>
                                                            @endforeach
                                                        </p>
                                                        <h5 class="text-danger">
                                                            <strong>{{ number_format($patient->OPD_fee + $totalIPD + $totalPharmacy + $totalLab - $totalLabDiscount) }}</strong>
                                                        </h5>
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
        </div>
    </div><!-- end #print-preview -->
@endsection
