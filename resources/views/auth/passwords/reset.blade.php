@extends('templates.master')

@section('title')
    <title>Wastemaster</title>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h2 class="login">Reset Password</h2>
                <form role="form" method="POST" action="{{ url('/password/reset') }}">
                    {!! csrf_field() !!}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        @if ($errors->has('email'))
                            <div class="alert alert-danger" role="alert">{{ $errors->first('email') }}</div>
                        @endif
                        <label for="email" class="control-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="inputEmail" value="{{ old('email', '') }}" placeholder="john.doe@example.com">
                    </div>

                    <div class="form-group">
                        @if ($errors->has('password'))
                            <div class="alert alert-danger" role="alert">{{ $errors->first('password') }}</div>
                        @endif
                        <label for="password" class="control-label">Password</label>
                        <input type="password" class="form-control" name="password" id = "inputPassword" value="{{ old('password', '') }}" >
                    </div>

                    <div class="form-group">
                        @if ($errors->has('password_confirmation'))
                            <div class="alert alert-danger" role="alert">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                        <label for="password_confirmation" class="control-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" id = "inputPasswordConfirmation" value="{{ old('password_confirmation', '') }}">
                    </div>

                    <br/>

                    <button type="submit" class="btn btn-primary btn-block">Reset</button>
                </form>
            </div>
        </div>
    </div>
@endsection
