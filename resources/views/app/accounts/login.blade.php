@extends('templates.master')

@section('title')
    <title>Wastemaster</title>
@stop

@section('content')
    <form class="form-signin" method="post" action="/login">
        {{ csrf_field() }}

        <h2 class="form-signin-heading">Please sign in</h2>

        <div class="form-group">
            <label for="inputEmail">Email address</label>
            <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        </div>

        <div class="form-group">
            <label for="inputPassword" >Password</label>
            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="remember-me"> Remember me
            </label>
        </div>

        <p>
            <a href="/password/reset">
                Forgot your Password?
            </a>
        </p>

        <button class="btn btn-lg btn-green btn-block" type="submit">Sign in</button>
    </form>
@endsection
