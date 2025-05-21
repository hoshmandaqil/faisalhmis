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
            {{-- <div class="row gutters">

            <div class="col-6 offset-3 text-center">
                <p class="title"
                    style="font-size: 1.3rem">Ministry of Health</p>
                <p class="title"
                    style="font-size: 1.2rem">Bayazid Rokhan Hospital</p>
                <p class="title"
                    style="font-size: 1rem">Patient Laboratory</p>
            </div>

        </div> --}}
            <div class="form-group">
                <label><b>Patient Name:</b> {{ $patient->patient_name }}</label>
            </div>
            <div class="form-group">
                <label>Selected Tests for Patient: </label>
            </div>
            @foreach ($labs as $lab)
                <div class="form-group">
                    <div class="input-group">
                        <select class="form-control selectpicker col-md-6 labTestsSelectOnEdit"
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
                        <input class="form-control col-md-6"
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
                    </thead>
                    <tbody>
                        <?php $grandTotal = 0; ?>
                        @foreach ($labs as $lab)
                            <tr>
                                <td>{{ ucfirst($lab->lab->dep_name) }}</td>
                                <td>{{ round($lab->lab->price ) }}</td>
                                <td>1</td>
                                <td>{{ round($lab->lab->price ) }}</td>
                                <?php $grandTotal += $lab->lab->price; ?>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="border-top: 1px solid lightgray; font-weight:bold;"
                                colspan="100%"><b>Total: {{ round($grandTotal) }} AFN</b></td>
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
     <select class="form-control selectpicker col-md-6 labTestsSelectOnEdit" data-live-search="true" name="labDeps[]">
         <option value="" selected disabled hidden>Please select</option>
            @foreach ($selectLab as $lab)
                <option value="{{ $lab->id }}" normal_range="{{ $lab->normal_range }}" labTestPrice="{{ $lab->price }}" test_price="{{ $lab->price }}" test_main_dep="{{ $lab->main_dep_id }}" test_discount="{{ $lab->mainDepartment->discount }}">{{ ucfirst($lab->dep_name) }}</option>
            @endforeach
        </select>
        <input type="text" class="form-control col-md-6" name="remark[]" placeholder="Remark" style="height: 38px !important;">
        <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnewLabTestOnEdit()"></i>
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
        setTotalPriceOnEdit();
    });


    function setTotalPriceOnEdit() {

        var grandTotalPrice = 0;
        var grandTotalAfterDiscount = 0;
        var grandTotalDiscount = 0;

        var totalValues = $(".labTestsSelectOnEdit :selected").map((i, el) => $(el).attr("labTestPrice")).toArray();
        var totalDiscounts = $(".labTestsSelectOnEdit :selected").map((i, el) => $(el).attr("test_discount")).toArray();

        for (var i = 0; i < totalValues.length; i++) {
            grandTotalPrice += totalValues[i] << 0;

            if (no_discount == 1) {
                grandTotalAfterDiscount += totalValues[i] * 1
            } else {
                grandTotalAfterDiscount += (totalValues[i] * (100 - totalDiscounts[i]) / 100)
                grandTotalDiscount += (totalValues[i] * (totalDiscounts[i]) / 100)
            }
        }

        $('#dep_lab_total1').html('<b>' + grandTotalPrice.toLocaleString() + '</b>');
        $('#dep_lab_discount1').html('<b>' + grandTotalDiscount.toLocaleString() + '</b>');
        $('#dep_lab_total_discount1').html('<b>' + grandTotalAfterDiscount.toLocaleString() + '</b>');
    }
</script>
