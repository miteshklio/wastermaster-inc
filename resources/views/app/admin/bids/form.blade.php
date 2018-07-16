@extends('templates.master')

@section('title')
    <title>Wastemaster | View Bid</title>
@stop

@section('content')
    <form action="{{ route('bids::update', ['id' => $bid->id]) }}" method="post" class="form-horizontal">

    <div class="row">

        <!-- Details Column -->
        <div class="col-sm-5">
            <h2>Update Bid</h2>

                <br>

                {{ csrf_field() }}

            <!-- Bidding Company -->
            <div class="form-group">
                <label for="name" class="control-label col-sm-4">Bidder</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="company" value="{{ $bid->hauler->name }}"  disabled />
                </div>
            </div>

            <!-- Bidding Email -->
            <div class="form-group">
                <label for="hauler_email" class="control-label col-sm-4">Bid Email</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="hauler_email" value="{{ $bid->hauler_email }}"  disabled />
                </div>
            </div>

            <!-- Lead Name -->
            <div class="form-group">
                <label for="lead_name" class="control-label col-sm-4">Lead Name</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="lead_name" value="{{ $bid->lead->company }}"  disabled />
                </div>
            </div>

            <!-- Date Received -->
            <div class="form-group">
                <label for="name" class="control-label col-sm-4">Date Received</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="created_at" value="{{ date('M j, Y g:ia', strtotime($bid->created_at)) }}" disabled />
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" class="control-label col-sm-4">Status</label>
                <div class="col-sm-8">
                    <select name="status" class="form-control" disabled>
                        <option value="{{ \App\Bid::STATUS_LIVE }}" @if ($bid->status == \App\Bid::STATUS_LIVE) selected @endif>Live</option>
                        <option value="{{ \App\Bid::STATUS_ACCEPTED }}" @if ($bid->status == \App\Bid::STATUS_ACCEPTED) selected @endif>Accepted</option>
                        <option value="{{ \App\Bid::STATUS_CLOSED }}" @if ($bid->status == \App\Bid::STATUS_CLOSED) selected @endif>Closed</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label for="notes" class="control-label col-sm-4">Notes</label>
                <div class="col-sm-8">
                    <textarea name="notes" class="form-control" disabled rows="6">{{ $bid->notes }}</textarea>
                </div>
            </div>
        </div>


        <div class="col-sm-4 col-sm-offset-1 text-center">

            <h2>Offer</h2>

            <!-- Prior Total -->
            <div class="form-group">
                <label for="contact_name" class="control-label col-sm-5">Prior Total</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="prior_total" value="{{ $bid->lead->monthly_price }}" disabled />
                    </div>
                </div>
            </div>

            <!-- Monthly MSW -->
            <div class="form-group">
                <label for="msw_price" class="control-label col-sm-5">Monthly MSW</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="msw_price" id="msw_price" value="{{ $bid->msw_price or old('msw_price') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Monthly REC -->
            <div class="form-group">
                <label for="rec_price" class="control-label col-sm-5">Monthly REC</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="rec_price" id="rec_price" value="{{ $bid->rec_price or old('rec_price') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Recycle Offset Fee -->
            <div class="form-group">
                <label for="rec_offset" class="control-label col-sm-5">REC Offset Fee</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="rec_offset" id="rec_offset" value="{{ $bid->rec_offset or old('rec_offset') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Fuel Surcharge -->
            <div class="form-group">
                <label for="fuel_surcharge" class="control-label col-sm-5">Fuel Surcharge</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="fuel_surcharge" id="fuel_surcharge" value="{{ $bid->fuel_surcharge or old('fuel_surcharge') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Env Surcharge -->
            <div class="form-group">
                <label for="env_surcharge" class="control-label col-sm-5">Environmental Surcharge</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="env_surcharge" id="env_surcharge" value="{{ $bid->env_surcharge or old('env_surcharge') }}" required />
                    </div>
                </div>
            </div>

            <!-- Regulatory Cost Recovery -->
            <div class="form-group">
                <label for="recovery_fee" class="control-label col-sm-5">Regulatory Cost Recovery</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="recovery_fee" id="recovery_fee" value="{{ $bid->recovery_fee or old('recovery_fee') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Admin Fee -->
            <div class="form-group">
                <label for="admin_fee" class="control-label col-sm-5">Admin Fee</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="admin_fee" id="admin_fee" value="{{ $bid->admin_fee or old('admin_fee') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Other Fees -->
            <div class="form-group">
                <label for="other_fees" class="control-label col-sm-5">Other Fees (Lock, etc)</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="other_fees" id="other_fees" value="{{ $bid->other_fees or old('other_fees') }}" required />
                    </div>
                </div>
            </div>

            <!-- Net Monthly -->
            <div class="form-group">
                <label for="net_monthly" class="control-label col-sm-5">Net Monthly</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="net_monthly" id="net_monthly" value="{{ $bid->net_monthly or old('net_monthly') }}" required  />
                    </div>
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="form-group">
                <label for="gross_profit" class="control-label col-sm-5">Gross Profit</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="gross_profit" value="{{ $bid->gross_profit }}" disabled  />
                    </div>
                </div>
            </div>

            <!-- Net Monthly -->
            <div class="form-group">
                <label for="total" class="control-label col-sm-5">Total</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="total"
                               value="@if(! empty($bid->net_monthly)){{ number_format($bid->net_monthly + $bid->gross_profit, 2) }} @endif" disabled  />
                    </div>
                </div>
            </div>

        </div>
    </div>

        <br>

    <div class="row">
        <div class="text-center">
            <input type="submit" class="btn btn-success" value="Save Bid">
            @if ($bid->status == \App\Bid::STATUS_LIVE)
                <a href="#" class="btn btn-primary" id="accept" data-id="{{ $bid->id }}">Accept Bid</a>
            @endif
        </div>
    </div>

