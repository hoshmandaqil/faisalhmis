@extends('layouts.master')

@section('page_title')
    Pharmacy
@endsection

@section('page-action')
    @if(Request::is('search_medicine'))
        <a type="button" class="btn btn-danger btn-sm" href="{{route('pharmacy.index')}}">Clear Search</a>
    @endif
    <a type="button" class="btn btn-primary btn-sm" href="{{route('pharmacy.create')}}">
        Add New Medicine
    </a>
{{--    <a href="#" class="btn btn-success btn-sm pull-right"--}}
{{--       data-toggle="modal" data-target="#exampleModal">Set Supplier</a>--}}
@endsection
@section('styles')
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendor/bs-select/bs-select.css')}}"/>
@endsection
@section('search_bar')
    <div class="search-container">
        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">

                <div class="search-box">
                    <form action="{{url('search_medicine')}}" method="GET">
                        <input type="text" name="search_medicine" class="search-query"
                               value="{{(Request::is('search_medicine') ? $medicineSearchDetail : '')}}" placeholder="Search Medicine By Name, QTY, Invoice, Remark or Price">
                        <i class="icon-search1" onclick="$(this).closest('form').submit();"></i>
                    </form>
                </div>

            </div>
        </div>
        <!-- Row end -->
    </div>
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
            <div class="table-responsive">
                <table id="scrollVertical" class="table">
                    <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Name</th>
                        <th>QTY</th>
                        <th>Purchase Price</th>
                        <th>Sale Percentage</th>
                        <th>Sale Price</th>
                        <th>Supplier</th>
                        <th>Invoice #NO</th>
                        <th>Remark</th>
                            <th>created at</th>
                        <th>created By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pharmacies  as $pharmacy)
                        <tr>
                            <td>
{{--                                <span><input type="checkbox" class="selectedMedicine" value="{{$pharmacy->id}}"></span> --}}
                                {{$loop->iteration}}</td>
                            <td>{{$pharmacy->medicineName->medicine_name}}</td>
                            <td>{{$pharmacy->quantity}}</td>
                            <td>{{$pharmacy->purchase_price}}</td>
                            <td>{{$pharmacy->sale_percentage}}%</td>
                            <td>{{$pharmacy->sale_price}}</td>
                            <td>{{($pharmacy->supplier != null) ?$pharmacy->supplier->supplier_name: 'No Supplier'}}</td>
                            <td>{{$pharmacy->invoice_no}}</td>
                            <td>{{$pharmacy->remark}}</td>
                             <td>{{$pharmacy->created_at}}</td>
                            <td>{{$pharmacy->user->name}}</td>
                            <td>
                           @if(in_array('pharmacy_procurement_edit_medicine', $user_permissions))
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item text-info" href="#" data-toggle="modal"
                                               data-target="#editMedicineModal" data-id="{{$pharmacy->id}}"
                                               data-medicine-id="{{$pharmacy->medicine_id}}" data-supplier="{{$pharmacy->supplier_id}}"
                                               data-quantity="{{$pharmacy->quantity}}" data-purchase-price="{{$pharmacy->purchase_price}}"
                                               data-percentage="{{$pharmacy->sale_percentage}}" data-sale-price="{{$pharmacy->sale_price}}"
                                               data-remark="{{$pharmacy->remark}}" data-invoice="{{$pharmacy->invoice_no}}"
                                               data-vendor="{{$pharmacy->vendor}}">

                                                <i class="icon icon-edit"></i> Edit</a>
                                            <form action="{{route('pharmacy.destroy', $pharmacy->id)}}" method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="_method" value="Delete">
                                                <button class="dropdown-item text-danger" type="submit"
                                                        onclick="return confirm('Are you sure You want to delete this Medicine?')">
                                                    <i class="icon icon-delete"></i> Delete
                                                </button>
                                            </form>
                                              @if($pharmacy->returned == 0)
                                            <a class="dropdown-item text-warning" href="{{ url('return_medicine', $pharmacy->id) }}" onclick="return confirm('Are you sure You want to return this Medicine?')"><i class="icon icon-block"></i> Return Medicine</a>
                                            @else
                                            <a class="dropdown-item text-warning" href="{{ url('undo_return_medicine', $pharmacy->id) }}" onclick="return confirm('Are you sure You want to Undo return this Medicine?')"><i class="icon icon-block"></i> Undo Return</a>
                                            @endif

                                            @if($pharmacy->expired == 0)
                                            <a class="dropdown-item text-success" href="{{ url('expire_this_medicine', $pharmacy->id) }}" onclick="return confirm('Are you sure You want to expire this Medicine?')"><i class="icon icon-calendar"></i> Expire Medicine</a>
                                            @else
                                            <a class="dropdown-item text-success" href="{{ url('undo_expire_this_medicine', $pharmacy->id) }}" onclick="return confirm('Are you sure You want to Undo expire this Medicine?')"><i class="icon icon-calendar"></i> Undo Expire</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                    @endif

                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {{ $pharmacies->links() }}

            </div>
        </div>
    </div>

