@extends('layouts.master')

@section('page_title')
    Suppliers List
@endsection

@section('page-action')
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal">
        Add Supplier
    </button>
@endsection
@section('styles')
    <style>
        .modal-body input, .modal-body select {
            height: 30px !important;
        }
        .modal-body div.form-group{
            margin-top: -10px !important;
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
            <div class="table-responsive">
                <table id="scrollVertical" class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier Name</th>
                        <th>Supplier Phone</th>
                        <th>Supplier Address</th>
                        <th>Supplier Short Code</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$supplier->supplier_name}}</td>
                            <td>{{$supplier->supplier_phone}}</td>
                            <td>{{$supplier->supplier_address}}</td>
                            <td>{{$supplier->supplier_shortCode}}</td>
                            <td>
                                @if(in_array('edit_supplier', $user_permissions) || in_array('delete_supplier', $user_permissions))
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            @if(in_array('edit_supplier', $user_permissions))
                                            <a class="dropdown-item text-info"  href="#" data-toggle="modal" data-target="#editsupplierModal" data-id="{{$supplier->id}}"
                                               data-name ="{{$supplier->supplier_name}}"    data-phone ="{{$supplier->supplier_phone}}"  data-address ="{{$supplier->supplier_address}}"
                                               data-shortcode="{{$supplier->supplier_shortCode}}">
                                                <i class="icon icon-edit"></i> Edit</a>
                                            @endif
                                            @if(in_array('delete_supplier', $user_permissions))
                                                    <form action="{{route('supplier.destroy', $supplier->id)}}" method="post">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="_method" value="Delete">
                                                        <button class="dropdown-item text-danger" type="submit"
                                                                onclick="return confirm('Are you sure You want to delete this Test?')">
                                                            <i class="icon icon-delete"></i> Delete
                                                        </button>
                                                    </form>                                        @endif
                                        </div>
                                    </div>
                                </div>
                                    @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('supplier.store')}}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Supplier Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="supplier_name" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier phone<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="supplier_phone" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier Address</label>
                            <input class="form-control" type="text" name="supplier_address">
                        </div>
                        <div class="form-group">
                            <label>Supplier Short Code</label>
                            <input class="form-control" type="text" name="supplier_shortCode">
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger btn-sm submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{{--Edit Modal--}}
    <div class="modal fade" id="editsupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editsupplierModalForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <div class="form-group">
                            <label>Supplier Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="supplier_name" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier phone<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="supplier_phone" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier Address</label>
                            <input class="form-control" type="text" name="supplier_address">
                        </div>
                        <div class="form-group">
                            <label>Supplier Short Code</label>
                            <input class="form-control" type="text" name="supplier_shortCode">
                        </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger btn-sm submit-btn" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $('#editsupplierModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            var id = button.data('id');
            var name = button.data('name');
            var phone = button.data('phone');
            var address = button.data('address');
            var shortcode = button.data('shortcode');
            var modal = $(this)

            // Set values in edit popup
            var action = '/supplier/'+id;
            $("#editsupplierModalForm").attr("action", action);
            modal.find('.modal-body [name="supplier_name"]').val(name);
            modal.find('.modal-body [name="supplier_phone"]').val(phone);
            modal.find('.modal-body [name="supplier_address"]').val(address);
            modal.find('.modal-body [name="supplier_shortCode"]').val(shortcode);
        })
    </script>
@endsection
