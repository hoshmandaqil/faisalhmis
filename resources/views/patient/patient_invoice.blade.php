@extends('layouts.master')

@section('page_title')
    Patients Invoice
@endsection

@section('page-action')
@endsection

@section('content')
    <div id="print-preview">
        <div>
            <button class="btn btn-primary" onclick="printDiv('print-me')">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div>
                    <div class="row gutters">
                        <div class="col-12 text-center">
                            <p class="title">Ministry of Health</p>
                            <p class="title">Bayazid Rokhan Hospital</p>
                            <p class="title">Finance Department</p>
                            <p class="title">Patient Invoice</p>
                        </div>
                    </div>
                    <div id="print-me">
                        <div class="invoice-container">
                            <hr>
                            <div class="invoice-body col-12">
                                <div class="row gutters">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5><strong>Patient Name: </strong> {{ ucfirst($patient->patient_name) }}
                                                </h5>
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
                                                        <th>Categories</th>
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

                                                            if ($patient->ipd != null) {
                                                                $totalPrice = 0;
                                                                $totalDiscount = 0;
                                                                if ($patient->ipd->discharge_date != null) {
                                                                    $register_date = \Carbon\Carbon::parse(
                                                                        date(
                                                                            'Y-m-d',
                                                                            strtotime($patient->ipd->created_at),
                                                                        ),
                                                                    );
                                                                    $discharge_date = $patient->ipd->discharge_date;
                                                                    $ipdDays = $register_date->diffInDays(
                                                                        $discharge_date,
                                                                    );

                                                                    for ($i = 1; $i <= $ipdDays; $i++) {
                                                                        $totalPrice += $patient->ipd->price;
                                                                        $discountForTest =
                                                                            ($patient->ipd->discount *
                                                                                $patient->ipd->price) /
                                                                            100;
                                                                        $totalDiscount += $discountForTest;
                                                                    }
                                                                }
                                                                $totalIPD += $totalPrice - $totalDiscount;
                                                                $totalIPD_discount += $totalDiscount;
                                                            }

                                                            if ($patient->pharmacyMedicines != null) {
                                                                foreach ($patient->pharmacyMedicines as $medicine) {
                                                                    $totalPharmacy +=
                                                                        $medicine->quantity * $medicine->unit_price;
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
                                                                    @php $totalLabDiscount += ($labTest->discount * $labTest->price) / 100; @endphp
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
        </div>
    </div><!-- end #print-preview -->
@endsection

{{-- Print style --}}
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #print-me,
        #print-me * {
            visibility: visible;
        }

        #print-me {
            position: absolute;
            left: 0;
            top: 0;
            width: 72mm;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 1px 2px;
            font-size: 10px;
            white-space: nowrap;
        }

        .title {
            font-size: 11px !important;
            font-weight: bold;
        }

        h5,
        p {
            margin: 1px 0;
            font-size: 10px;
        }

        .no-border {
            border: none !important;
        }

        .invoice-body,
        .invoice-payment {
            padding: 0 !important;
            margin: 0 !important;
        }

        hr {
            margin: 3px 0;
        }
    }
</style>

{{-- Print script --}}
<script>
    function printDiv(divId) {
        window.print();
    }
</script>
