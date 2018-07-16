@extends('templates.master')

@section('title')
    <title>Wastemaster | Bids</title>
@stop

@section('content')
    <br><br>

    <div class="row">
        <div class="col-sm-9">
            <h2 style="margin-top: 0">Manage Bids</h2>
        </div>
        <div class="col-sm-3 tex-right">
            {!! $datatable->renderSearch() !!}
        </div>
    </div>

    <br>

    @if ($datatable->hasResults())

        <p>
            {!! $datatable->renderMeta() !!}
            @if (isset($_GET['lead']))
                &nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ route('bids::home') }}">Show for all Leads</a>
            @endif
        </p>

        <table class="table">
            {!! $datatable->renderHeader('table') !!}
            <tbody>
            @foreach ($datatable->rows() as $row)
                <tr class="@if ($row->status == \App\Bid::STATUS_CLOSED) archived @endif @if (strtotime($row->created_at) + 10 > $recentDate)) has_bids @endif">
                    <td>
                        <a href="{{ route('bids::show', ['id' => $row->id]) }}">{{ $row->lead_name }}</a>
                    </td>
                    <td>{{ $row->describeStatus() }}</td>
                    <td>
                        {{ $row->hauler_name }}
                        <?php $lead = $row->lead; $hauler = $lead->hauler ?? null; ?>
                        @if (!empty($lead->hauler) && $hauler->name == $row->hauler_name)
                            <img src="/img/star.png" class="hauler_star" alt="Current Hauler">
                        @endif
                    </td>
                    <td class="hidden-xs">{{ date('M j, Y', strtotime($row->created_at)) }}</td>
                    <td class="hidden-xs">${{ number_format($row->current_total, 2) }}</td>
                    <td>${{ number_format($row->net_monthly, 2) }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Modify <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('bids::show', ['id' => $row->id]) }}">Details</a>
                                </li>
                                <li>
                                @if ($row->status == \App\Bid::STATUS_LIVE)
                                    <a href="#" class="accept" data-id="{{ $row->id }}">
                                        Accept
                                    </a>
                                @elseif ($row->status == \App\Bid::STATUS_ACCEPTED)
                                    <a href="{{ route('bids::rescind', ['id' => $row->id]) }}" onClick="return confirm('Rescind this bid?');">
                                        Rescind
                                    </a>
                                @endif
                                </li>
                                <li>
                                    <a href="{{ route('bids::delete', ['id' => $row->id]) }}" onClick="return confirm('Delete this Bid permanently?');">
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

    <div id="modal-wrap"></div>
@endsection

@section('scripts')
    <script>
        $('a.accept').click(function(el){
            el.preventDefault();

            var bidID = $(this).attr('data-id');

            // Load the customized modal
            $('#modal-wrap').load(
                '/admin/bid/'+bidID+'/get_accept_modal',
                function()
                {
                    $('#accept-modal').modal().modal('show');
                }
            )
        });

        $('body').on('keyup', '.profit', function()
        {
            var net = $('#net').text();
            var profit = $('#gross').val();

            $('#modal-total').text(parseFloat(net) + parseFloat(profit));
        });
    </script>
@endsection
