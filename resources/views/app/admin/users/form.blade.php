@extends('templates.master')

@section('title')
    <title>Wastemaster | Users</title>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            @if(request()->is('admin/user'))
                <h2>Create New User</h2>
            @else
                <h2>Update User</h2>
            @endif

            <form method="post" autocomplete="off">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" class="form-control" name="name" value="{{ $user->name or '' }}" required/>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" class="form-control" name="user_email" autocomplete="off" value="{{ $user->email or '' }}" required/>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" class="form-control" name="pass" autocomplete="off" {{ request()->is('admin/user/create') ? 'required':'' }}/>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" class="form-control" name="role">
                        @foreach($roles as $role)
                            <option {{ (isset($user) and $user->role->id == $role->id) ? 'selected':'' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-success">Submit</button>
            </form>
        </div>
    </div>
@endsection
