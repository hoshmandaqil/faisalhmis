@extends('layouts.master')

@section('page_title')
    Add New Medicine
@endsection

@section('page-action')
    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal">
        Insert Medicine Name
    </button>
@endsection
@section('styles')
    <!-- Bootstrap Select CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link rel="stylesheet" href="{{asset('assets/vendor/bs-select/bs-select.css')}}" />
    <style type="text/css">
        #table {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        td{
            text-align: center;
            padding: 1px !important;

        }
        .w-140
        {width: 144px !important}
        .w-180
        {width: 180px !important}
        .item
        {
            width: 100px !important;
        }

        #sale_price,#per
        {
            float: left;
            width: 70px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered
        {
            display: block;
            padding-left: 0px !important;
            padding-right: 2px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .container {
            max-width: 100% !important;
        }

    </style>
@endsection
@section('content')

    <!-- Row start -->
    @if(session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                    <div class="col-md-8">
                    <button id="add" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">
                        <i class="sidenav-icon icon icon-plus"></i> Existing
                    </button>
                    <button id="remove" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal">
                        <i class="sidenav-icon icon icon-trash"></i> Remove Last Row
                    </button>
                    </div>
                    <div class="col-md-4 pull-right">
                        <form action="">
                            <select class="form-control selectpicker" data-live-search="true" id="supplier_list" name="supplier_name">
                                <option value="" selected disabled>Please Select Supplier</option>
                                @foreach($suppliers as $key => $supplier)
                                    <option value="{{$supplier->id}}" shortcode="{{$supplier->supplier_shortCode}}">{{ucfirst($supplier->supplier_name)}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    </div>
                    <p></p>
                    <form id="demo-form-wizard-1" novalidate method="post" data-toggle="validator"
                          enctype="multipart/form-data" action="{{route('pharmacy.store')}}" class="purchase_form"
                          style="display: none"
                    >
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="supplier_id" id="supplier_id">
                        <table id="table"  border="1" cellspacing="0" cellpadding="2">
                            <tr>
                                <td colspan="3">Date</td>
                                <td colspan="3"><input type="date" name="date" id="date" class="form-control"></td>
                                <td colspan="2">Invoice Number</td>
                                <td colspan="4"><input type="text" name="invice_no" id="invice_no" class="form-control"></td>
                            </tr>
                            <tr>

{{--                                <td>Pro Type</td>--}}
{{--                                <td>Asset Type</td>--}}
                                <td width="250px">Item</td>
{{--                                <td>Unit</td>--}}
                                <td>Barcode</td>

                                <td>PQTY</td>
                                <td>PPrice</td>
                                <td>%</td>
                                <td>SPrice</td>

                                <td>Vendor</td>
                                <td>Mfg date</td>
                                <td class="w-180">Exp date</td>
                                <td>Remark</td>


                            </tr>
                            <tfoot>
                            <th colspan="13">Grand Total: <span id="PPTotalValue">0</span></th>
                            </tfoot>
                        </table>
                        <button class="btn btn-info" type="submit"> <i class="fa fa-save"></i> Save Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Medicine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('medicine_name.store')}}" method="post" enctype="multipart/form-data" id="medicineForm">
                        {!! csrf_field() !!}
                        <input type="hidden" name="patient_id" id="medicine_patient_id">

                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine Name" style="height: 38px !important;">

                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>

                        </div>
                        <div id="add_more">
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn" type="button" onclick="saveMedicineName()">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <!-- Bootstrap Select JS -->
    <script src="{{asset('assets/vendor/bs-select/bs-select.min.js')}}"></script>

    <script>
        function addnew(){
            $('#add_more').append(`
                   <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine Name" style="height: 38px !important;">

                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>

                        </div>`);
            $(".selectpicker").selectpicker('refresh')

        }
        function clearInput() {
            $('#medicineForm').find('input[type=text], input[type=password], input[type=number], input[type=email], input[type=checkbox],textarea').val('');
        };

        $('#exampleModal').on('hidden.bs.modal', function(){
            clearInput();
            $('#add_more').empty();
        });

    </script>


    <script type="text/javascript">
        var sum = 0;
        $(document).on("input", ".price",function() {
            var quantity = $(this).closest('tr').find('td input.quantity').val();
            var thisRowSum = $(this).val() * quantity;

            // $(".price").each(function(){
            sum += +thisRowSum;
            // });
            $("#PPTotalValue").text(sum);
        });



        function increment(newValue) {
            $('#PPTotalValue').text( function(i, oldval) {
                return oldval + newValue;
            });
        }
        $(document).ready(function() {
            document.getElementById('date').valueAsDate = new Date();

            $("#add").click(function(){

                var medicines = getMedicines();

            });

            function getMedicines(){
                var medicines =[];
                $.ajax({
                    type: "GET",
                    url: '{{url('getMedicines')}}',
                    success: function (response) {
                        $.each(response, function (index, value) {
                            medicines[index] = value;
                        });
                        console.log(response)
                        var ex_random = Math.random();
                        var ex_random2 = Math.random();
                        var options;
                        jQuery.each( medicines, function( i, val ) {
                            if (val != undefined){
                                options += `<option value=`+i+`>`+val+`</option>`;
                            }
                        });
                        $("#table").append('<tr id="tr">' +
                            // '<td><select class="select2" name="procurement_type[]" required>' +
                            // '<option value="1">Test </option></select></td>' +
                            // '<td> <select class="select2" name="asset_type[]" required> ' +
                            // '<option value="1">type1</option></select></td>' +

                            '<td> <select class="select2"  data-live-search="true" name="item[]" required> ' +
                            ''+options+'</select> </td>' +
                            // '<td><select class="select2" required name="unit[]"><option value="">Type1</option> </select> </td>' +
                            '<td><input type="text" name="barcode[]" class="form-control" placeholder="Barcode inpute"></td>' +
                            '<td><input type="text" name="quantity[]" class="form-control quantity"/></td>' +
                            '<td><input type="text" name="purchase_price[]"  class="form-control price"/></td>' +
                            '<td><input type="number" name="sale_percentage[]" id="per" class="per" max="50" ></td>' +
                            '<td><input type="text" name="sale_price[]" id="sale_price" class="sale_price"></td> ' +
                            '<td><input type="text" name="vendor[]"  class="form-control"/></td>' +
                            ' <td><input type="date" id="'+ex_random2+'" name="mfg_date[]"  class="form-control w-140 date-val"/></td> ' +
                            '<td><input type="date" id="'+ex_random+'" name="exp_date[]"  class="form-control w-180 date-val"/></td> ' +
                            '<td><input type="text" name="remark[]"   class="form-control"/></td></tr>');

                        document.getElementById(ex_random).valueAsDate = new Date();
                        document.getElementById(ex_random2).valueAsDate = new Date();
                        function initializeSelect2(selectElementObj) {
                            selectElementObj.selectpicker({
                                width: "100%",
                                tags: true,
                                search: true
                            });
                        }

                        //onload: call the above function
                        $(".select2").each(function() {
                            initializeSelect2($(this));
                        });
                    },
                    error: function () {
                        alert("An Error Occured, Please try again!");
                    }
                });
                // console.log(medicines + '122121')
            }

            $("#new").click(function(){
                var random = Math.random();
                var random2 = Math.random();
                $("#table").append('<tr id="tr"><td><select class="select2" name="procurement_type[]" required>' +
                    '<option value="1">type1 </option> </select> </td> <td> <select class="select2" name="asset_type[]" required> ' +
                    '<option value="1">tt </option> ' +
                    '</select> </td> <td> <input class="form-control"  name="item[]" required>  </td> ' +
                    '<td><select class="select2" required name="unit[]"> ' +
                    '<option value="1">unit1</option> ' +
                    '</select> </td> <td><input type="text" name="barcode[]"class="form-control" placeholder="Barcode inpute"></td>' +
                    '<td><input type="text" name="quantity[]" class="form-control quantity"/></td>' +
                    '<td><input type="text" name="purchase_price[]"  class="price form-control"/></td>' +
                    '<td><input type="number" name="sale_percentage[]" id="per" class="per" max="50" ></td>' +
                    '<td><input type="text" class="sale_price" name="sale_price[]" style="width:66px !important"  class="form-control"/></td> ' +
                    ' <td><input type="text" name="vendor[]"  class="form-control"/></td> ' +
                    '<td><input type="date" name="mfg_date[]" id="'+random2+'" class="form-control w-140 date-val"/></td>' +
                    ' <td><input type="date" name="exp_date[]" id="'+random+'" class="form-control w-140 date-val"/></td> ' +
                    '<td><input type="text" name="remark[]"  class="form-control"/></td> </tr>');

                document.getElementById(random).valueAsDate = new Date();
                document.getElementById(random2).valueAsDate = new Date();

            });

        });
        $("#remove").click(function(){
            $("#table tr:last-child").remove();
        });


        $(document).on('keyup', '.per', function () {
            if($(this).hasClass("per")){
                var per =  $(this).val();
                var price = $(this).closest('tr').children('td').find('input.price').val();
                var sale_price = (parseFloat(price) + ((parseFloat(price) * parseFloat(per)) / 100));
                $(this).closest('tr').children('td').find('input.sale_price').val(sale_price);

                var maxLength = 50;
            }
            if ($(this).val() > maxLength){
                alert(maxLength+' is Max length!');
            }
        });

        $(document.body).on("change","#supplier_list",function(){
            // alert(this.value);
            var shortCode = $(this).find('option:selected').attr('shortcode');
            $('#supplier_id').val(this.value);
            $('#invice_no').val('BR_'+shortCode+'_{{$lastIdForInvoiceNumber}}');
            $('.purchase_form').css('display', 'block');
        });
    </script>
    <script>
        function saveMedicineName() {
            var values = $("input[name='medicine_name[]']")
                .map(function(){return $(this).val();}).get();
            $.ajax({
                type: "POST",
                url: '{{route('medicine_name.store')}}',
                data: {values: values, '_token':'{{csrf_token()}}'},
                success: function (response) {
                    $('#exampleModal').modal('hide');
                },
                error: function () {
                    alert("An Error Occured, Please try again!");
                }
            });
        }
    </script>
@endsection
