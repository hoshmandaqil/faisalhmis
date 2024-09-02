@extends('layouts.master')

@section('page_title')
    Payroll Payments
@endsection

@section('page-action')
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPayrollPayment">
        New Payment
    </button>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-sm table-rounded border gs-7 gy-3 table-row-bordered table-column-bordered table-striped"
            id="pageTable">
            <thead>
                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200 text-center">
                    <th>Slip No</th>
                    <th>Month/Year</th>
                    <th>Fullname</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payrollPayments as $payment)
                    <tr class="text-center">
                        <td>{{ $payment->slip_no }}</td>
                        <td>{{ $payment->payroll ? \Carbon\Carbon::parse($payment->payroll->payroll_date)->format('m/Y') : 'N/A' }}
                        </td>
                        <td>{{ $payment->employee->first_name }} {{ $payment->employee->last_name }}</td>
                        <td>{{ $payment->employee->position ?? 'Unknown' }}</td>
                        <td>
                            <span>
                                {{ $payment->employee->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ number_format($payment->amount) }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>
                            <span>
                                {{ $payment->payment_method ? 'Full Payment' : 'Advance' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex">
                                <!-- View Button -->
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#viewPayrollPayment">
                                    View
                                </button>

                                <!-- Edit Button -->
                                <button class="btn btn-icon btn-primary btn-sm edit-btn mr-2" data-id="{{ $payment->id }}"
                                    data-employee="{{ $payment->employee->first_name }}"
                                    data-amount="{{ $payment->amount }}" data-date="{{ $payment->payment_date }}"
                                    data-remarks="{{ $payment->remarks }}">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('payroll_payments.destroy', $payment->id) }}" method="post"
                                    class="d-inline">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-icon btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this payment?');">
                                        Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPayrollPayment" tabindex="-1" role="dialog" aria-labelledby="addPayrollPaymentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="btn btn-sm btn-dark" onclick="newPo()">Add New</button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="payrollPaymentForm">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="employee">Employee</label>
                                <select id="employee" name="employee" class="form-control" required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="payrollDate">Payroll Month</label>
                                <input id="payrollDate" class="form-control" type="date" name="payroll_date" required>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div id="payrollDetails" class="table-responsive d-none">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payroll Month</th>
                                        <th>Salary</th>
                                        <th>Present Days</th>
                                        <th>Additional Payments</th>
                                        <th>Tax</th>
                                        <th>Bonus</th>
                                        <th>Payable</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="payrollMonthDisplay"></td>
                                        <td id="salary"></td>
                                        <td id="presentDays"></td>
                                        <td id="additionalPayments"></td>
                                        <td id="tax"></td>
                                        <td id="bonus"></td>
                                        <td id="payable"></td>
                                        <td id="paid"></td>
                                        <td id="balance"></td>
                                        <td id="remarks"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Details Section -->
                        <h5>Payment Details:</h5>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="paymentType">Type</label>
                                <select id="paymentType" name="paymentType" class="form-control" required>
                                    <option value="1">Final Month Payment</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payment">Payment</label>
                                <input type="text" id="payment" name="payment" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paymentDate">Payment Date</label>
                                {{-- <input type="text" id="paymentDate" name="paymentDate" class="form-control" required> --}}
                                <input type="date" id="paymentDate" name="paymentDate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="paymentRemarks">Remarks</label>
                                <textarea id="paymentRemarks" name="paymentRemarks" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button id="submitBtn" type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Payment Modal -->
    <div class="modal fade" id="viewPayrollPayment" tabindex="-1" role="dialog"
        aria-labelledby="viewPayrollPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="btn btn-sm btn-dark" onclick="newPo()">Add New</button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <example-component></example-component>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/persianDatepicker/js/persianDatepicker.min.js') }}"></script>
    <script>
        $('body').on('focus', ".persianDate", function() {
            $(this).persianDatepicker();
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#employee, #payrollDate').change(function() {
                let employeeId = $('#employee').val();
                let payrollDate = $('#payrollDate').val();

                if (employeeId && payrollDate) {
                    // Make an AJAX call to fetch payroll details
                    $.ajax({
                        url: '/getPayrollDetails',
                        method: 'GET',
                        data: {
                            employee_id: employeeId,
                            payroll_date: payrollDate
                        },
                        success: function(response) {
                            // Assuming response contains payroll details
                            $('#payrollMonthDisplay').text(response.payroll_date);
                            $('#salary').text(response.salary);
                            $('#presentDays').text(response.present_days);
                            $('#additionalPayments').text(response.additional_payments);
                            $('#tax').text(response.tax);
                            $('#bonus').text(response.bonus);
                            $('#payable').text(response.payable);
                            $('#paid').text(response.paid);
                            $('#balance').text(response.balance);
                            $('#remarks').text(response.remarks);

                            $('#payment').val(response.balance); // Set payment amount
                            $('#payrollDetails').removeClass('d-none'); // Show table
                            if (Number(response.balance) <= 0) {
                                $('#submitBtn').hide()
                            } else {
                                $('#submitBtn').show()
                            }
                        }
                    });
                }
            });


            $('#payrollPaymentForm').submit(function(event) {
                event.preventDefault();
                // Get form data
                var formData = {
                    employee_id: $('#employee').val(),
                    payroll_date: $('#payrollDate').val(),
                    payment_type: $('#paymentType').val(),
                    payment_amount: $('#payment').val(),
                    payment_date: $('#paymentDate').val(),
                    remarks: $('#paymentRemarks').val(),
                };

                if (Number(formData.payment_amount) <= 0) {
                    alert('Unable to process payment!')
                    return
                }

                // AJAX request
                $.ajax({
                    url: '/payrolls/payments',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Display success message or update UI
                        alert(response.message);
                        // Optionally, you can close the modal and refresh the page or update the table with the new data
                        $('#payrollPaymentModal').modal('hide');
                        location.reload(); // Reload the page to reflect changes (optional)
                    },
                    error: function(xhr, status, error) {
                        // Handle errors and display them to the user
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                });
            });
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/persianDatepicker/css/persianDatepicker-default.css') }}" />
@endsection
