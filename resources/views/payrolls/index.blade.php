@extends('layouts.master')

@section('page_title')
    Payrolls
@endsection

@section('page-action')
    <a href="{{ route('payrolls.create') }}" class="btn btn-primary mb-3">New Payroll</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Payroll Month</th>
                <th>Official Days</th>
                <th>Total(Salary + %age)</th>
                <th>Tax</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->id }}</td>
                    <td>{{ date('m/Y', strtotime($payroll->end_date)) }}</td>
                    <td>{{ $payroll->official_days }}</td>
                    <td><strong>{{ number_format($payroll->total_amount) }}</strong></td>
                    @php
                        // Initialize total tax
                        $itemsTax = $payroll->items->sum('tax');
                        $additionalPaymentsTax = 0;

                        // Iterate through items and decode additional_payments
                        foreach ($payroll->items as $item) {
                                $additionalPayments = json_decode($item->additional_payments, true) ?? [];

                                $additionalPaymentsTax += collect($additionalPayments)->sum('tax');
                        }
                        $totalTax = $itemsTax + $additionalPaymentsTax;
                    @endphp

                    <td><strong>{{ number_format($totalTax, 2) }}</strong></td>
                    <td>
                        <span
                            class="badge badge-{{ $payroll->status == 'approved' ? 'success' : ($payroll->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($payroll->status) }}
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
                                    <a class="dropdown-item px-3" href="{{ route('payrolls.show', $payroll->id) }}">
                                        View
                                    </a>
                                    <a class="dropdown-item px-3" href="#"
                                        onclick="openManageStatusModal({{ $payroll }}, '{{ $payroll->status }}')">
                                        Manage Status
                                    </a>
                                    @if (!$payroll->approved_date)
                                        <a class="dropdown-item px-3" href="{{ route('payrolls.edit', $payroll->id) }}">
                                            Edit
                                        </a>
                                        <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item px-3">Delete</button>
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

    <!-- Manage Status Modal -->
    <div class="modal fade" id="manageStatusModal" tabindex="-1" role="dialog" aria-labelledby="manageStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageStatusModalLabel">Manage Payroll Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="2">
                                            <h5>Payroll ID</h5>
                                            <h6 id="modal-payroll-id"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>Payroll Date</h5>
                                            <h6 id="modal-payroll-date"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>Total Amount </h5>
                                            <h6 id="modal-payroll-total-amount"></h6>
                                        </th>
                                        <th class="text-center">
                                            <h5>Status </h5>
                                            <h6 class="text-danger" id="modal-payroll-status"></h6>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <table class="table table-sm table-rounded table-row-bordered border gs-7 gy-3">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>Status</th>
                                        <th>By</th>
                                        <th>Date</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Checked</td>
                                        <td id="checked-by">X</td>
                                        <td id="checked-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" id="check-button">Check</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Verified</td>
                                        <td id="verified-by">X</td>
                                        <td id="verified-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" id="verify-button">Verify</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Approved</td>
                                        <td id="approved-by">X</td>
                                        <td id="approved-date">X</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" id="approve-button">Approve</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Rejected</td>
                                        <td id="rejected-by">X</td>
                                        <td id="rejected-date">X</td>
                                        <td id="reject-comment"></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" id="reject-button">Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openManageStatusModal(payroll, status) {
            // Populate modal with payroll data
            $('#modal-payroll-id').text(payroll.id);
            $('#modal-payroll-date').text(payroll.end_date);
            $('#modal-payroll-total-amount').text(payroll.total_amount);
            $('#modal-payroll-status').text(status);

            // Populate status data
            $('#checked-by').text(payroll.checked_by || 'X');
            $('#checked-date').text(payroll.checked_date || 'X');
            $('#verified-by').text(payroll.verified_by || 'X');
            $('#verified-date').text(payroll.verified_date || 'X');
            $('#approved-by').text(payroll.approved_by || 'X');
            $('#approved-date').text(payroll.approved_date || 'X');
            $('#rejected-by').text(payroll.rejected_by || 'X');
            $('#rejected-date').text(payroll.rejected_date || 'X');
            $('#reject-comment').text(payroll.reject_comment || '');

            // Show/hide buttons based on current status
            updateStatusButtons(status);

            // Open the modal
            $('#manageStatusModal').modal('show');
        }

        function updateStatusButtons(currentStatus) {
            // Hide all buttons first
            $('#check-button, #verify-button, #approve-button, #reject-button').hide();

            // Show appropriate buttons based on current status
            switch (currentStatus) {
                case 'pending':
                    $('#check-button').show();
                    break;
                case 'checked':
                    $('#verify-button').show();
                    break;
                case 'verified':
                    $('#approve-button').show();
                    $('#reject-button').show();
                    break;
            }
        }

        // Add click handlers for status buttons
        $('#check-button, #verify-button, #approve-button, #reject-button').click(function() {
            let action = $(this).text().toLowerCase();
            let payrollId = $('#modal-payroll-id').text();
            updatePayrollStatus(payrollId, action);
        });

        function updatePayrollStatus(payrollId, status) {
            $.ajax({
                url: '/payroll_status',
                type: 'POST',
                data: {
                    payroll_id: payrollId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Status updated successfully');
                    $('#manageStatusModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error('Error updating payroll status:', error);
                    alert('Error updating payroll status. Please try again.');
                }
            });
        }
    </script>
@endsection