{{--    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">--}}
{{--        <div class="modal-dialog" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalLabel">Set Supplier<span id="lab_patient_name"></span></h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form  method="GET" enctype="multipart/form-data" id="medicineForm">--}}

{{--                        <div class="form-group " id="vendor_base">--}}
{{--                            <label class="label">Vendor:</label>--}}
{{--                            <select class="form-control selectpicker" data-live-search="true" id="supplier_id" name="vendor_base" >--}}
{{--                                @foreach($suppliers as $vendor)--}}
{{--                                        <option value="{{$vendor->id}}">{{$vendor->supplier_name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div class="submit-section">--}}
{{--                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}

{{--                            <button class="btn btn-primary submit-btn" type="button" id="approvePosButton">Submit</button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div--}}
{{--    >--}}


    <!-- Edit Modal -->
    <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Medicine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editMedicineForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row">

                            <div class="form-group col-6">
                                <label class="label">Medicine</label>
                                <select class="form-control selectpicker" data-live-search="true" id="item"
                                        name="item">
                                    @foreach($medicines as $medicine)
                                        <option value="{{$medicine->id}}">{{$medicine->medicine_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label class="label">Supplier</label>
                                <select class="form-control"
                                        name="supplier_id">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->supplier_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-6">
                                <label>Purchase Quantity</label>
                                <input class="form-control" type="text" name="quantity">
                            </div>

                            <div class="form-group col-6">
                                <label>Purchase Price</label>
                                <input class="form-control purchase_price" type="text" name="purchase_price">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-6">
                                <label>Percentage</label>
                                <input class="form-control sale_percentage_edit" type="text" name="sale_percentage">
                            </div>

                            <div class="form-group col-6">
                                <label>Sale Price</label>
                                <input class="form-control" type="text" name="sale_price">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-6">
                                <label>Invoice#</label>
                                <input class="form-control" type="text" name="invice_no">
                            </div>

                            <div class="form-group col-6">
                                <label>Vendor</label>
                                <input class="form-control" type="text" name="vendor">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form- col-12">
                                <label>Remark</label>
                                <textarea class="form-control" name="remark"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Save</button>
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
        $('#editMedicineModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var medicine_id = button.data('medicine-id');
            var supplier = button.data('supplier');
            var quantity = button.data('quantity');
            var purchase_price = button.data('purchase-price');
            var percentage = button.data('percentage');
            var sale_price = button.data('sale-price');
            var remark = button.data('remark');
            var invoice = button.data('invoice');
            var vendor = button.data('vendor');
            var modal = $(this)

            // Set values in edit popup
            var action = '/pharmacy/'+id;
            $("#editMedicineForm").attr("action", action);
            // modal.find('.modal-body [name="patient_generated_id"]').val(generated_id);
            modal.find('.modal-body [name="item"]').val(medicine_id);
            modal.find('.modal-body [name="supplier_id"]').val(supplier);
            modal.find('.modal-body [name="quantity"]').val(quantity);
            modal.find('.modal-body [name="purchase_price"]').val(purchase_price);
            modal.find('.modal-body [name="sale_percentage"]').val(percentage);
            modal.find('.modal-body [name="sale_price"]').val(sale_price);
            modal.find('.modal-body [name="invice_no"]').val(invoice);
            modal.find('.modal-body [name="vendor"]').val(vendor);
            modal.find('.modal-body [name="remark"]').val(remark);
            $('.selectpicker').selectpicker('refresh')

        })



        $(document).on('keyup', '.sale_percentage_edit', function () {
                var per =  $(this).val();
                var price = $('#editMedicineForm').find('input[name="purchase_price"]').val();
                var sale_price = (parseFloat(price) + ((parseFloat(price) * parseFloat(per)) / 100));
                 $('#editMedicineForm').find('input[name="sale_price"]').val(sale_price);
                var maxLength = 50;
            if ($(this).val() > maxLength){
                alert(maxLength+' is Max length!');
            }
        });

    </script>


{{--    <script>--}}
{{--        var selectedOpds = [];--}}
{{--        $('#approvePosButton').click(function () {--}}
{{--            selectedOpds.length = 0;--}}
{{--            if (confirm("Are you sure you want to Proceed?")){--}}

{{--                $('.selectedMedicine:checkbox:checked').each(function () {--}}
{{--                    // var sThisVal = (this.checked ? $(this).val() : "");--}}
{{--                    selectedOpds.indexOf($(this).val()) === -1 ? selectedOpds.push($(this).val()) : console.log("This item already exists");--}}

{{--                    // selectedOpds.push($(this).val());--}}

{{--                });--}}
{{--                var supplier_id = $('#supplier_id').val();--}}

{{--                $.ajax({--}}
{{--                    url: "/setSupplierMultipleMedicine",--}}
{{--                    type: "post",--}}
{{--                    data: {Pos: selectedOpds, supplierId: supplier_id, "_token": "{{ csrf_token() }}"} ,--}}
{{--                    success: function (response) {--}}
{{--                        if (response == 1){--}}
{{--                            window.location.reload();--}}
{{--                        }--}}
{{--                    },--}}
{{--                    error: function(jqXHR, textStatus, errorThrown) {--}}
{{--                        console.log(textStatus, errorThrown);--}}
{{--                        alert("an error occured!");--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}
{{--        })--}}
{{--    </script>--}}

@endsection
