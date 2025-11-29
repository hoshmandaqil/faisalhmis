<form action="{{ route('patient_lab.update', $patient_id) }}"
    method="post"
    enctype="multipart/form-data">
    {!! csrf_field() !!}
    <input name="_method"
        type="hidden"
        value="PUT">
    <input name="patient_id"
        type="hidden"
        value="{{ $patient_id }}">
    <div id="editPatientLabPrintMe">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 d-print-none"
            id="editPatientLabPrint">
            <div class="form-group">
                <label><b>Patient Name:</b> {{ $patient->patient_name }}</label>
            </div>
            <div class="form-group">
                <label>Selected Tests for Patient: </label>
            </div>
            @foreach ($labs as $lab)
                <div class="form-group">
                    <div class="input-group">
                        <select class="form-control selectpicker col-md-3 labTestsSelectOnEdit"
                            name="labDeps[]"
                            data-live-search="true"
                            required>
                            <option value=""
                                disabled
                                hidden>Please select</option>
                            <option value="{{ $lab->lab->id }}"
                                selected
                                normal_range="{{ $lab->lab->normal_range }}"
                                labTestPrice="{{ $lab->lab->price }}"
                                test_price="{{ $lab->lab->price }}"
                                test_main_dep="{{ $lab->lab->main_dep_id }}"
                                test_discount="{{ $lab->lab->mainDepartment->discount }}">
                                {{ ucfirst($lab->lab->dep_name) }}
                            </option>
                        </select>
                        <input class="form-control col-md-3 test-price-display-edit"
                            type="text"
                            style="height: 38px !important;"
                            placeholder="Price"
                            value="{{ $lab->lab->price }}"
                            readonly>
                        <input class="form-control col-md-2 lab-discount-input-edit"
                            type="number"
                            name="discount[]"
                            style="height: 38px !important;"
                            placeholder="Discount"
                            min="0"
                            step="0.01"
                            value="{{ $lab->discount ?? 0 }}">
                        <input class="form-control col-md-2 test-total-display-edit"
                            type="text"
                            style="height: 38px !important;"
                            placeholder="Total"
                            value="{{ round($lab->lab->price - (($lab->lab->price * ($lab->discount ?? 0)) / 100)) }}"
                            readonly>
                        <input class="form-control col-md-4"
                            name="remark[]"
                            type="text"
                            value="{{ $lab->remark }}"
                            style="height: 38px !important;"
                            placeholder="Remark">

                        @if ($loop->last)
                            <i class="icon-plus-circle ml-2 mt-2"
                                style="cursor: pointer"
                                onclick="addnewLabTestOnEdit()"></i>
                        @else
                            <i class="icon-minus-circle ml-2 mt-2 text-danger"
                                style="cursor: pointer"
                                onclick="removeLab(this)"></i>
                        @endif
                    </div>

                </div>
            @endforeach
            <div id="add_more_lab_test_edit_form">
            </div>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td><b>Total: <span id="dep_lab_total1"></span></b></td>
                        <td><b>Discount: <span id="dep_lab_discount1"></span></b></td>
                        <td><b>Total After Discount: <span id="dep_lab_total_discount1"></span></b></td>
                    </tr>
                </table>
            </div>
            <hr>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 d-none d-print-block">
            <div class="table-responsive table-borderless table-light"
                style="width: 400px !important; max-wdith: 400px !important; padding: 10px !important;">
                <table>
                    <tr>
                        <td class="text-center center"
                            colspan="100%">
                            <h3>Bayazid Rokhan Hospital</h3>
                            <h4>Labratory</h4>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">ID:<span class="font-weight-bolder"> {{ $patient->patient_generated_id }}</span> / </td>
                        <td style="font-weight:bold;">Patient Name:<span class="font-weight-bolder"> {{ $patient->patient_name }}</span></td>
                    </tr>
                </table>
                <table class="table medicine_table"
                    style="font-weight:bold; font-size: 15px !important;">
                    <thead>
                        <th>ITEM</th>
                        <th>RATE</th>
                        <th>QTY</th>
                        <th>AMOUNT</th>
                        <th>DISCOUNT (%)</th>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                            $grandTotalDiscount = 0;
                            $grandTotalAfterDiscount = 0;
                        @endphp
                        @foreach ($labs as $lab)
                            @php
                                $price = $lab->lab->price ?? 0;
                                $discountPercentage = $patient->no_discount == 1 ? 0 : ($lab->discount ?? 0);
                                // Calculate actual discount amount from percentage
                                $discountAmount = ($price * $discountPercentage) / 100;
                                $afterDiscount = $price - $discountAmount;

                                $grandTotal += $price;
                                $grandTotalDiscount += $discountAmount;
                                $grandTotalAfterDiscount += $afterDiscount;
                            @endphp
                            <tr>
                                <td>{{ ucfirst($lab->lab->dep_name) }}</td>
                                <td>{{ round($price) }}</td>
                                <td>1</td>
                                <td>{{ round($afterDiscount) }}</td>
                                <td>{{ $discountPercentage }}%</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;" colspan="3"><b>Amount:</b></td>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;">{{ round($grandTotal) }} AFN</td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;" colspan="3"><b>Discount:</b></td>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;">{{ round($grandTotalDiscount) }} AFN</td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;" colspan="3"><b>Total After Discount:</b></td>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;">{{ round($grandTotalAfterDiscount) }} AFN</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p style="font-size:14px">By: {{ $patient->createdBy->name }}</p>
                <p style="font-size:14px">Date: {{ $patient->created_at }}</p>
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button class="btn btn-secondary btn-sm"
            data-dismiss="modal"
            type="button">Close</button>
        <button class="btn btn-warning btn-sm"
            type="button"
            onclick="printDiv('editPatientLabPrintMe')">Print</button>
        <button class="btn btn-danger submit-btn btn-sm"
            type="submit">Save</button>
    </div>
