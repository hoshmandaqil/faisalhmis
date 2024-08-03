<form action="{{ route('laboratory_patient_lab.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $patient_id }}">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-inline">
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Test Name</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Price</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-3">Remark</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-3">Result</label>
                    <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Attachment</label>
                    {{-- <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Actions</label> --}}
                </div>
                <?php $onlyReassignLabs = []; ?>
                @foreach ($labs as $lab)
                    @if (($lab->checkLabAlreadySet($lab->patient_id, $lab->lab_id) || $lab->lab->main_dep_id == 15) && in_array($lab->lab->mainDepartment->dep_name, $user_permissions))
                        @if ($lab->lab->main_dep_id == 15)
                            <?php array_push($onlyReassignLabs, $lab); ?>
                        @else
                            <div class="form-inline" id="{{ $lab->id }}">
                                <input type="hidden" name="lab_id[]" value="{{ $lab->lab->id }}">
                                <input type="hidden" name="lab_discounts[]"
                                    value="{{ $lab->lab->mainDepartment->discount }}">
                                <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="lab_name[]"
                                    readonly value="{{ $lab->lab->dep_name }}">
                                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_price"
                                    name="price[]" readonly value="{{ $lab->lab->price }}">
                                <textarea style="height:60px;" cols="3" rows="10"
                                    class="form-control-sm mb-2 mr-sm-2 col-md-3" name="remark[]"
                                    readonly>{{ $lab->remark }}</textarea>
                                <textarea style="height:60px;" cols="3" rows="10"
                                    class="form-control-sm mb-2 mr-sm-2 col-md-3" name="result[]"></textarea>
                                <input type="file" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="attachments[]">
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
                        <div class="form-inline" id="{{ $lab->id }}">
                            <input type="hidden" name="lab_id[]" value="{{ $lab->lab->id }}">
                            <input type="hidden" name="lab_discounts[]"
                                value="{{ $lab->lab->mainDepartment->discount }}">
                            <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="lab_name[]" readonly
                                value="{{ $lab->lab->dep_name }}">
                            <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1 test_price" name="price[]"
                                readonly value="{{ $lab->lab->price }}">
                            <textarea style="height:60px;" cols="3" rows="10"
                                class="form-control-sm mb-2 mr-sm-2 col-md-3" name="remark[]"
                                readonly>{{ $lab->remark }}</textarea>
                            <textarea style="height:60px;" cols="3" rows="10"
                                class="form-control-sm mb-2 mr-sm-2 col-md-3" name="result[]"></textarea>
                            <input type="file" class="form-control-sm mb-2 mr-sm-2 col-md-2" name="attachments[]">
                            <i class="icon-minus-circle text-danger" style="cursor: pointer"
                                onclick="removeTest(this)"></i>
                        </div>
                    @endforeach
                @endif



                <hr>
                <span id="patientTotalPrice"></span>
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

    function setTotalPrice() {
        var grandTotalPrice = 0;
        var totalValues = $('.test_price').map((_, el) => el.value).get();
        for (var i = 0; i < totalValues.length; i++) {
            grandTotalPrice += totalValues[i] << 0;
        }
        $('#patientTotalPrice').html('<b>Total: ' + grandTotalPrice + '</b>');
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
