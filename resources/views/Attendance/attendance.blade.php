@extends('layouts.master')

@section('page_title')
    Attendance
@endsection

@section('page-action')
@endsection
@section('styles')
<!-- Bootstrap Select CSS -->
<link rel="stylesheet" href="{{asset('assets/vendor/bs-select/bs-select.css')}}" />
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

    @if(in_array('attendance_import', $user_permissions))
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Attendance Import:</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('attendance.store') }}" method="post" enctype="multipart/form-data">
                         {{ csrf_field() }}
                        <div class="row gutters">

                            <div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label>Choose Your File (*.xlsx)</label>
                                    <input type="file" class="form-control" id="file" name="select_file"
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    required >
                                </div>
                            </div>
                            <div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" name="type" id="type" required>
                                        <option value="1">Daily</option>
                                        <option value="2">Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-danger btn-sm mt-4">
                                        <i class="fa fa-users"></i> Import Now</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(in_array('attendance_report', $user_permissions))
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Generate Attendance Report:</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('attendance.report') }}" method="post" enctype="multipart/form-data">
                         {{ csrf_field() }}
                        <div class="row gutters">

                            <div class="col-xl-3 col-lglg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label>Select Staff</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="attendance_emps">
                                        <option value="all"> Select All</option>
                                        @foreach ($staffs as $staff)
                                        <option value="{{ $staff->attendance_id }}">{{ $staff->name }}</option>
                                    @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-xl-3 col-lglg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label>Select Year</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="year">
                                        @for ($i = 1400; $i < 1450; $i++)
                                        <option value={{ $i }}>{{ $i }}</option>
                                    @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lglg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label>Select Year</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="month">
                                        <option value="1">حمل</option>
                                        <option value="2">ثور</option>
                                        <option value="3">جوزا</option>
                                        <option value="4">سرطان</option>
                                        <option value="5">اسد</option>
                                        <option value="6">سنبله</option>
                                        <option value="7">میزان</option>
                                        <option value="8">عقرب</option>
                                        <option value="9">قوس</option>
                                        <option value="10">جدی</option>
                                        <option value="11">دلو</option>
                                        <option value="12">حوت</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lglg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm mt-4">
                                        <i class="fa fa-eye"></i> View Report</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@section('scripts')
<script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>
@endsection
