<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Patient User</title>
    <!-- Bootstrap css -->
    <style media='screen,print'>
        <?php include public_path('assets/css/bootstrap.min.css'); ?>
    </style>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" media='all'>
    <style>
        @media print {
            .remark {
                display: block;
            }

            body {
                padding: 10px !important;
            }

            /* body::after {
                content: '';
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: url('{{ asset('assets/img/hospital_logo.jpeg') }}') no-repeat center;
                opacity: 0.03;
                z-index: 999999;
            } */
        }
    </style>
</head>

<body>
    {{-- <header>
        <div class="row">
            <div class="col-10 offset-1">
                <img src="{{ asset('assets/img/top_header.png') }}"
                    width="100%"
                    height=100%"
                    alt="">
            </div>
        </div>
    </header> --}}
    <main>
        {{-- <div class="col-10 offset-1"
            id="patient_details_header">
            <div class="row">
                <table class="table tab-content"
                    id="patient_details_table">
                    <td style="padding-right: 15px !important;"
                        width="40%">اسم مریض:<span> {{ $patient->patient_name }}</span></td>
                    <td width="20%">سن: <span> {{ $patient->age }}</span></td>
                    <td width="20%"> جنس:<span> {{ $patient->gender == 0 ? 'مرد' : 'زن' }}</span></td>
                    <td width="20%">تاریخ</td>
                </table>
            </div>
        </div> --}}

        <div class="col-12">
            <div class="row">
                {{-- <div class="col-3"
                    id="clinical_record">
                    <h1 class="brand-color">Clinical</h1>
                    <h1 class="brand-color clinical-right-text text-right">Record</h1>
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <p><b>Blood Pressure:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->blood_pressure }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Respiration Rate:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->respiration_rate }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Pulse Rate:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->pulse_rate }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Heart Rate:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->heart_rate }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Temperature:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->temperature }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Weight:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->weight }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Height:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->height }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Mental State:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->mental_state }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Medical History:</b></p>
                                </td>
                                <td>
                                    <p>{{ $patient->medical_history }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>VA < </b>
                                    </p>
                                </td>
                                <td>
                                    <p>{{ $patient->va_1 }} <strong>|</strong> {{ $patient->va_2 }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>IOP < </b>
                                    </p>
                                </td>
                                <td>
                                    <p>{{ $patient->iop_1 }} <strong>|</strong> {{ $patient->iop_2 }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>CHIEF COMPLAINT:</b>
                                    </p>
                                </td>
                                <td>
                                    <p>{{ $patient->chief_complaint }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>DX:</b>
                                    </p>
                                </td>
                                <td>
                                    <p>{{ $patient->dx }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <img src="{{ asset('assets/img/hospital_logo.jpeg') }}"
                        alt=""
                        width="100%">
                    <br>
                </div> --}}
                <div class="col-12">
                    <div>
                        <div class="table-responsive table-borderless table-light">
                            <table class="table">
                                <tr>
                                    <td class="text-center center" colspan="100%">
                                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt=""
                                            style="height: 80px" class="mb-4">
                                        <h3 class="font-weight-bold">Bayazid Rokhan Hospital</h3>
                                        <h4>Pharmacy</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>ID:</strong>
                                        <span>
                                            {{ $patient->patient_generated_id }}
                                        </span>
                                    </td>
                                    <td><strong>Patient Name:</strong>
                                        <span>
                                            {{ $patient->patient_name }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table class="table medicine_table table-bordered border-black"
                                style="font-size: 15px !important;">
                                <thead class="table-bordered border-black" style="font-weight:bold; ">
                                    <th>ITEM</th>
                                    <th>RATE</th>
                                    <th>QTY</th>
                                    <th>AMOUNT</th>
                                </thead>
                                <tbody class="table-bordered border-black">
                                    <?php $grandTotal = 0; ?>
                                    @foreach ($soldMedicines as $medicine)
                                        <tr>
                                            <td>{{ ucfirst($medicine->medicine->medicine_name) }}</td>
                                            <td>{{ $medicine->quantity }}</td>
                                            <td>{{ round($medicine->unit_price) }}</td>
                                            <td>{{ round($medicine->unit_price * $medicine->quantity) }}</td>
                                            <?php $grandTotal += $medicine->unit_price * $medicine->quantity; ?>
                                        </tr>
                                        {{-- @if ($medicine->getMedicineDetailFromDoctor($medicine->patient_id, $medicine->medicine_id) != null)
                                            <tr style="border-collapse: separate; border-spacing: 0px !important;">
                                                <td></td>
                                                <td style="text-align: center; padding: 0px !important; font-size: 17px"
                                                    colspan="3"><span>
                                                        {{ $medicine->getMedicineDetailFromDoctor($medicine->patient_id, $medicine->medicine_id) }}
                                                    </span></td>
                                            </tr>
                                        @endif --}}
                                    @endforeach
                                    <tr>
                                        <td style="border-top: 1px solid lightgray; font-weight:bold;" colspan="100%">
                                            <b>Total: {{ round($grandTotal) }} AFN</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="px-2 d-flex justify-content-between">
                                <p style="font-size:14px"><strong>By:</strong> {{ $patient->createdBy->name }}</p>
                                <p style="font-size:14px"><strong>Address:</strong> Karte-Naw, Nanwayi Station</p>
                            </div>
                            <div class="px-2 d-flex justify-content-between">
                                <p style="font-size:14px"><strong>Date:</strong> {{ $patient->created_at }}</p>
                                <p style="font-size:14px"><strong>Phone No:</strong>078 700 04 44</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{--  --}}

    </main>
    {{-- <hr> --}}
    <div class="submit-section d-flex justify-content-end px-4">
        <button class="btn btn-dark btn-sm hidden-print d-print-none" type="button" onclick="window.print();">
            Print
            <i class="icon icon-print"></i>
        </button>
    </div>
    <br class="d-print-none">
</body>

</html>
