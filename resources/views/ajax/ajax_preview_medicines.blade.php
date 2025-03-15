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
        }
    </style>
</head>

<body>
    <main>
        <div class="col-12">
            <div class="row">
                <div class="col-12" id="print-preview">
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
    </main>

    <div class="submit-section d-flex justify-content-end px-4">
        <button class="btn btn-dark btn-sm hidden-print d-print-none" type="button"
            onclick="printSpecificDiv('print-preview')">
            Print
            <i class="icon icon-print"></i>
        </button>
    </div>
    <br class="d-print-none">
</body>

</html>

    <script>
        function printSpecificDiv(divId) {
            var printContent = document.getElementById(divId).innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;

            window.print();

            document.body.innerHTML = originalContent;

            window.location.reload();
        }
    </script>