@extends('layouts.master')

@section('page_title')
    Laboratory Tests
@endsection

@section('page-action')
@endsection
@section('styles')
    <!-- Data Tables -->
    <link href="{{ asset('assets/vendor/datatables/dataTables.bs4.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('assets/vendor/datatables/dataTables.bs4-custom.css') }}"
        rel="stylesheet" />
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                <div class="search-box">
                    <form action="{{ url('recent_entries_lab_patients_search') }}"
                        method="post">
                        @csrf
                        <input class="search-query"
                            id="search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Search Patient By Id, Name or Phone...">
                        <i class="icon-search1"
                            onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>
            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection

@section('content')
    <!-- Row start -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}"
                role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Patient F-Name</th>
                            <th>Test Name</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recent_entries as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry->patient->patient_generated_id }}</td>
                                <td>{{ ucfirst($entry->patient->patient_name) }}</td>
                                <td>{{ ucfirst($entry->patient->patient_fname) }}</td>
                                @if ($entry->testName != null)
                                    <td>{{ $entry->testName->dep_name . '(' . $entry->testName->price . ')' }}</td>
                                @else
                                    <td>No Test</td>
                                @endif
                                <td>{{ $entry->created_at }}</td>
                                <td>
                                    <div class="btn-group"
                                        role="group"
                                        aria-label="Button group with nested dropdown">
                                        <a class="btn btn-danger"
                                            href="{{ url('delete_patient_test', $entry->id) }}"
                                            onclick="return confirm('Are you sure You want to delete this test?')"><i class="icon icon-delete"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 pt-5">
            {{ $recent_entries->links() }}
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $('#datatable').dataTable({
            paging: false
        });
    </script>
@endsection
