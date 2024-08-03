<form action="{{route('patient_medicine.update', $id)}}" method="post" enctype="multipart/form-data" id="medicineForm">
    {!! csrf_field() !!}
    <input type="hidden" name="patient_id" value="{{$id}}">
    <input type="hidden" name="_method" value="PUT">


    <div class="form-group">
        <label>Selected Medicine</label>
        @foreach($medicines as $soldMedicine)
        <div class="input-group mt-2">
            <select class="form-control selectpicker medicineItemsOfEdit" data-live-search="true" name="medicine_id[]" required>
                <?php $maxSalePrice = 0; ?>        
                
                @if($soldMedicine->medicine->thisMedicinePharmacy->sum('quantity'))
                    <?php
                        $i=1;
                        foreach ($soldMedicine->medicine->thisMedicinePharmacy as $medicineSalePrice){
                            if($medicineSalePrice->sale_price > $maxSalePrice && $i < 3){
                            $maxSalePrice = $medicineSalePrice->sale_price;
                            }
                            $i++;
                        }
                    ?>
                @endif
                
                <option value="{{$soldMedicine->medicine->id}}" sale_price="{{$maxSalePrice}}" selected >
                    {{ucfirst($soldMedicine->medicine->medicine_name)}}
                </option>
            </select>
            <input type="number" class="form-control medicineQTYOfEdit" value="{{$soldMedicine->quantity}}" name="quantity[]" placeholder="Quantity" style="height: 38px !important;">
            <input type="text" class="form-control" value="{{$soldMedicine->remark}}" name="remark[]" placeholder="Remark" style="height: 38px !important;">

            @if($loop->last)

                <i class="icon-minus-circle ml-2 text-danger" style="cursor: pointer" onclick="removeMedicine(this)"></i>
                <i class="icon-plus-circle   text-info" style="cursor: pointer" onclick="addNewOnEdit()"></i>

            @else
                <i class="icon-minus-circle ml-2 mt-2 text-danger" style="cursor: pointer" onclick="removeMedicine(this)"></i>
            @endif
        </div>
        @endforeach
    </div>


    <div id="add_more_edit_form">
    </div>
    <b>Total: <span id="medicine_total_sale_price_on_edit">0</span></b>
    <div class="submit-section">
        <br>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

        <button class="btn btn-primary submit-btn btn-sm" type="submit">Save</button>
    </div>
</form>

<script>
    function addNewOnEdit(){
        $('#add_more_edit_form').append(`
             <div class="form-group"> <div class="input-group"><select class="form-control selectpicker medicineItemsOfEdit" data-live-search="true" name="medicine_id[]">
                @foreach($selectPharmacy as $key => $medicine)

                <?php
                $maxSalePrice = 0;
                $i=1;
                foreach ($medicine->thisMedicinePharmacy as $medicineSalePrice){
                    if($medicineSalePrice->sale_price > $maxSalePrice && $i < 3){
                       $maxSalePrice = $medicineSalePrice->sale_price;
                    }
                    $i++;
                }
                ?>
                <option value="{{$medicine->id}}" sale_price="{{$maxSalePrice}}">
                    {{ucfirst($medicine->medicine_name)}}
                </option>
                @endforeach
        </select>
        <input type="number" class="form-control medicineQTYOfEdit" name="quantity[]" placeholder="Quantity" style="height: 38px !important;">
         <input type="text" class="form-control" name="remark[]" placeholder="Remark" style="height: 38px !important;">
         <i class="icon-minus-circle ml-2 text-danger" style="cursor: pointer" onclick="removeMedicine(this)"></i>
         <i class="icon-plus-circle   text-info" style="cursor: pointer" onclick="addNewOnEdit()"></i>        </div>
    </div>
`);
        $(".selectpicker").selectpicker('refresh')

    }

    function removeMedicine(element) {
        $(element).parent('div').remove();
        setTotalPriceOfMedicineOnEdit();
    }

    $(document).on('input','.medicineQTYOfEdit',function () {
        setTotalPriceOfMedicineOnEdit();
    });
    $(document).on('change','.medicineItemsOfEdit',function () {
        setTotalPriceOfMedicineOnEdit();
    });


    function setTotalPriceOfMedicineOnEdit() {
        var grandTotalPrice = 0;
        var totalValues =  $(".medicineItemsOfEdit :selected").map((i, el) => $(el).attr("sale_price")).toArray();
        var totalQuantities =  $(".medicineQTYOfEdit").map((i, el) => $(el).val()).toArray();
        console.log(totalQuantities, totalValues);
        for (var i = 0; i < totalValues.length; i++) {
            grandTotalPrice += totalValues[i] * totalQuantities[i] << 0;
        }
        $('#medicine_total_sale_price_on_edit').html('<b>' + grandTotalPrice + '</b>');

    }
    setTotalPriceOfMedicineOnEdit();
</script>
