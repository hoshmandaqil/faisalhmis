
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="Responsive Bootstrap4 Dashboard Template">
    <meta name="author" content="ParkerThemes">
    <link rel="shortcut icon" href="{{asset('assets/img/fav.png')}}" />

    <!-- Title -->
    <title>Bayazid Rokhan Hospital </title>

    <!-- *************
        ************ Common Css Files *************
        ************ -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />

    <!-- Master CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/main.css')}}" />

</head>

<body class="authentication">

<!-- Container start -->
<div class="container">

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row justify-content-md-center">
            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12">
                <div class="login-screen">
                    <div class="login-box">
                        <a href="#" class="login-logo">
                            <span class="text-danger">Hamza</span><span class="text-warning">Medical</span><span class="text-success">Clinic</span><span class="text-info">

                        </a>
                        <h5>Welcome back,<br />Please Login to your Account.</h5>
                        <div class="form-group">
{{--                            <input type="text" class="form-control" placeholder="Email Address" />--}}
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="actions">
                            <button type="submit" class="btn btn-info">Login</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<!-- Container end -->

</body>
<script src="{{asset('assets/js/jquery.min.js')}}"></script>

<script>
    $('form').submit(function(){

        $(this).find(':submit').attr( 'disabled','disabled' );
        //the rest of your code
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 2000)
    });
</script>
</html>
