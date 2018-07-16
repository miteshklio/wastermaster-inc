@extends('templates.master')

@section('title')
    <title>Wastemaster | Users</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-7">
            <h2 style="margin-top: 0">Manage Users</h2>
        </div>
        <div class="col-sm-3">
            {!! $datatable->renderSearch() !!}
        </div>
        <div class="col-sm-2 text-right">
             <a href="/admin/user" class="btn btn-sm btn-success btn-block btn-new">New User</a>
        </div>
    </div>

    <br>

    @if ($datatable->hasResults())

        <p>{!! $datatable->renderMeta() !!}</p>

        <div class="table-responsive">
            <table class="table">
                {!! $datatable->renderHeader('table') !!}
                <tbody>
                @foreach ($datatable->rows() as $user)
                    <tr>
                        <td class="hidden-xs">{{ $user->id }}</td>
                        <td>
                            <a href="/admin/user/{{ $user->id }}">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td class="hidden-xs">{{ $user->role->name }}</td>
                        <td class="hidden-xs">{{ date('Y-m-d', strtotime($user->created_at)) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Modify <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin/user/{{ $user->id }}">Edit</a></li>
                                    <li><a href="/admin/user/{{ $user->id }}/delete">Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! $datatable->renderLinks() !!}
    @else
        <div class="alert alert-warning">
            No users were found in the system.
        </div>
    @endif
@endsection
