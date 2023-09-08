<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>ShopLister Register</title>
</head>
    <body>
        <section class="vh-100" style="background-color: #508bfc;">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="card shadow-2-strong" style="border-radius: 1rem;">
                            <div class="card-body p-5 text-center">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <h3 class="mb-5">Register</h3>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="name">Name</label>
                                        <input id="name" type="text" class="form-control form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                        @error('name')
                                            <span class="text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="email">Email</label>
                                        <input id="email" type="email" class="form-control form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="name" autofocus>
                                        @error('email')
                                            <div class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <input id="password" type="password" class="form-control form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                        <div class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="password-confirm">Password Confirm</label>
                                        <input id="password-confirm" type="password" class="form-control form-control form-control-lg" name="password_confirmation" required autocomplete="new-password">
                                        @error('password')
                                        <div class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-check d-flex justify-content-start mb-4">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">Remember Me</label>
                                    </div>
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
                                    <hr class="my-4">
                                    <a class="btn btn-lg btn-block btn-primary" href="{{ route('login') }}" style="background-color: #dd4b39;" type="submit"><i class="fab fa-google me-2"></i>Already registered? Login</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
