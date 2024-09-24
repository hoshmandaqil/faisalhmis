@extends('layouts.master')

@section('page_title')
    Import Employee Attendance
@endsection

@section('content')
    <div>
        @if (in_array('View Employee Attendance', $user_permissions))
            <div class="card mb-5">
                <div class="card-header">
                    <h5 class="card-title">Upload Attendance Excel File</h5>
                </div>
                <div class="card-body">
                    @if (!$todayDateExists || in_array('Upload Employee Attendance All Days', $user_permissions))
                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session()->get('success') }}</div>
                        @endif
                        <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row align-items-center">
                                <div class="form-group col-md-3">
                                    <label for="file">Choose Excel File</label>
                                    <input type="file" class="form-control" name="file" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="date">Select Date</label>
                                    <input type="date" class="form-control" name="date">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Import Attendance</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <p>Today's employee attendance data is already imported.</p>
                    @endif
                </div>
            </div>


            <div class="mt-8">
                <form action="{{ route('attendance.index') }}" method="GET">

                    @csrf
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Employees</label>
                                <select class="form-control" name="employee_name" data-control="select2"
                                    data-placeholder="Select an employee" data-allow-clear="true">
                                    <option value="">All Employees</option>
                                    @foreach ($employeeNames as $key => $employeeName)
                                        <option value="{{ $employeeName }}"
                                            {{ request('employee_name') == $employeeName ? 'selected' : '' }}>
                                            {{ $employeeName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>From Date
                                    <x-general.required />
                                </label>
                                <input class="form-control" name="from" type="date"
                                    value="{{ request('from') ? request('from') : $todayShamsiDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date
                                    <x-general.required />
                                </label>
                                <input class="form-control" name="to" type="date"
                                    value="{{ request('to') ? request('to') : $todayShamsiDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-lg btn-primary" type="submit">
                                <span class="indicator-label">View Report</span>
                            </button>
                            <a href="{{ route('attendance.index') }}" class="btn btn-lg btn-secondary ml-4" type="button">
                                <span class="indicator-label">Reset</span>
                            </a>
                        </div>
                    </div>

                </form>
            </div>
            {{-- <div class="row d-print-none mt-8 align-items-center">
                <h4 class="col-6">Attendance Records</h4>
                <div class="col-6 mx-auto text-end">
                    <button class="btn btn-lg btn-primary print" type="submit"
                        @click="$store.global.printElement($refs.employeeAttendancePrint)" x-cloak>Print</button>
                </div>
            </div> --}}
            <div class="mt-8 d-print-block" x-ref="employeeAttendancePrint">

                @forelse($attendanceRecords as $employeeName => $records)
                    <div class="mb-4">
                        <div class="text-center mb-2">
                            <h4>{{ $employeeName }}</h4>
                        </div>
                        <div class="row gx-1 g-1">
                            @foreach ($allDates as $date)
                                @php
                                    $record = $records->firstWhere('date', $date->format('Y-m-d'));
                                    $clockIn =
                                        $record && $record->clock_in
                                            ? \Carbon\Carbon::parse($record->clock_in)->format('H:i')
                                            : 'N/A';
                                    $clockOut =
                                        $record && $record->clock_out
                                            ? \Carbon\Carbon::parse($record->clock_out)->format('H:i')
                                            : 'N/A';
                                    $isLate =
                                        $record &&
                                        $record->clock_in &&
                                        $record->on_duty &&
                                        \Carbon\Carbon::parse($record->clock_in)->gt(
                                            \Carbon\Carbon::parse($record->on_duty),
                                        );

                                    $color = '#FFF';
                                    if ($record && $record->absent && !$record->comment) {
                                        $color = '#fca5a5';
                                    } elseif ($isLate && $record && !$record->absent) {
                                        $color = '#bfdbfe';
                                    } elseif ($record && $record->comment) {
                                        $color = '#86efac';
                                    }
                                @endphp
                                <div class="col-2">
                                    <div class="card attendance-card">
                                        <div class="card-header d-flex justify-content-between align-items-center py-1 px-2"
                                            style="min-height: 5px; {{ 'background: ' . $color }}">
                                            <h5 class="card-title print-text-xs mb-1" style="font-size: 12px">
                                                {{ $date->format('Y-m-d') }} -
                                                ({{ $date->format('M d') }})
                                            </h5>
                                            <h5 class="card-title print-text-xs mb-1" style="font-size: 12px">
                                                {{ $date->format('D') }}</h5>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-between px-2 py-2">
                                            <p class="mb-0 print-text-xs" style="font-weight: 500">
                                                <i class="bi bi-box-arrow-in-right text-muted print-text-xs"
                                                    style="font-size: 1.2rem; padding-top: 10px"></i>
                                                &nbsp;
                                                <span>{{ $clockIn ?? 'N/A' }}</span>
                                            </p>
                                            @if ($record && in_array('Edit Employee Attendance', $user_permissions))
                                                )
                                                <div class="d-none btn--edit">
                                                    <a class="menu-link " href="#"
                                                        x-on:click="$store.attendance.edit({{ $record }})">
                                                        <i class="bi bi-pencil-square text-primary"
                                                            style="font-size: 1.2rem; padding-top: 10px"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            <p class="mb-0 print-text-xs" style="font-weight: 500">
                                                <i class="bi bi-box-arrow-right text-muted print-text-xs"
                                                    style="font-size: 1.2rem; padding-top: 10px"></i>
                                                &nbsp;
                                                <span>{{ $clockOut ?? 'N/A' }}</span>
                                            </p>
                                        </div>


                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <x-general.no-record :data="$attendanceRecords" />
                @endforelse
            </div>

        @endif
    </div>
@endsection
