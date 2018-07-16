@extends('templates.master')

@section('title')
    <title>Wastemaster | Haulers</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-7">
            <h2 style="margin-top: 0">Manage Haulers</h2>
        </div>
        <div class="col-sm-3">
            {!! $datatable->renderSearch() !!}
        </div>
        <div class="col-sm-2 text-right">
             <a href="{{ route('haulers::new') }}" class="btn btn-sm btn-success btn-block btn-new">New Hauler</a>
        </div>
    </div>

    <br>

    @if ($datatable->hasResults())

        <p>{!! $datatable->renderMeta() !!}</p>

        <table class="table">
            {!! $datatable->renderHeader('table') !!}
            <tbody>
            @foreach ($datatable->rows() as $row)
                <tr @if ($row->archived) class="archived" @endif>
                    <td>
                        <a href="{{ route('haulers::show', ['id' => $row->id]) }}">{{ $row->name }}</a>
                    </td>
                    <td>@if (! empty($row->serviceArea)){{ $row->serviceArea->name }} @endif</td>
                    <td>{{ $row->listWasteTypes() }}</td>
                    <td class="hidden-xs">{{ $row->listEmails() }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Modify <span class="caret"></span>
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('haulers::show', ['id' => $row->id]) }}">Details</a>
                                </li>
                                <li>
                                    @if ($row->archived)
                                        <a href="{{ route('haulers::unarchive', ['id' => $row->id]) }}">
                                            UN-Archive
                                        </a>
                                    @else
                                        <a href="{{ route('haulers::archive', ['id' => $row->id]) }}" onClick="return confirm('Archive this Hauler?');">
                                            Archive
                                        </a>
                                    @endif
                                </li>
                                <li>
                                    <a href="{{ route('haulers::delete', ['id' => $row->id]) }}" onClick="return confirm('Delete this Hauler permanently?');">
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $datatable->renderLinks() !!}
    @else
        <div class="alert alert-warning">
            No users were found in the system.
        </div>
    @endif
@endsection
