@extends('layouts.master')

@section('page_title')
    Payroll Payments
@endsection

{{-- @section('page-action')
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPayrollPayment">
        New Payment
    </button>
@endsection --}}

@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{ url('search-payroll-payments') }}" method="post">
                        @csrf
                        <input type="text" name="search" class="search-query"
                            value="{{ Request::is('search') ? $patientSearchDetail : '' }}"
                            placeholder="Search Payroll Payment By Employee ID, Name or Phone...">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection

@section('page-action')
    @if (\Request::is('search-payroll-payments'))
        <a type="button" class="btn btn-danger btn-sm" href="{{ url('payroll-payments') }}">
            Clear Search
        </a>
    @endif
    @if (!\Request::is('search-payroll-payments'))
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPayrollPayment">
            New Payment
        </button>
    @endif
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
                        <td>{{ $payment->payroll ? \Carbon\Carbon::parse($payment->payment_date)->format('m/Y') : 'N/A' }}
                        </td>
                        <td>{{ $payment->employee->first_name }} {{ $payment->employee->last_name }}</td>
                        <td>{{ $payment->employee->position ?? 'Unknown' }}</td>
                        <td>
                            <span>
                                {{ $payment->employee->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>
                            <span>
                                {{ $payment->payment_method ? 'Full Payment' : 'Advance' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm dropdown-toggle" id="btnGroupDrop1"
                                        data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        @if (in_array('payment_view', $user_permissions))
                                            <a class="dropdown-item px-3" href="#" data-toggle="modal"
                                                data-target="#viewPayrollPayment" data-id="{{ $payment->id }}">
                                                View
                                            </a>
                                        @endif
                                        @if (in_array('payment_edit', $user_permissions))
                                            <a class="dropdown-item px-3 edit-btn" href="#"
                                                data-id="{{ $payment->id }}"
                                                data-employee="{{ $payment->employee->first_name }}"
                                                data-amount="{{ $payment->amount }}"
                                                data-date="{{ $payment->payment_date }}"
                                                data-type="{{ $payment->payment_method }}"
                                                data-remarks="{{ $payment->remarks }}">
                                                Edit
                                            </a>
                                        @endif
                                        @if (in_array('payment_delete', $user_permissions))
                                            <form action="{{ route('payroll_payments.destroy', $payment->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item px-3"
                                                    onclick="return confirm('Are you sure you want to delete this payment?');">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div>
            {{ $payrollPayments->links() }} </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPayrollPayment" tabindex="-1" role="dialog" aria-labelledby="addPayrollPaymentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-wide" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="btn btn-sm btn-dark" onclick="newPo()">Add New</button> --}}
                    <h5 class="modal-title" id="addPayrollPaymentLabel">Add New Payroll Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="payrollPaymentForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="employee">Employee</label>
                                <select id="employee" name="employee" class="form-control" required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->first_name }}
                                            {{ $employee->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payrollDate">Payroll Month</label>
                                <input id="payrollDate" class="form-control" type="month" name="payroll_date" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paymentType">Payment Type</label>
                                <select id="paymentType" name="payment_type" class="form-control" required>
                                    <option value="1">Full Payment</option>
                                    {{-- <option value="2">Advance Payment</option> --}}
                                </select>
                            </div>
                        </div>

                        <!-- Payroll Details Section -->
                        <div id="payrollDetails" class="table-responsive d-none">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payroll Month</th>
                                        <th>Base Salary</th>
                                        <th>Present Days</th>
                                        <th>Night Duty/Monibox</th>
                                        <th>Additional Payments</th>
                                        <th>Tax</th>
                                        <th>Gross Salary</th>
                                        <th>Net Payable</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="payrollMonthDisplay"></td>
                                        <td id="baseSalary"></td>
                                        <td id="presentDays"></td>
                                        <td id="bonus"></td>
                                        <td id="additionalPayments"></td>
                                        <td id="tax"></td>
                                        <td id="grossSalary"></td>
                                        <td id="netPayable"></td>
                                        <td id="paid"></td>
                                        <td id="balance"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Details Section -->
                        <h5 class="mb-4">Payment Details:</h5>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="payment">Payment Amount</label>
                                <input type="number" id="payment" name="payment" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paymentDate">Payment Date</label>
                                <input type="date" id="paymentDate" name="payment_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paymentRemarks">Remarks</label>
                                <textarea id="paymentRemarks" name="remarks" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button id="submitBtn" type="submit" class="btn btn-primary">Save Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Payment Modal -->
    <div class="modal fade" id="viewPayrollPayment" tabindex="-1" role="dialog"
        aria-labelledby="viewPayrollPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-wide" role="document">
            <div class="modal-content" id="printPayment">
                <div class="modal-header d-none d-print-block">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mr-auto">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="" style="height: 50px"
                                class="mb-4">
                        </div>
                        <div class="text-center mx-auto">
                            <h5 class="modal-title">Ministry of Public Health</h5>
                            <h5 class="modal-title">Bayazid Rokhan Curative Hospital</h5>
                            <h5 class="modal-title">Finance Department</h5>
                            <h5 class="modal-title">Employee Payment Slip</h5>
                        </div>
                        <div class="ml-auto">
                            <img src="{{ asset('assets/img/logo/mlogo.png') }}" alt="" style="height: 50px"
                                class="mb-4">
                        </div>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                    <div id="paymentDetails">
                        <p class="text-center">Loading...</p>
                    </div>
                </div>
                <div class="d-print-none">
                    <div class="d-flex justify-content-between m-4">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button onclick="printDiv('printPayment')" type="button" class="btn btn-primary">Print</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-print-block">
                    <div style="flex-grow: 1; text-align: left;">
                        <div class="signature" style="display: inline-block; width: 200px;">
                            Finance Officer: ____________________
                        </div>
                    </div>
                    <div style="flex-grow: 1; text-align: right;">
                        <div class="signature" style="display: inline-block; width: 200px;">
                            Employee: ____________________
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fetch payroll details when employee and payroll month are selected
            $('#employee, #payrollDate').change(function() {
                let employeeId = $('#employee').val();
                let payrollDate = $('#payrollDate').val();

                if (employeeId && payrollDate) {
                    // Make an AJAX call to fetch payroll details
                    $.ajax({
                        url: '{{ route('payroll_payments.getPayrollDetails') }}',
                        method: 'GET',
                        data: {
                            employee_id: employeeId,
                            payroll_date: payrollDate
                        },
                        success: function(response) {
                            if (response) {
                                let data = response;

                                $('#payrollMonthDisplay').text(data.payroll_date);
                                $('#baseSalary').text(formatNumber(data.gross_salary - data
                                    .bonus) + ' AF');
                                $('#presentDays').text(data.present_days);
                                $('#bonus').text(formatNumber(data.bonus) + ' AF');
                                $('#additionalPayments').html(formatAdditionalPayments(data
                                    .additional_payments));
                                $('#tax').text(formatNumber(data.tax) + ' AF');
                                $('#grossSalary').text(formatNumber(data.gross_salary) + ' AF');
                                $('#netPayable').text(formatNumber(data.net_salary) + ' AF');
                                $('#paid').text(formatNumber(data.paid) + ' AF');
                                $('#balance').text(formatNumber(data.balance) + ' AF');
                                // $('#remarks').text(data.remarks || '');

                                $('#payment').val(data.balance);
                                $('#payrollDetails').removeClass('d-none');

                                if (Number(data.balance) <= 0) {
                                    $('#submitBtn').hide();
                                } else {
                                    $('#submitBtn').show();
                                }
                            } else {
                                alert('Failed to fetch payroll details.');
                                $('#payrollDetails').addClass('d-none');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('An error occurred while fetching payroll details.');
                            $('#payrollDetails').addClass('d-none');
                        }
                    });
                } else {
                    $('#payrollDetails').addClass('d-none');
                }
            });

            // Handle form submission
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
                    alert('Unable to process payment!');
                    return;
                }

                // AJAX request to store payment
                $.ajax({
                    url: '{{ route('payroll_payments.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response) {
                            alert(response.message);
                            $('#addPayrollPayment').modal('hide');
                            location.reload();
                        } else {
                            alert('Failed to save payment.');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });
                        alert(errorMessage);
                    }
                });
            });

            // Handle View Payment Modal
            $('#viewPayrollPayment').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var paymentId = button.data('id'); // Extract info from data-id attribute

                var modal = $(this);
                var paymentDetailsDiv = modal.find('#paymentDetails');

                // Show loading state
                paymentDetailsDiv.html('<p class="text-center">Loading...</p>');

                // Make AJAX request to fetch payment details
                $.ajax({
                    url: '{{ route('payroll_payments.show') }}', // Update with your actual route
                    method: 'GET',
                    data: {
                        id: paymentId
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var payrollPayments = response.payrollItems;
                            // Construct the HTML to display payment details in cards
                            var detailsHtml = `
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 rounded">
                                            <div>
                                                <h6>${data.employee.first_name} ${data.employee.last_name}</h6>
                                                <h6><strong>Father Name:</strong> ${data.employee.father_name || 'N/A'}</h6>
                                            </div>
                                            <div>
                                                <h6><strong>Position:</strong> ${data.employee.position || 'Unknown'}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Payment Details</h5>
                                                <p class="card-text"><strong>Slip No:</strong> ${data.slip_no}</p>
                                                <p class="card-text"><strong>Month/Year:</strong> ${data.payroll_date}</p>
                                                <p class="card-text"><strong>Payment Date:</strong> ${data.payment_date}</p>
                                                <p class="card-text"><strong>Remarks:</strong> ${data.remarks || 'N/A'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Salary Details</h5>
                                                <p class="card-text"><strong>Gross Salary:</strong> ${parseFloat(payrollPayments.gross_salary - payrollPayments.bonus).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AFN</p>
                                                <p class="card-text"><strong>Bonus:</strong> ${parseFloat(payrollPayments.bonus).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AFN</p>
                                                <p class="card-text"><strong>Tax:</strong> ${parseFloat(payrollPayments.tax).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AFN</p>
                                                <p class="card-text"><strong>Net Salary:</strong> ${parseFloat(payrollPayments.net_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AF</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Additional Information</h5>
                                                <p class="card-text"><strong>Present Days:</strong> ${payrollPayments.present_days}</p>
                                                <p class="card-text"><strong>Paid Amount:</strong> ${parseFloat(data.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AF</p>
                                                <p class="card-text"><strong>Balance Amount:</strong> ${parseFloat(payrollPayments.net_salary - data.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} AF</p>
                                                <p class="card-text"><strong>Type:</strong> ${data.payment_method ? 'Full Payment' : 'Advance'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 rounded">
                                            <div>
                                                <h6><strong>Grand Total:</strong> ${data.amount || '0'} AFN</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            paymentDetailsDiv.html(detailsHtml);
                        } else {
                            paymentDetailsDiv.html(
                                '<p class="text-center">Unable to fetch payment details.</p>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        paymentDetailsDiv.html(
                            '<p class="text-center text-danger">An error occurred while fetching payment details.</p>'
                        );
                    }
                });

            });
        });

        // Function to format additional payments into a table
        function formatAdditionalPayments(additionalPayments) {
            if (!additionalPayments || additionalPayments.length === 0) {
                return 'N/A';
            }

            let table =
                '<table class="table table-bordered"><thead><tr class="bg-secondary"><th class="pb-2 pt-2 text-nowrap">Department</th><th class="pb-2 pt-2">Tests</th><th class="pb-2 pt-2 text-nowrap">Total Price</th><th class="pb-2 pt-2">Gross</th><th class="pb-2 pt-2">Tax</th><th class="pb-2 pt-2 text-nowrap">Net Payable</th></tr></thead><tbody>';

            additionalPayments.forEach(function(payment) {
                table += '<tr>';
                table += `<td class="pb-2 pt-2">${payment.main_lab_department}</td>`;
                table += `<td class="pb-2 pt-2">${payment.number_of_tests}</td>`;
                table += `<td class="pb-2 pt-2">${formatNumber(payment.total_price)} AF</td>`;
                table += `<td class="pb-2 pt-2">${formatNumber(payment.payable)} AF</td>`;
                table += `<td class="pb-2 pt-2">${formatNumber(payment.tax)} AF</td>`;
                table += `<td class="pb-2 pt-2">${formatNumber(payment.payable - payment.tax)} AF</td>`;
                table += '</tr>';
            });
            // Calculate totals
            let totalTests = 0;
            let totalPrice = 0;
            let totalGross = 0;
            let totalTax = 0;
            let totalNetPayable = 0;

            additionalPayments.forEach(function(payment) {
                totalTests += parseInt(payment.number_of_tests);
                totalPrice += parseFloat(payment.total_price);
                totalGross += parseFloat(payment.payable);
                totalTax += parseFloat(payment.tax);
                totalNetPayable += parseFloat(payment.payable) - parseFloat(payment.tax);
            });

            // Add footer with totals inside tfoot
            table += '<tfoot>';
            table += '<tr class="bg-light font-weight-bold">';
            table += '<td class="pb-2 pt-2">Total</td>';
            table += `<td class="pb-2 pt-2">${totalTests}</td>`;
            table += `<td class="pb-2 pt-2">${formatNumber(totalPrice)} AF</td>`;
            table += `<td class="pb-2 pt-2">${formatNumber(totalGross)} AF</td>`;
            table += `<td class="pb-2 pt-2">${formatNumber(totalTax)} AF</td>`;
            table += `<td class="pb-2 pt-2">${formatNumber(totalNetPayable)} AF</td>`;
            table += '</tr>';
            table += '</tfoot>';

            table += '</tbody></table>';
            return table;
        }

        // Function to format numbers with commas and two decimal places
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endsection

@section('styles')
    <style>
        table td {
            vertical-align: top !important;
        }

        .modal-body input,
        .modal-body select,
        .modal-body textarea {
            height: 35px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

        .modal-wide {
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .modal-wide {
                max-width: 90%;
            }
        }
    </style>
@endsection
