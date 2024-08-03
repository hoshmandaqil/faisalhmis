@extends('layouts.master')

@section('page_title')
    Change Password
@endsection

@section('page-action')
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
    <form action="{{url('save_change_password')}}" autocomplete="off" method="POST" enctype="multipart/form-data">
        {{csrf_field()}}
    <div class="col-8">

        <div class="form-group">
            <label class="label-control">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>

        <div class="form-group">
            <label class="label-control">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
    </div>

        <div class="col-4">
            <button type="submit" class="btn btn-danger btn-sm">Save</button>
        </div>
    </form>
@endsection
@section('scripts')
@endsection
