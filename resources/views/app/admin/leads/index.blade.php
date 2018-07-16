@extends('templates.master')

@section('title')
    <title>Wastemaster | Leads</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-7">
            <h2 style="margin-top: 0">Manage Leads</h2>
        </div>
        <div class="col-sm-3">
            {!! $datatable->renderSearch() !!}
        </div>
        <div class="col-sm-2 text-center">
             <a href="{{ route('leads::new') }}" class="btn btn-sm btn-block btn-success btn-new">New Lead</a>
        </div>
    </div>

    <br>

    @if ($datatable->hasResults())

        <p>{!! $datatable->renderMeta() !!}</p>

        <table class="table">
            {!! $datatable->renderHeader('table') !!}
            <tbody>
            @foreach ($datatable->rows() as $row)

                <tr class="@if ($row->archived) archived @endif @if ($bids->leadHasRecent($row->id, $recentDate)) has_bids @endif">
                    <td>
                        <a href="{{ route('leads::show', ['id' => $row->id]) }}">{{ $row->company }}</a>
                    </td>
                    <td class="text-center">
                        @if ($row->bid_count > 0)
                        <a href="{{ route('bids::home').'?lead='. $row->id }}">
                            {{ (int)$row->bid_count }}
                        </a>
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $row->contact_name }}</td>
                    <td>
                        @if ((int)$row->status === \App\Lead::BID_ACCEPTED && is_object($row->acceptedBid()))
                            <a href="{{ route('bids::show', ['id' => $row->acceptedBid()->id]) }}">Bid Accepted</a>
                        @else
                            {{ $row->status() }}
                        @endif
                    </td>
                    <td>{{ $row->hauler_name }}</td>
                    <td>@if ($row->serviceArea !== null){{ $row->serviceArea->name }} @endif</td>
                    <td class="hidden-xs">{{ date('M j, Y', strtotime($row->created_at)) }}</td>
                    <td class="hidden-xs">${{ number_format($row->monthly_price, 2) }}</td>
                    <td>{{ $row->cheapestBid() }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Modify <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('leads::show', ['id' => $row->id]) }}">Details</a>
                                </li>
                                <li>
                                @if ($row->archived)
                                    <a href="{{ route('leads::unarchive', ['id' => $row->id]) }}">UN-Archive</a>
                                @else
                                    <a href="{{ route('leads::archive', ['id' => $row->id]) }}" onClick="return confirm('Archive this Lead?');">
                                        Archive
                                    </a>
                                @endif
                                </li>
                                <li>
                                    <a href="{{ route('leads::delete', ['id' => $row->id]) }}" onClick="return confirm('Delete this Lead permanently?');">
                                        Delete
                                    </a>
                                </li>
                            </ul>
                            @if ($row->status == \App\Lead::BID_ACCEPTED)
                                <a href="{{ route('leads::convert', ['id' => $row->id]) }}" class="btn btn-success btn-xs btn-convert" title="Convert to Client"
                                    onclick="return confirm('Convert this Lead into a Client?');">
                                    Convert
                                </a>
                            @elseif ($row->status == \App\Lead::CONVERTED_TO_CLIENT)
                                <a href="{{ route('leads::rebid', ['id' => $row->id]) }}" class="btn btn-warning btn-xs btn-rebid" title="Rebid this Lead"
                                   onclick="return confirm('Rebid this lead?');">
                                    Rebid
                                </a>
                            @endif
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
