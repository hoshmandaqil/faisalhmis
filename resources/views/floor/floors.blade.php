@extends('layouts.master')

@section('page_title')
   Floors
@endsection

@section('page-action')
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Add Floor
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
                        <th>S.NO</th>
                        <th>Floor Name</th>
                        <th>Room</th>
                        <th>Bed</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Remark</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($floors  as $floor)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$floor->floor_name}}</td>
                            <td>{{$floor->room}}</td>
                            <td>Bed-{{$floor->bed}}</td>
                            <td>{{$floor->price}}</td>
                            <td>{{$floor->discount}}</td>
                            <td>{{$floor->remark}}</td>
                            <td>
                                @if($floor->status == 0)
                                    <span class="badge badge-success">Available</span>
                                    @else
                                    <span class="badge badge-danger">Busy</span>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add New Floor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('floor.store')}}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Floor Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="floor_name" required>
                        </div>
                        <div class="form-group">
                            <label>Room</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="rooms[]"  style="height: 38px !important;">
                                <input type="number" class="form-control" name="beds[]" placeholder="NO of Beds" style="height: 38px !important;">
                                <input type="number" class="form-control" name="prices[]" placeholder="Price" value="2000" style="height: 38px !important;">
                                <input type="number" class="form-control" name="discounts[]" placeholder="Discount %" value="0" style="height: 38px !important;">
                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>
                        </div>
                            <div id="add_more">
                            </div>
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea class="form-control" name="remark"></textarea>
                            </div>
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function addnew(){
            $('#add_more').append(`
               <div class="form-group">
                            <label>Room</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="rooms[]"  style="height: 38px !important;">
                                <input type="number" class="form-control" name="beds[]" placeholder="NO of Beds" style="height: 38px !important;">
                                 <input type="number" class="form-control" name="prices[]" placeholder="Price" value="2000" style="height: 38px !important;">
                                <input type="number" class="form-control" name="discounts[]" placeholder="Discount %" value="0" style="height: 38px !important;">
                                <i class="icon-plus-circle ml-2 mt-2" style="cursor: pointer" onclick="addnew()"></i>
                            </div>
                        </div>
            `);

        }
        function clearInput() {
            $('#medicineForm').find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
        };

        $('#exampleModal').on('hidden.bs.modal', function(){
            clearInput();
            $('#add_more').empty();
        });
    </script>
@endsection
