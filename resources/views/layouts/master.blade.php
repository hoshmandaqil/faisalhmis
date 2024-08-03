<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description"
        content="">
    <meta name="author"
        content="Sajad Haibat">
    <link href="{{ asset('assets/img/fav.png') }}"
        rel="shortcut icon" />
    <!-- CSRF Token -->
    <meta name="csrf-token"
        content="{{ csrf_token() }}">
    <!-- Title -->
    <title>Bayazid Rokhan Pharmacy System</title>

    <!-- *************
        ************ Common Css Files *************
        ************ -->
    <!-- Bootstrap css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}"
        rel="stylesheet">

    <!-- Icomoon Font Icons css -->
    <link type="text/css"
        href="{{ asset('assets/fonts/style.css') }}"
        rel="stylesheet">

    <!-- Main css -->
    <link href="{{ asset('assets/css/main.css') }}"
        rel="stylesheet">

    <style>
        .print-header {
            display: none;
        }

        @media print {
            .page-title {
                display: none;
            }

            .print-header {
                display: block;
            }

            .content-wrapper {
                padding-top: 0 !important;
            }
        }
    </style>

    @yield('styles')

</head>

<body>
    <!-- Loading starts -->
    {{-- @include('layouts.loading') --}}
    <!-- Loading ends -->

    <div class="container">
        <!-- Header start -->
        @include('layouts.header')
        <!-- Header end -->

        <!-- Navigation start -->
        @include('layouts.navbar')
        <!-- Navigation end -->

        <!-- Search bar start -->
        @yield('search_bar')

        <!-- Search bar end -->

        <div class="main-container">

            <!-- Page header start -->
            <div class="page-title">
                <div class="row gutters">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <h5 class="title">@yield('page_title')</h5>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="daterange-container">
                            <!-- Button trigger modal -->
                            @yield('page-action')

                        </div>
                    </div>
                </div>
            </div>

            @yield('on_print_page_header')

            <!-- Page header end -->

            <!-- Content wrapper start -->
            <div class="content-wrapper">
                @yield('content')
            </div>
            <!-- Content wrapper end -->

        </div>

    </div>
    <!-- *************
        ************ Required JavaScript Files *************
    ************* -->
    <!-- Required jQuery first, then Bootstrap Bundle JS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/nav.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <!-- Main Js Required -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        $('form').submit(function() {
            $(this).find(':submit').attr('disabled', 'disabled');
            //the rest of your code
            setTimeout(() => {
                $(this).find(':submit').attr('disabled', false);
            }, 5000)
        });
    </script>

    <script>
        // disable mousewheel on a input number field when in focus
        // (to prevent Cromium browsers change the value when scrolling)
        $('form').on('focus', 'input[type=number]', function(e) {
            $(this).on('wheel.disableScroll', function(e) {
                e.preventDefault()
            })
        });
        $('form').on('blur', 'input[type=number]', function(e) {
            $(this).off('wheel.disableScroll')
        })
    </script>
    @yield('scripts')
    <script>
        function printDiv(divId) {
            var prtContent = document.getElementById(divId);
            var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            WinPrint.innerHTML = "";
            WinPrint.document.write(prtContent.innerHTML);
            WinPrint.document.write(`<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">`)
            WinPrint.document.write(`<link href="{{ asset('assets/css/main.css') }}" rel = "stylesheet" >`)
            WinPrint.document.write(`<link href="{{ asset('assets/vendor/bs-select/bs-select.css') }}" rel="stylesheet" />`)
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            // WinPrint.close();
        }
    </script>
</body>

</html>
