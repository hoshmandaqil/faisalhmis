@extends('layouts.master')

@section('page_title')
    Set Permission To User
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
    <form action="{{url('save_permissions')}}" method="POST" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" name="user_id" value="{{$id}}">
    <div class="row gutters">
        <?php
        $firstFiveElementPermission = array_slice($permissionArray, 0, 5, true);
        $afterFiveElementPermission = array_slice($permissionArray, 5, null, true);
        ?>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                <div class="card">
                    <div class="card-body">
                        @foreach($firstFiveElementPermission as $key => $permission)
                            <div class="ml-1">
                                <h5><b>{{$key}}</b></h5>
                            </div>
                            @foreach($permission as $permission_name)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="permissions[]"
                                           {{(in_array($permission_name->id, $userPermission) ? 'checked': '')}}
                                           value="{{$permission_name->id}}"
                                           id="{{$permission_name->permission_name}}">
                                    <label class="custom-control-label"
                                           for="{{$permission_name->permission_name}}">{{ucwords(str_replace('_', ' ', $permission_name->permission_name))}}</label>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                <div class="card">
                    <div class="card-body">
                        @foreach($afterFiveElementPermission as $key => $permission)
                            <div class="ml-1">
                                <h5><b>{{$key}}</b></h5>
                            </div>
                            @foreach($permission as $permission_name)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="permissions[]"
                                           {{(in_array($permission_name->id, $userPermission) ? 'checked': '')}}
                                           value="{{$permission_name->id}}"
                                           id="{{$permission_name->permission_name}}">
                                    <label class="custom-control-label"
                                           for="{{$permission_name->permission_name}}">{{ucwords(str_replace('_', ' ', $permission_name->permission_name))}}</label>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

    </div>

        <div class="row gutters">
            <button type="submit" class="btn btn-danger btn-sm ml-2">Save</button>
        </div>
    </form>
@endsection
@section('scripts')
@endsection
