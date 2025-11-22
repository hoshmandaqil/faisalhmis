<!DOCTYPE html>
<html lang="en">
@php use Carbon\Carbon; @endphp

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

        .section-header {
            background: #f0f0f0;
            font-weight: bold;
            text-align: left;
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
            <div class="size-large">Ministry of Public Health</div>
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
                        {{-- OPD Section --}}
                        <tr>
                            <td colspan="4" class="section-header">OPD Charges</td>
                        </tr>
                        @php
                            // Calculate OPD original price and discount
                            $opdOriginalPrice = $patient->OPD_fee;
                            $opdDiscount = 0;

                            // If patient has discount_type (student or staff), OPD was already reduced by 50%
                            // So we need to calculate the original price
                            if (in_array($patient->discount_type, ['student', 'staff'])) {
                                $opdOriginalPrice = $patient->OPD_fee * 2; // Original price before 50% discount
                                $opdDiscount = $patient->OPD_fee; // The discount amount
                            }

                            // If no_discount is 0, the patient gets no OPD fee at all
                            if ($patient->no_discount == 0 && !in_array($patient->discount_type, ['student', 'staff'])) {
                                $opdOriginalPrice = $patient->doctor->OPD_fee ?? 0;
                                $opdDiscount = $opdOriginalPrice;
                            }
                        @endphp
                        <tr>
                            <td class="text-c">OPD Fee</td>
                            <td class="text-c">{{ number_format($opdOriginalPrice) }} AF</td>
                            <td class="text-c">{{ number_format($opdDiscount) }} AF</td>
                            <td class="text-c">{{ number_format($patient->OPD_fee) }} AF</td>
                        </tr>

                        {{-- Always initialize IPD totals --}}
                        @php
                            $totalIPD = 0;
                            $totalIPD_discount = 0;
                        @endphp

                        {{-- IPD Section --}}
                        <tr>
                            <td colspan="4" class="section-header">IPD Charges</td>
                        </tr>
                        @php
                            $totalIPD = 0;
                            $totalIPD_discount = 0;
                        @endphp
                        @if ($patient->ipds->count() > 0)
                            @foreach ($patient->ipds->sortBy('created_at') as $ipd)
                                @php
                                    $register_date = Carbon::parse($ipd->created_at)->startOfDay();
                                    $end_date = $ipd->discharge_date
                                        ? Carbon::parse($ipd->discharge_date)->startOfDay()
                                        : Carbon::now()->startOfDay();
                                    $total_days = $register_date->diffInDays($end_date);
                                    $daily_price = (float) $ipd->price;
                                    $discount_percent = (float) $ipd->discount;
                                    $admission_total = 0;
                                    $admission_discount = 0;
                                @endphp
                                @if ($total_days > 0)
                                    <tr>
                                        <td colspan="4" style="font-weight:bold; background:#e0e0e0;">
                                            Admission: {{ $register_date->format('Y-m-d') }} to {{ $end_date->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                    @for ($i = 0; $i < $total_days; $i++)
                                        @php
                                            $date = $register_date->copy()->addDays($i)->format('Y-m-d');
                                            $discount = ($discount_percent * $daily_price) / 100;
                                            $final_price = $daily_price - $discount;
                                            $admission_total += $final_price;
                                            $admission_discount += $discount;
                                        @endphp
                                        <tr>
                                            <td class="text-c">IPD Fee ({{ $date }})</td>
                                            <td class="text-c">{{ number_format($daily_price) }} AF</td>
                                            <td class="text-c">{{ number_format($discount) }} AF</td>
                                            <td class="text-c">{{ number_format($final_price) }} AF</td>
                                        </tr>
                                    @endfor
                                    <tr>
                                        <td class="text-c font-bold">Subtotal for this Admission</td>
                                        <td class="text-c">{{ number_format($admission_total + $admission_discount) }} AF</td>
                                        <td class="text-c">{{ number_format($admission_discount) }} AF</td>
                                        <td class="text-c">{{ number_format($admission_total) }} AF</td>
                                    </tr>
                                    @php
                                        $totalIPD += $admission_total;
                                        $totalIPD_discount += $admission_discount;
                                    @endphp
                                @endif
                            @endforeach
                            <tr>
                                <td class="text-c font-bold">Total IPD Fee</td>
                                <td class="text-c">{{ number_format($totalIPD + $totalIPD_discount) }} AF</td>
                                <td class="text-c">{{ number_format($totalIPD_discount) }} AF</td>
                                <td class="text-c">{{ number_format($totalIPD) }} AF</td>
                            </tr>
                        @endif

                        {{-- Pharmacy Section --}}
                        <tr>
                            <td colspan="4" class="section-header">Pharmacy Charges</td>
                        </tr>
                        @php
                            $totalPharmacyOriginal = 0;
                            $totalPharmacyDiscount = 0;
                            $discountPercent = 0;

                            // Apply 50% discount for student or staff
                            if (in_array($patient->discount_type, ['student', 'staff'])) {
                                $discountPercent = 50;
                            }
                        @endphp
                        @foreach ($patient->pharmacyMedicines as $medicine)
                            @php
                                $totalPharmacyOriginal += $medicine->quantity * $medicine->unit_price;
                            @endphp
                        @endforeach
                        @php
                            $totalPharmacyDiscount = ($totalPharmacyOriginal * $discountPercent) / 100;
                            $totalPharmacy = $totalPharmacyOriginal - $totalPharmacyDiscount;
                        @endphp
                        <tr>
                            <td class="text-c">Pharmacy Charges</td>
                            <td class="text-c">{{ number_format($totalPharmacyOriginal) }} AF</td>
                            <td class="text-c">{{ number_format($totalPharmacyDiscount) }} AF</td>
                            <td class="text-c">{{ number_format($totalPharmacy) }} AF</td>
                        </tr>

                        {{-- Lab Section --}}
                        <tr>
                            <td colspan="4" class="section-header">Lab Charges</td>
                        </tr>
                        @php
                            $totalLabOriginal = 0;
                            $totalLabDiscount = 0;
                        @endphp
                        @foreach ($patient->laboratoryTests as $labTest)
                            @php
                                // The price field contains the price AFTER discount
                                // So we need to calculate the original price
                                $labTestDiscountPercent = (float) ($labTest->discount ?? 0);
                                $testPayable = $labTest->price; // This is already the discounted price

                                // Calculate original price: original = payable / (1 - discount%)
                                if ($labTestDiscountPercent > 0 && $labTestDiscountPercent < 100) {
                                    $testOriginalPrice = $testPayable / (1 - ($labTestDiscountPercent / 100));
                                } else if ($labTestDiscountPercent >= 100) {
                                    $testOriginalPrice = 0; // If 100% discount, original doesn't matter
                                } else {
                                    $testOriginalPrice = $testPayable; // No discount
                                }

                                $testDiscount = $testOriginalPrice - $testPayable;
                                $totalLabOriginal += $testOriginalPrice;
                                $totalLabDiscount += $testDiscount;
                            @endphp
                            <tr>
                                <td class="text-c">{{ $labTest->testName->dep_name }}</td>
                                <td class="text-c">{{ number_format($testOriginalPrice) }} AF</td>
                                <td class="text-c">{{ number_format($testDiscount) }} AF ({{ $labTestDiscountPercent }}%)</td>
                                <td class="text-c">{{ number_format($testPayable) }} AF</td>
                            </tr>
                        @endforeach
                        @php
                            $totalLab = $totalLabOriginal - $totalLabDiscount;
                        @endphp

                        {{-- Grand Total Section --}}
                        <tr>
                            <td colspan="4" class="section-header">Grand Total</td>
                        </tr>
                        @php
                            $grandTotalOriginal = $opdOriginalPrice + ($totalIPD + $totalIPD_discount) + $totalPharmacyOriginal + $totalLabOriginal;
                            $grandTotalDiscount = $opdDiscount + $totalIPD_discount + $totalPharmacyDiscount + $totalLabDiscount;
                            $grandTotal = $patient->OPD_fee + $totalIPD + $totalPharmacy + $totalLab;
                        @endphp
                        <tr>
                            <td class="size-medium text-bold text-c">Grand Total</td>
                            <td class="size-medium text-bold text-c">{{ number_format($grandTotalOriginal) }} AF</td>
                            <td class="size-medium text-bold text-c">{{ number_format($grandTotalDiscount) }} AF</td>
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
