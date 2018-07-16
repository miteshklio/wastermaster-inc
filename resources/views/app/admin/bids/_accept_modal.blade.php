<div class="modal fade" tabindex="-1" role="dialog" id="accept-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Accept {{ $hauler->name }}'s bid for {{ $lead->company }}</h4>
            </div>

            <div class="modal-body">
                <form action="{{ route('bids::accept', ['id' => $bid->id]) }}" method="post">

                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <div class="form-group">
                                <label for="gross" class="text-center">Gross Profit</label>
                                <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    <input type="text" name="gross" id="gross" class="form-control profit" value="{{ $gross }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <?php $colSize = ! empty($lead->gross_profit) ? 3 : 4; ?>
                    <div class="row">
                        <div class="col-sm-{{ $colSize }} text-center">
                            <p>Currently Paying</p>

                            <h3>${{ number_format($lead->monthly_price,2) }}</h3>
                        </div>

                        @if (! empty($lead->gross_profit))
                            <div class="col-sm-{{ $colSize }} text-center">
                                <p>Current Profit</p>

                                <h3>${{ number_format($lead->gross_profit,2) }}</h3>
                            </div>
                        @endif

                        <div class="col-sm-{{ $colSize }} text-center">
                            <p>Bid</p>

                            <h3>$<span id="net">{{ number_format($bid->net_monthly,2) }}</span></h3>
                        </div>

                        <div class="col-sm-{{ $colSize }} text-center">
                            <p>Total w/ GP</p>

                            <h3>$<span id="modal-total">{{ number_format($total,2) }}</span></h3>
                        </div>
                    </div>

                    <br>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Accept</button>
                        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
