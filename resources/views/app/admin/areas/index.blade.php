@extends('templates.master')

@section('title')
    <title>Wastemaster | Service Areas</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-7">
            <h2 style="margin-top: 0">Manage Service Areas</h2>
        </div>
        <div class="col-sm-3">
            {!! $datatable->renderSearch() !!}
        </div>
        <div class="col-sm-2">
            <a href="{{ route('areas::new') }}" class="btn btn-sm btn-success btn-block btn-new">New Service Area</a>
        </div>
    </div>

    <br>

    @if ($datatable->hasResults())

        <p>{!! $datatable->renderMeta() !!}</p>

        <table class="table">
            {!! $datatable->renderHeader('table') !!}
            <tbody>
            @foreach ($datatable->rows() as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>
                        <a href="{{ route('areas::show', ['id' => $row->id]) }}">{{ $row->name }}</a>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Modify <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('areas::show', ['id' => $row->id]) }}">Details</a>
                                </li>
                                <li>
                                    <a href="{{ route('areas::delete', ['id' => $row->id]) }}" onClick="return confirm('Delete this Service Area permanently?');">
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $datatable->renderLinks() !!}
    @else
        <div class="alert alert-warning">
            No Service Areas were found in the system.
        </div>
    @endif

    <div id="modal-wrap"></div>
@endsection
