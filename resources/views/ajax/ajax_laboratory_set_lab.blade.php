<form action="{{ route('laboratory_patient_lab.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $patient_id }}">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-inline">
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Test Name</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Price</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Discount</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Total</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Remark</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Result</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Attachment</label>
                    {{-- <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Actions</label> --}}
                </div>
                <?php $onlyReassignLabs = []; ?>
                @foreach ($labs as $lab)
                    @if (($lab->checkLabAlreadySet($lab->patient_id, $lab->lab_id) || $lab->lab->main_dep_id == 15) && in_array($lab->lab->mainDepartment->dep_name, $user_permissions))
                        @if ($lab->lab->main_dep_id == 15)
                            <?php array_push($onlyReassignLabs, $lab); ?>
                        @else
                            <?php
                            // Calculate automatic discount based on patient type
                            $discountPercentage = 0;
                            if (isset($patient->discount_type)) {
                                if ($patient->discount_type == 'student') {
                                    $discountPercentage = 10; // 10% for students
                                } elseif ($patient->discount_type == 'staff') {
                                    $discountPercentage = 20; // 20% for staff
                                }
                            }
                            // Use auto discount if patient has discount type, otherwise use existing discount
                            $discountPercentage = $discountPercentage > 0 ? $discountPercentage : ($lab->discount ?? 0);
                            $discountAmount = ($discountPercentage * $lab->lab->price) / 100;
                            $totalAfterDiscount = $lab->lab->price - $discountAmount;
                            ?>
                            <div class="form-inline" id="{{ $lab->id }}">
                                <input type="hidden" name="lab_id[]" value="{{ $lab->lab->id }}">
                                <input type="hidden" name="payable_amount[]" class="test_payable_amount" value="{{ $totalAfterDiscount }}">
                                <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="lab_name[]"
                                    readonly value="{{ $lab->lab->dep_name }}">
                                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_price"
                                    name="price[]" readonly value="{{ $lab->lab->price }}">
                                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_discount"
                                    name="lab_discounts[]" value="{{ $discountPercentage }}"
                                    min="0" max="100" step="0.01" onchange="calculateTestTotal(this)">
                                <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_total_display"
                                    readonly value="{{ $totalAfterDiscount }}">
                                <textarea style="height:60px;" cols="3" rows="10"
                                    class="form-control-sm mb-2 mr-sm-2 col-md-2" name="remark[]"
                                    readonly>{{ $lab->remark }}</textarea>
                                <textarea style="height:60px;" cols="3" rows="10"
                                    class="form-control-sm mb-2 mr-sm-2 col-md-2" name="result[]"></textarea>
                                <input type="file" class="form-control-sm mb-2 mr-sm-2 col-md-1" name="attachments[]">
                                <i class="icon-minus-circle text-danger" style="cursor: pointer"
                                    onclick="removeTest(this)"></i>
                            </div>
                        @endif
                    @endif
                @endforeach
                @if (!empty($onlyReassignLabs))
                    <?php
                    $reAssignLabTests = DB::table('lab_departments')
                        ->where('main_dep_id', 15)
                        ->select('id')
                        ->pluck('id')
                        ->toArray();
                    $alreadySavedReassignLabToPatient = DB::table('laboratory_patient_labs')
                        ->whereIn('lab_id', $reAssignLabTests)
                        ->where('patient_id', $patient_id)
                        ->count();
                    $onlyReassignLabs = array_slice($onlyReassignLabs, $alreadySavedReassignLabToPatient);
                    ?>
                    @foreach ($onlyReassignLabs as $reassignLabs)
                        <?php
                        // Calculate automatic discount based on patient type
                        $discountPercentage = 0;
                        if (isset($patient->discount_type)) {
                            if ($patient->discount_type == 'student') {
                                $discountPercentage = 10; // 10% for students
                            } elseif ($patient->discount_type == 'staff') {
                                $discountPercentage = 20; // 20% for staff
                            }
                        }
                        // Use auto discount if patient has discount type, otherwise use existing discount
                        $discountPercentage = $discountPercentage > 0 ? $discountPercentage : ($lab->discount ?? 0);
                        $discountAmount = ($discountPercentage * $lab->lab->price) / 100;
                        $totalAfterDiscount = $lab->lab->price - $discountAmount;
                        ?>
                        <div class="form-inline" id="{{ $lab->id }}">
                            <input type="hidden" name="lab_id[]" value="{{ $lab->lab->id }}">
                            <input type="hidden" name="payable_amount[]" class="test_payable_amount" value="{{ $totalAfterDiscount }}">
                            <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="lab_name[]" readonly
                                value="{{ $lab->lab->dep_name }}">
                            <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_price" name="price[]"
                                readonly value="{{ $lab->lab->price }}">
                            <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_discount"
                                name="lab_discounts[]" value="{{ $discountPercentage }}"
                                min="0" max="100" step="0.01" onchange="calculateTestTotal(this)">
                            <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_total_display"
                                readonly value="{{ $totalAfterDiscount }}">
                            <textarea style="height:60px;" cols="3" rows="10"
                                class="form-control-sm mb-2 mr-sm-2 col-md-2" name="remark[]"
                                readonly>{{ $lab->remark }}</textarea>
                            <textarea style="height:60px;" cols="3" rows="10"
                                class="form-control-sm mb-2 mr-sm-2 col-md-2" name="result[]"></textarea>
                            <input type="file" class="form-control-sm mb-2 mr-sm-2 col-md-1" name="attachments[]">
                            <i class="icon-minus-circle text-danger" style="cursor: pointer"
                                onclick="removeTest(this)"></i>
                        </div>
                    @endforeach
                @endif

                <hr>
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td><b>Grand Total Price: <span id="lab_grand_total">0</span></b></td>
                            <td><b>Total Discount: <span id="lab_total_discount">0</span></b></td>
                            <td><b>Payable Amount: <span id="lab_payable_amount">0</span></b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

        <button class="btn btn-danger submit-btn btn-sm" type="submit">Save and Print</button>
    </div>
