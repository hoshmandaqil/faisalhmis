<form action="{{route('patient_pharmacy_medicine.store')}}" method="post">
    @csrf
    <input type="hidden" name="patient_id" value="{{$patient_id}}">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card">
        <div class="card-body">
            <div class="form-inline">
                <label class="col-form-label-sm mb-2 mr-sm-2 col-md-4">Medicine Name</label>
                <label class="col-form-label-sm mb-2 mr-sm-2 col-md-1">Quantity</label>
                <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Sell Price</label>
                <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Total</label>
                <label class="col-form-label-sm mb-2 mr-sm-2 col-md-2">Actions</label>
            </div>
            @foreach($medicines as $medicine)
            @if($medicine->checkMedicineAlreadySet($medicine->patient_id, $medicine->medicine_id))
            <?php $maxValueOfMedicine = getMedicineSalePrice($medicine->medicine->id)?>
                <div class="form-inline" id="{{$medicine->id}}">
                    <input type="hidden" name="medicine_id[]" value="{{$medicine->medicine->id}}">
                <input type="text" class="form-control-sm mb-2 mr-sm-2 col-md-4" name="medicine_name[]" readonly value="{{$medicine->medicine->medicine_name}}">
                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-1" name="quantity[]"  onkeyup="calculateTotalPrice({{$maxValueOfMedicine}}, {{$medicine->id}}, $(this).val())" value="{{$medicine->quantity}}">
                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-2"  name="sell_price[]" readonly value="{{$maxValueOfMedicine}}">
                <input type="number" class="form-control-sm mb-2 mr-sm-2 col-md-2 totalPrice" readonly value="{{$maxValueOfMedicine * $medicine->quantity}}">
                <input type="checkbox" class="form-check-input mb-2 mr-sm-2 col-md-2" checked value="{{$medicine->id}}" onclick="removeMedicine({{$medicine->id}})">
            </div>
            @endif
                @endforeach
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
    $('form').submit(function(){
        $(this).find(':submit').attr( 'disabled','disabled' );
        //the rest of your code
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 2000)
    });
</script>
