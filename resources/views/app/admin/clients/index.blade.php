@extends('templates.master')

@section('title')
    <title>Wastemaster | Clients</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-7">
            <h2 style="margin-top: 0">Manage Clients</h2>
        </div>
        <div class="col-sm-3">
            {!! $datatable->renderSearch() !!}
        </div>
        <div class="col-sm-2">
             <a href="{{ route('clients::new') }}" class="btn btn-sm btn-success btn-block btn-new">New Client</a>
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
                        <a href="{{ route('clients::show', ['id' => $row->id]) }}">{{ $row->company }}</a>
                    </td>
                    <td>@if (! empty($row->serviceArea)){{ $row->serviceArea->name }} @endif</td>
                    <td class="hidden-xs">{{ date('M j, Y', strtotime($row->created_at)) }}</td>
                    <td class="hidden-xs text-right">${{ number_format($row->prior_total, 2) }}</td>
                    <td class="text-right">${{ number_format($row->net_monthly, 2) }}</td>
                    <td class="text-right">${{ number_format($row->gross_profit, 2) }}</td>
                    <td class="text-right">${{ number_format($row->total, 2) }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Modify <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('clients::show', ['id' => $row->id]) }}">Details</a>
                                </li>
                                <li>
                                    <a href="{{ route('clients::rebid', ['id' => $row->id]) }}" onClick="return confirm('Rebid this client?');">
                                        Rebid
                                    </a>
                                </li>
                                <li>
                                @if ($row->archived)
                                    <a href="{{ route('clients::unarchive', ['id' => $row->id]) }}">UN-Archive</a>
                                @else
                                    <a href="{{ route('clients::archive', ['id' => $row->id]) }}" onClick="return confirm('Archive this Client?');">
                                        Archive
                                    </a>
                                @endif
                                </li>
                                <li>
                                    <a href="{{ route('clients::delete', ['id' => $row->id]) }}" onClick="return confirm('Delete this Client permanently?');">
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
            No users were found in the system.
        </div>
    @endif
@endsection