</form>

<div id="modal-wrap"></div>
@endsection

@section('scripts')
    <script>
        // Calculates the Net monthly costs and updates the form.
        $('.fee').change(function () {
            var mswPrice = parseFloat($('#msw_price').val());
            var recPrice = parseFloat($('#rec_price').val());
            var recOffset = parseFloat($('#rec_offset').val());
            var fuelSurcharge = parseFloat($('#fuel_surcharge').val());
            var envSurcharge = parseFloat($('#env_surcharge').val());
            var recovery = parseFloat($('#recovery_fee').val());
            var admin = parseFloat($('#admin_fee').val());
            var other = parseFloat($('#other_fees').val());

            var net =
                (mswPrice ? mswPrice : 0) +
                (recPrice ? recPrice : 0) +
                (recOffset ? recOffset : 0) +
                (fuelSurcharge ? fuelSurcharge : 0) +
                (envSurcharge ? envSurcharge : 0) +
                (recovery ? recovery : 0) +
                (admin ? admin : 0) +
                (other ? other : 0);

            $('#net_monthly').val(net.toFixed(2)).trigger('change');
        });

        // Format the fee and update the Net Value
        $('.fee, .number').change(function () {
            var amount = parseFloat($(this).val())

            $(this).val(amount ? amount.toFixed(2) : 0.00);
        });

        /*
         Accept Bid Modal
         */
        $('#accept').click(function (el) {
            el.preventDefault();

            var bidID = $(this).attr('data-id');

            // Load the customized modal
            $('#modal-wrap').load(
                '/admin/bid/' + bidID + '/get_accept_modal',
                function () {
                    $('#accept-modal').modal().modal('show');
                }
            )
        });

        $('body').on('keyup', '.profit', function () {
            var net = $('#net').text();
            var profit = $('#gross').val();

            $('#modal-total').text(parseFloat(net) + parseFloat(profit));
        });
    </script>
@endsection

