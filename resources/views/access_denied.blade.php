@extends('layouts.master')

@section('page_title')
    Access Denied
@endsection

@section('page-action')
@endsection
@section('styles')
    <style>
        #notfound {
            height: 80vh
        }

        #notfound .notfound {
            position: absolute;
            left: 50%;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%)
        }

        .notfound {
            max-width: 410px;
            width: 100%;
            text-align: center
        }

        .notfound .notfound-404 {
            height: 280px;
            position: relative;
            z-index: -1
        }

        .notfound .notfound-404 h1 {
            font-family: montserrat, sans-serif;
            font-size: 190px;
            margin: 0;
            font-weight: 900;
            position: absolute;
            left: 50%;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            background: url({{asset('assets/img/opps_bg.jpg')}}) no-repeat;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: cover;
            background-position: center
        }

        .notfound h2 {
            font-family: montserrat, sans-serif;
            color: #000;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 0
        }

        .notfound p {
            font-family: montserrat, sans-serif;
            color: #000;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 20px;
            margin-top: 0
        }

        .notfound a {
            font-family: montserrat, sans-serif;
            font-size: 14px;
            text-decoration: none;
            text-transform: uppercase;
            background: #d52825;
            display: inline-block;
            padding: 15px 30px;
            border-radius: 40px;
            color: #fff;
            font-weight: 700;
            -webkit-box-shadow: 0 4px 15px -5px #d5646b;
            box-shadow: 0 4px 15px -5px #d56564
        }

        @media only screen and (max-width: 767px) {
            .notfound .notfound-404 {
                height: 142px
            }

            .notfound .notfound-404 h1 {
                font-size: 112px
            }
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

        <div id="notfound">
            <div class="notfound">
                <div class="notfound-404">
                    <h1>Oops!</h1>
                </div>
                <h2>403 - Access Denied!</h2>
                <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable.</p>
                <a href="{{url('/')}}">Go To Homepage</a>
            </div>
        </div>
@endsection
@section('scripts')
@endsection


