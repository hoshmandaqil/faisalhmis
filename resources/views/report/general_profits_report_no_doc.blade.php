@extends('layouts.master')

@section('page_title')
    General Profits Report
@endsection

@section('page-action')
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#exampleModal">
        Create Report
    </button>
    <button class="btn btn-sm btn-dark " onclick="window.print()">Print</button>
@endsection
@section('styles')
    <style>
        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        .lab_tests_table tr td {
            border: none !important;
        }

        .lab_tests_td {
            width: 30%;
        }

        .lab_tests_tr {
            display: inline-block;
            height: 30px;
        }

        .patient_number {
            font-size: 11px
        }
    </style>
    <!-- Pricing css -->
    <link rel="stylesheet" href="{{ asset('assets/css/pricing.css') }}">
@endsection

@section('on_print_page_header')
    @include('layouts.page_header_print', [
        'reportName' => 'General Incomes Report',
        'from' => $from,
        'to' => $to,
    ])
@endsection
@section('content')

    <!-- Row start -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            @if (empty($from))
                <div class="alert alert-danger">Please Create report!</div>
            @else
                <div class="table-responsive">
                    <table id="scrollVertical" class="table">
                        <thead>
                            <tr>
                                <th>Dates</th>
                                @foreach ($days as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalIncomesPerDay = [];
                            $grandTotalOfEach = 0; ?>
                            @foreach ($data as $key => $reportData)
                                @if ($key != 'Total Patients Per Day')
                                    <?php $totalOfEach = 0; ?>
                                    <tr>
                                        <td>{{ $key }}</td>
                                        @foreach ($reportData as $key1 => $dayData)
                                            <td>{{ $dayData != null ? number_format($dayData) : 0 }}
                                                @if ($key != 'Patients' && $key != 'OPD Incomes' && $key != 'Other Incomes')
                                                    <span
                                                        class="patient_number">({{ array_sum($data['Total Patients Per Day'][$key][$key1]) }})</span>
                                                @endif
                                            </td>
                                            <?php
                                            $totalOfEach += $dayData;
                                            
                                            if ($key != 'Patients') {
                                                $totalIncomesPerDay[$key1][] = $dayData;
                                            }
                                            ?>
                                        @endforeach
                                        <td>{{ number_format($totalOfEach) }}</td>
                                        <?php
                                        if ($key != 'Patients') {
                                            $grandTotalOfEach += $totalOfEach;
                                        }
                                        ?>

                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td>Total (Patients Not Calculated): </td>
                                @foreach ($totalIncomesPerDay as $perDayIncome)
                                    <td> {{ number_format(array_sum($perDayIncome)) }}</td>
                                @endforeach
                                <td>{{ number_format($grandTotalOfEach) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="row ml-2">
                    <div class="col-md-3">
                        <h4>Total Income: {{ number_format($grandTotalOfEach) }}</h4>
                    </div>
                    <div class="col-md-3">
                        <h4>Total Expenses: {{ number_format($kblTotalExpense) }}</h4>
                    </div>

                    <div class="col-md-3">
                        <h4>Balance: {{ number_format($grandTotalOfEach - $kblTotalExpense) }}</h4>
                    </div>

                    <div class="col-md-3">
                        <h4>Available Cash: {{ number_format($allIncomes - $allExpenses) }}</h4>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Date Wise General income Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('general_profits_report_no_doc') }}" method="GET" enctype="multipart/form-data"
                        id="medicineForm">
                        <div class="form-group">
                            <label class="label">From:</label>
                            <input class="form-control" type="date" name="from"
                                value="{{ $from != null ? $from : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">To:</label>
                            <input class="form-control" type="date" name="to"
                                value="{{ $to != null ? $to : date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="label">Registerd By:</label>
                            <select class="form-control" name="registered_by">
                                <option value="0" {{ $registered_by == 0 ? 'selected' : '' }}>All</option>
                                @foreach ($patientsRegisteredBy as $key => $registeredBy)
                                    <option value="{{ $key }}" {{ $registered_by == $key ? 'selected' : '' }}>
                                        {{ $registeredBy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Doctor:</label>
                            <select class="form-control" name="doctor_id">
                                <option value="0" {{ $doctor_id == 0 ? 'selected' : '' }}>All</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ $doctor_id == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }}</option>
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
    </div>


@endsection
@section('scripts')
@endsection