</form>

<script>
    function addnewLabTestOnEdit() {
        $('#add_more_lab_test_edit_form').append(`
         <div class="form-group">
       <div class="input-group">
     <select class="form-control selectpicker col-md-3 labTestsSelectOnEdit" data-live-search="true" name="labDeps[]">
         <option value="" selected disabled hidden>Please select</option>
            @foreach ($selectLab as $lab)
                <option value="{{ $lab->id }}" normal_range="{{ $lab->normal_range }}" labTestPrice="{{ $lab->price }}" test_price="{{ $lab->price }}" test_main_dep="{{ $lab->main_dep_id }}" test_discount="{{ $lab->mainDepartment->discount }}">{{ ucfirst($lab->dep_name) }}</option>
            @endforeach
        </select>
        <input type="text" class="form-control col-md-3 test-price-display-edit" placeholder="Price" readonly style="height: 38px !important;">
        <input type="number" class="form-control col-md-2 lab-discount-input-edit" name="discount[]" placeholder="Discount" min="0" step="0.01" value="0" style="height: 38px !important;">
        <input type="text" class="form-control col-md-2 test-total-display-edit" placeholder="Total" readonly style="height: 38px !important;">
        <input type="text" class="form-control col-md-4" name="remark[]" placeholder="Remark" style="height: 38px !important;">
        <i class="icon-minus-circle ml-2 mt-2 text-danger" style="cursor: pointer" onclick="removeLab(this)"></i>
    </div>
</div>

`);
        $(".selectpicker").selectpicker('refresh')
    }

    var no_discount = {{ $patient->no_discount }}

    setTotalPriceOnEdit();

    function removeLab(element) {
        $(element).parent('div').remove();
        setTotalPriceOnEdit();
    }

    $(document).on('change', '.labTestsSelectOnEdit', function() {
        var test_price = $('option:selected', this).attr('test_price');
        $(this).parent('.input-group').children('input.test-price-display-edit').val(test_price);
        calculateTestTotalOnEdit($(this));
        setTotalPriceOnEdit();
    });

    $(document).on('input', '.lab-discount-input-edit', function() {
        calculateTestTotalOnEdit($(this).closest('.input-group').find('.labTestsSelectOnEdit'));
        setTotalPriceOnEdit();
    });

    function calculateTestTotalOnEdit(selectElement) {
        var selectedOption = selectElement.find('option:selected');
        var testPrice = parseFloat(selectedOption.attr('test_price')) || 0;
        var discountInput = selectElement.closest('.input-group').find('.lab-discount-input-edit');
        var discountPercentage = no_discount == 1 ? 0 : (parseFloat(discountInput.val()) || 0);
        var totalDisplay = selectElement.closest('.input-group').find('.test-total-display-edit');

        // Calculate discount amount from percentage
        var discountAmount = (testPrice * discountPercentage) / 100;
        var finalPrice = Math.max(0, testPrice - discountAmount);
        totalDisplay.val(finalPrice.toLocaleString());
    }

    function setTotalPriceOnEdit() {
        var grandTotalPrice = 0;
        var grandTotalDiscount = 0;
        var testCount = 0;

        console.log('=== Starting setTotalPriceOnEdit calculation ===');

        // Use a more specific selector to avoid Bootstrap Select wrappers
        var labTestSelects = $("select.labTestsSelectOnEdit");
        var totalElements = labTestSelects.length;
        console.log('Total .labTestsSelectOnEdit elements found:', totalElements);

        // Loop through all lab test rows in edit modal
        labTestSelects.each(function(index) {
            var selectedOption = $(this).find('option:selected');
            var testValue = selectedOption.val();

            console.log('Edit Row ' + index + ': Test value = "' + testValue + '"');

            // Only calculate if a test is selected and has a valid value
            if (testValue && testValue !== '' && testValue !== 'Please select' && testValue !== undefined) {
                var testPrice = parseFloat(selectedOption.attr('test_price')) || 0;
                var discountInput = $(this).closest('.input-group').find('.lab-discount-input-edit');
                var discountPercentage = no_discount == 1 ? 0 : (parseFloat(discountInput.val()) || 0);

                // Calculate discount amount from percentage
                var discountAmount = (testPrice * discountPercentage) / 100;

                console.log('Edit Row ' + index + ' - Price:', testPrice, 'Discount%:', discountPercentage, 'Discount Amount:', discountAmount);

                // Ensure we have valid numbers
                if (!isNaN(testPrice) && !isNaN(discountAmount)) {
                    grandTotalPrice += testPrice;
                    grandTotalDiscount += discountAmount;
                    testCount++;

                    console.log('Edit Test ' + testCount + ':', selectedOption.text(), 'Price:', testPrice, 'Discount:', discountAmount);
                }
            }
        });

        var payableAmount = Math.max(0, grandTotalPrice - grandTotalDiscount);

        console.log('Edit Final totals - Price:', grandTotalPrice, 'Discount:', grandTotalDiscount, 'Payable:', payableAmount, 'Test count:', testCount);

        $('#dep_lab_total1').html('<b>' + grandTotalPrice.toLocaleString() + '</b>');
        $('#dep_lab_discount1').html('<b>' + grandTotalDiscount.toLocaleString() + '</b>');
        $('#dep_lab_total_discount1').html('<b>' + payableAmount.toLocaleString() + '</b>');
    }
</script>
