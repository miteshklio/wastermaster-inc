@extends('templates.master')

@section('title')
    <title>Wastemaster</title>
@stop

@section('content')


    <div class="content-container container-fluid" id = "login-page">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-container login-reset-container">
                    <h2 class="login">Reset Password</h2>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form role="form" method="POST" action="{{ url('/password/email') }}">
                        {!! csrf_field() !!}

                        <label for="email" class="sr-only">E-Mail Address</label>

                        @if ($errors->has('email'))
                            <div class="alert alert-danger" role="alert">{{ $errors->first('email') }}</div>
                        @endif

                        <br>

                        <div class="form-group">
                            <label for="email" class="control-label">Your Email Address</label>
                            <input type="email" class="form-control" name="email" id = "inputEmail" placeholder="john.doe@example.com" value="{{ old('email', '') }}">
                        </div>

                        <br/>

                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
