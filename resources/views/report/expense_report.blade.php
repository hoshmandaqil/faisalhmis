@extends('layouts.master')

@section('page_title')
    Expenses Report
@endsection

@section('search_bar')
    <div class="search-container my-4">
        <div class="row justify-content-center">
            <!-- Search Bar Section -->
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12 mb-3">
                <form action="{{ route('expenses.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="searchTerm" class="form-control" value="{{ request('searchTerm') }}"
                            placeholder="Search Expense...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="icon-search1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Category Filter Section -->
            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mb-3">
                <form action="{{ route('overview_report') }}" method="GET">
                    <input type="hidden" name="from" value="{{ request('from') }}">
                    <input type="hidden" name="to" value="{{ request('to') }}">
                    <input type="hidden" name="report_type" value="{{ request('report_type') }}">
                    <input type="hidden" name="searchTerm" value="{{ request('searchTerm') }}"> <!-- Carry search term -->

                    <div class="input-group">
                        <select class="form-control" name="category" id="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">
                                <i class="icon-filter mr-2"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <!-- Alert Section -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="col-12">
                <div class="alert {{ session()->get('alert-type') }} alert-dismissible fade show" role="alert">
                    {{ session()->get('alert') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Table Section -->
    <div class="row gutters">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Expense Report ({{ request('from') }} - {{ request('to') }})</h5>
                </div> --}}
                <div class="card-body table-responsive">
                    <table id="scrollVertical" class="table table-striped table-hover">
                        <thead class="card-header bg-primary">
                            <tr>
                                <th>No.</th>
                                <th>Slip No.</th>
                                <th>Paid By</th>
                                <th>Paid To</th>
                                <th>Expense Date</th>
                                <th>Category</th>
                                <th>Remarks</th>
                                <th>Amount (AF)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAmount = 0;
                            @endphp
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ ($expenses->currentpage() - 1) * $expenses->perpage() + $loop->index + 1 }}</td>
                                    <td>{{ $expense->slip_no }}</td>
                                    <td>{{ $expense->paid_by }}</td>
                                    <td>{{ $expense->paid_to }}</td>
                                    <td>{{ $expense->date }}</td>
                                    <td>{{ $expense->expenseCategory->name ?? '' }}</td>
                                    <td class="text-muted">{{ $expense->remarks }}</td>
                                    <td>{{ number_format($expense->sum_paid, 2) }} AF</td>
                                </tr>
                                @php
                                    $totalAmount += $expense->sum_paid;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                <td><strong>{{ number_format($totalAmount, 2) }} AF</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
