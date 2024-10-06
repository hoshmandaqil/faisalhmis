@extends('layouts.master')

@section('page_title')
    Expenses Report 
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                <div class="search-box">
                    <form action="{{ route('expenses.search') }}" method="GET">
                        <input type="text" name="searchTerm" class="search-query" value="{{ request('searchTerm') }}"
                            placeholder="Search Expense ...">
                        <button type="submit" style="background:none; border:none;">
                            <i class="icon-search1"></i>
                        </button>
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
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif

    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table id="scrollVertical" class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Slip No.</th>
                            <th>Paid By</th>
                            <th>Paid To</th>
                            <th>Expense Date</th>
                            <th>Category</th>
                            <th>Expense details (Remarks)</th>
                            <th>Amount</th>
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
                                <td class="font-parastoo">{{ $expense->remarks }}</td>
                                <td>{{ number_format($expense->sum_paid) }} AF</td>
                            </tr>
                            @php
                                $totalAmount += $expense->sum_paid;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                            <td><strong>{{ number_format($totalAmount) }} AF</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
