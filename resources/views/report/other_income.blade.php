@extends('layouts.master')

@section('page_title')
    Miscellaneous Income Report
@endsection

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{ url('searchPO') }}" method="post">
                        @csrf
                        <input type="text" name="search" class="search-query" value="{{ request('search') }}"
                            placeholder="Search Income ...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
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
                            <th>S.No</th>
                            <th>Slip</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Income Details (Remarks)</th>
                            <th>Amount</th>
                            <th>Paid By</th>
                            <th>Paid To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <x-general.no-record :data="$incomes" />
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach ($incomes as $income)
                            <tr>
                                <td>{{ ($incomes->currentpage() - 1) * $incomes->perpage() + $loop->index + 1 }}
                                <td>{{ $income->slip_no }}</td>
                                <td>{{ $income->date }}</td>
                                <td>{{ $income->incomeCategory ? $income->incomeCategory->name : 'N/A' }}</td>
                                <td>{{ $income->remarks }}</td>
                                <td>{{ number_format($income->amount) }} AF</td>
                                <td>{{ $income->paid_by }}</td>
                                <td>{{ $income->paid_to }}</td>
                            </tr>
                            @php
                                $totalAmount += $income->amount;
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

$(document).ready(function() {
$('#viewIncome').on('show.bs.modal', function(event) {
var button = $(event.relatedTarget);
var income = button.data('income');
var incomeCategory = button.data('category');
var incomeCashir = button.data('cashier');

// Populate modal fields with income details
$('#viewIncome #slipNo').text(income.slip_no);
$('#viewIncome #incomeDate').text(income.date);
$('#viewIncome #paidBy').text(income.paid_by);
$('#viewIncome #paidTo').text(income.paid_to);
$('#viewIncome #incomeCategory').text(incomeCategory);
$('#viewIncome #remarks').text(income.remarks);
$('#viewIncome #cashier').text(incomeCashir);
$('#viewIncome #amount').text(income.amount);

// Clear previous income items
$('#incomeItems').empty();
});
});
