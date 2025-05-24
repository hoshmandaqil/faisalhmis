<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Invoice</title>
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
</head>

<body>
    <div id="print-it">
        <div class="text-c">
            <div class="size-large">Ministry of Health</div>
            <div class="size-large">Bayazid Rokhan Hospital</div>
            <div class="size-large">Finance Department</div>
            <div class="size-large">Patient Invoice</div>
        </div>

        <div class="invoice-container">
            <div>
                <div class="display-flex">
                    <div class="flex-item-1">
                        <h5 class="text-c"><strong>Patient Name: </strong> {{ ucfirst($patient->patient_name) }}</h5>
                        <h5 class="text-c"><strong>Patient ID: </strong>{{ $patient->patient_generated_id }}</h5>
                    </div>
                    <div class="flex-item-1">
                        <h5 class="text-c">Doctor: {{ $patient->doctor->name }}</h5>
                        <h5 class="text-c">Date: {{ $currentDate }}</h5>
                    </div>
                </div>
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th class="text-c">Categories</th>
                            <th class="text-c">Original Price</th>
                            <th class="text-c">Discount</th>
                            <th class="text-c">Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- OPD Row --}}
                        <tr>
                            <td class="text-c">OPD Fee</td>
                            <td class="text-c">{{ number_format($patient->OPD_fee) }} AF</td>
                            <td class="text-c">0 AF</td>
                            <td class="text-c">{{ number_format($patient->OPD_fee) }} AF</td>
                        </tr>

                        {{-- IPD Row --}}
                        @php
                            use Carbon\Carbon;
                            $totalIPD = 0;
                            $totalIPD_discount = 0;

                            if ($patient->ipd != null) {
                                $register_date = Carbon::parse($patient->reg_date)->startOfDay();

                                $end_date = $patient->ipd->discharge_date
                                    ? Carbon::parse($patient->ipd->discharge_date)->startOfDay()
                                    : Carbon::now()->startOfDay(); // If not discharged, use today's date

                                $total_days = $register_date->diffInDays($end_date) + 1;

                                // Subtract 1 day if the patient is discharged
                                if ($patient->ipd->discharge_date) {
                                    $total_days -= 1;
                                }

                                $daily_price = (float) $patient->ipd->price;
                                $discount_percent = (float) $patient->ipd->discount;

                                for ($i = 0; $i < $total_days; $i++) {
                                    $date = $register_date->copy()->addDays($i)->format('Y-m-d');
                                    $discount = ($discount_percent * $daily_price) / 100;
                                    $final_price = $daily_price - $discount;

                                    $totalIPD += $final_price;
                                    $totalIPD_discount += $discount;
                        @endphp

                        <tr>
                            <td class="text-c">IPD Fee ({{ $date }})</td>
                            <td class="text-c">{{ number_format($daily_price) }} AF</td>
                            <td class="text-c">{{ number_format($discount) }} AF</td>
                            <td class="text-c">{{ number_format($final_price) }} AF</td>
                        </tr>

                        @php
                                }
                            }
                        @endphp

                        <!-- Summary Row -->
                        <tr>
                            <td class="text-c font-bold">Total IPD Fee</td>
                            <td class="text-c">{{ number_format($totalIPD + $totalIPD_discount) }} AF</td>
                            <td class="text-c">{{ number_format($totalIPD_discount) }} AF</td>
                            <td class="text-c">{{ number_format($totalIPD) }} AF</td>
                        </tr>

                        {{-- Pharmacy Row --}}
                        @php $totalPharmacy = 0; @endphp
                        @foreach ($patient->pharmacyMedicines as $medicine)
                            @php $totalPharmacy += $medicine->quantity * $medicine->unit_price; @endphp
                        @endforeach
                        <tr>
                            <td class="text-c">Pharmacy Charges</td>
                            <td class="text-c">{{ number_format($totalPharmacy) }} AF</td>
                            <td class="text-c">0 AF</td>
                            <td class="text-c">{{ number_format($totalPharmacy) }} AF</td>
                        </tr>

                        {{-- Lab Test Rows --}}
                        @php
                            $totalLab = 0;
                        @endphp
                        @foreach ($patient->laboratoryTests as $labTest)
                            @php $totalLab += $labTest->price; @endphp
                            <tr>
                                <td class="text-c">{{ $labTest->testName->dep_name }}</td>
                                <td class="text-c">{{ number_format($labTest->price) }} AF</td>
                                <td class="text-c">0 AF</td>
                                <td class="text-c">{{ number_format($labTest->price) }} AF</td>
                            </tr>
                        @endforeach

                        {{-- Grand Total --}}
                        @php
                            $grandTotal = $patient->OPD_fee + $totalIPD + $totalPharmacy + $totalLab;
                        @endphp
                        <tr>
                            <td class="size-medium text-bold text-c">Grand Total</td>
                            <td class="size-medium text-bold text-c">{{ number_format($grandTotal) }} AF</td>
                            <td class="size-medium text-bold text-c">0 AF</td>
                            <td class="size-medium text-bold text-c">{{ number_format($grandTotal) }} AF</td>
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

    <script>
        function printDiv(divId) {
            window.print();
        }
    </script>
</body>

</html>
