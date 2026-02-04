{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ config('app.name', 'Hotel Paradise') }}</title>

    <!-- Favicons -->
    <link rel="shortcut icon" href="{{asset('assets')}}/img/favicon.png">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/feather.css">

    <!-- Select 2 -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/select2.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/fontawesome/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/bootstrap/css/bootstrap.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/css/admin.css">

</head>

<body>
    <div class="main-wrapper">
        @if (request()->routeIs('login'))
        <div class="position-absolute top-0 end-0 m-3 demo-floating" style="z-index:1050;">
            <div class="card shadow-sm" style="min-width:240px;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>Demo users</strong>
                        <small class="text-muted">Try now</small>
                    </div>
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action demo-fill-btn" data-email="biplobhosen214@gmail.com" data-password="12369874">
                            <div class="fw-semibold">Hotel Paradise</div><small class="text-muted">biplobhosen214@gmail.com • 12369874</small>
                        </button>
                        <button class="list-group-item list-group-item-action demo-fill-btn" data-email="biplabhosen@icloud.com" data-password="123654">
                            <div class="fw-semibold">Grand Palace Hotel</div><small class="text-muted">biplabhosen@icloud.com • 123654</small>
                        </button>
                        <button class="list-group-item list-group-item-action demo-fill-btn" data-email="biplobhosen@gmail.com" data-password="123654">
                            <div class="fw-semibold">Hotel Florida</div><small class="text-muted">biplobhosen@gmail.com • 123654</small>
                        </button>
                        <small class="text-muted text-center mt-1">Professional PMS SaaS — Multi-tenant</small>
                    </div>
                </div>
            </div>
        </div>
        <style>
        .demo-floating .list-group-item { cursor: pointer; }
        .demo-floating .list-group-item:hover { background:#f8f9fa; }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.demo-fill-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var email = this.dataset.email;
                    var pass = this.dataset.password;
                    var emailField = document.querySelector('input[name="email"], input[type="email"]');
                    var passField = document.querySelector('input[name="password"]');
                    if (emailField) { emailField.value = email; emailField.focus(); }
                    if (passField) { passField.value = pass; }
                    [emailField, passField].forEach(function(el){ if(el){ el.classList.add('flash-demo'); setTimeout(function(){ el.classList.remove('flash-demo'); }, 1200); } });
                });
            });
        });
        </script>
        @endif
        <div class="login-pages">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="login-logo mx-auto">
                            <img src="{{asset('assets')}}/img/logo-login.png" alt="img">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="login-images">
                            <img src="{{asset('assets')}}/img/login-banner.png" alt="img">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="login-content">
                            <div class="login-contenthead mb-1">
                                <h5>Login</h5>
                            </div>
                            <form action="{{route('login')}}" method="POST">
                                @csrf
                                <div class="login-input">
                                    <div class="form-group">
                                        <label>{{__('E-mail')}}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label>{{__('Password')}}</label>
                                            {{-- <a class="forgetpassword-link" href="forget-password.html">Forgot password?</a> --}}
                                            @if (Route::has('password.request'))
                                    <a class="forgetpassword-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif
                                        </div>
                                        <div class="pass-group">
                                            {{-- <input type="password" class="form-control " placeholder="********"> --}}
                                            <input id="password" type="password" class="form-control pass-input @error('password') is-invalid @enderror" name="password" placeholder="********" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div>
                                    <div class="filter-checkbox mb-3">
                                        <ul class="d-flex justify-content-between">
                                            <li>
                                                <label class="checkboxs">
                                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <span><i></i></span>
                                                    <b class="check-content">Remember Me</b>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="login-button">
                                    {{-- <a href="index.html" class="btn btn-login">Login</a> --}}
                                    <button type="submit" class="btn btn-primary btn-login">
                                    {{ __('Login') }}
                                </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{asset('assets')}}/js/jquery-3.7.1.min.js" type="036b6eeee10d588318609634-text/javascript"></script>

    <!-- Select 2 JS-->
    <script src="{{asset('assets')}}/js/select2.min.js" type="036b6eeee10d588318609634-text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{asset('assets')}}/plugins/bootstrap/js/bootstrap.bundle.min.js" type="036b6eeee10d588318609634-text/javascript"></script>

 <!-- Sweetalert 2 -->
    <script src="{{asset('assets')}}/plugins/sweetalert/sweetalert2.all.min.js" type="036b6eeee10d588318609634-text/javascript"></script>
    <script src="{{asset('assets')}}/plugins/sweetalert/sweetalerts.min.js" type="036b6eeee10d588318609634-text/javascript"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets')}}/js/admin.js" type="036b6eeee10d588318609634-text/javascript"></script>

<script src="{{asset('assets')}}/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="036b6eeee10d588318609634-|49" defer></script></body>

</html>
