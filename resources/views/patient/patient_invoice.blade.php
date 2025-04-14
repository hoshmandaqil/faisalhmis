@extends('layouts.master')

@section('page_title')
    Patients Invoice
@endsection

@section('styles')
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .text-c {
            text-align: center !important;
        }

        .size-large {
            font-size: 25px !important;
        }

        .size-medium {
            font-size: 18px !important;
        }

        .display-flex {
            display: flex;
        }

        .flex-item-1 {
            flex: 1;
        }

        .invoice-table {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100% !important;
            margin-top: 20px;
        }

        .invoice-table td,
        .invoice-table th {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-bold {
            font-weight: bold !important;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        /* Hide the print button when printing */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-it,
            #print-it * {
                visibility: visible;
            }

            #print-it {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            button {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="">
        <div id="print-it" class="card">
            <div class="card-body">
                <div class="text-c">
                    <div class="size-large text-c">Ministry of Health</div>
                    <div class="size-large text-c">Bayazid Rokhan Hospital</div>
                    <div class="size-large text-c">Finance Department</div>
                    <div class="size-large text-c">Patient Invoice</div>
                </div>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- OPD Fee --}}
                        <tr>
                            <td>OPD Fee</td>
                            <td>{{ number_format($patient->OPD_fee) }} AF</td>
                            <td>0 AF</td>
                            <td>{{ number_format($patient->OPD_fee) }} AF</td>
                        </tr>

                        {{-- IPD Fee --}}
                        @php
                            $totalIPD = 0;
                            $totalIPD_discount = 0;
                            if ($patient->ipd != null && $patient->ipd->discharge_date != null) {
                                $register_date = \Carbon\Carbon::parse(
                                    date('Y-m-d', strtotime($patient->ipd->created_at)),
                                );
                                $discharge_date = $patient->ipd->discharge_date;
                                $ipdDays = $register_date->diffInDays($discharge_date);
                                for ($i = 1; $i <= $ipdDays; $i++) {
                                    $price = $patient->ipd->price;
                                    $discount = ($patient->ipd->discount * $price) / 100;
                                    $totalIPD += $price - $discount;
                                    $totalIPD_discount += $discount;
                                }
                            }
                        @endphp
                        <tr>
                            <td>IPD Fee</td>
                            <td>{{ number_format($totalIPD + $totalIPD_discount) }} AF</td>
                            <td>{{ number_format($totalIPD_discount) }} AF</td>
                            <td>{{ number_format($totalIPD) }} AF</td>
                        </tr>

                        {{-- Pharmacy Charges --}}
                        @php $totalPharmacy = 0; @endphp
                        @foreach ($patient->pharmacyMedicines as $medicine)
                            @php $totalPharmacy += $medicine->quantity * $medicine->unit_price; @endphp
                        @endforeach
                        <tr>
                            <td>Pharmacy Charges</td>
                            <td>{{ number_format($totalPharmacy) }} AF</td>
                            <td>0 AF</td>
                            <td>{{ number_format($totalPharmacy) }} AF</td>
                        </tr>

                        {{-- Laboratory Tests --}}
                        @php $totalLab = 0; @endphp
                        @foreach ($patient->laboratoryTests as $labTest)
                            @php $totalLab += $labTest->price; @endphp
                            <tr>
                                <td>{{ $labTest->testName->dep_name }}</td>
                                <td>{{ number_format($labTest->price) }} AF</td>
                                <td>0 AF</td>
                                <td>{{ number_format($labTest->price) }} AF</td>
                            </tr>
                        @endforeach

                        {{-- Grand Total --}}
                        @php
                            $grandTotal = $patient->OPD_fee + $totalIPD + $totalPharmacy + $totalLab;
                        @endphp
                        <tr>
                            <td class="text-bold">Grand Total</td>
                            <td class="text-bold">{{ number_format($grandTotal) }} AF</td>
                            <td class="text-bold">0 AF</td>
                            <td class="text-bold">{{ number_format($grandTotal) }} AF</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn btn-primary" onclick="printDiv('print-it')">
                    <i class="fa">🖨️</i> Print
                </button>
            </div>
        </div>
    </div>
@endsection