</form>

<script>
    setTotalPrice();

    function removeTest(element) {
        $(element).parent('div').remove();
        setTotalPrice();
    }

    function calculateTestTotal(element) {
        var row = $(element).closest('.form-inline');
        var price = parseFloat(row.find('.test_price').val()) || 0;
        var discountPercentage = parseFloat(row.find('.test_discount').val()) || 0;

        // Ensure discount is not negative and not more than 100%
        if (discountPercentage < 0) discountPercentage = 0;
        if (discountPercentage > 100) discountPercentage = 100;

        // Update the discount field with the corrected value
        row.find('.test_discount').val(discountPercentage);

        // Calculate discount amount from percentage
        var discountAmount = (price * discountPercentage) / 100;

        // Calculate total for this test (price - discount amount)
        var finalPrice = price - discountAmount;

        // Ensure final price is not negative
        if (finalPrice < 0) finalPrice = 0;

        // Update the total display for this row
        row.find('.test_total_display').val(finalPrice.toFixed(2));

        // Update the hidden payable amount field
        row.find('.test_payable_amount').val(finalPrice.toFixed(2));

        setTotalPrice();
    }

    function setTotalPrice() {
        var grandTotalPrice = 0;
        var grandTotalDiscount = 0;
        var grandPayableAmount = 0;

        $('.form-inline').each(function() {
            var price = parseFloat($(this).find('.test_price').val()) || 0;
            var discountPercentage = parseFloat($(this).find('.test_discount').val()) || 0;

            // Calculate discount amount from percentage
            var discountAmount = (price * discountPercentage) / 100;

            // Calculate final price: price - discount amount
            var finalPrice = price - discountAmount;

            // Ensure final price is not negative
            if (finalPrice < 0) finalPrice = 0;

            grandTotalPrice += price;
            grandTotalDiscount += discountAmount;
            grandPayableAmount += finalPrice;
        });

        $('#lab_grand_total').html('<b>' + grandTotalPrice.toLocaleString() + '</b>');
        $('#lab_total_discount').html('<b>' + grandTotalDiscount.toLocaleString() + '</b>');
        $('#lab_payable_amount').html('<b>' + grandPayableAmount.toLocaleString() + '</b>');
    }
</script>
<script>
    $('form').submit(function() {
        $(this).find(':submit').attr('disabled', 'disabled');
        //the rest of your code
        setTimeout(() => {
            $(this).find(':submit').attr('disabled', false);
        }, 5000)
    });
</script>
