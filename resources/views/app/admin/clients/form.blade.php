@extends('templates.master')

@section('title')
    <title>Wastemaster | New Hauler</title>
@stop

@section('content')
@if(! $editMode)
    <form action="{{ route('clients::create') }}" method="post" class="form-horizontal">
@else
    <form action="{{ route('clients::update', ['id' => $client->id]) }}" method="post" class="form-horizontal">
@endif

    <div class="row">

        <!-- Details Column -->
        <div class="col-sm-5">
            @if(! $editMode)
                <h2>Create New Client</h2>
            @else
                <h2>Update Client</h2>
            @endif

                <br>

                {{ csrf_field() }}

                @if ($editMode)
                    <!-- Time Created -->
                    <div class="form-group">
                        <label for="name" class="control-label col-sm-4">Time Created</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="created_at" value="{{ date('M j, Y g:ia', strtotime($client->created_at)) }}" disabled />
                        </div>
                    </div>
                @endif

                <!-- Company Name -->
                <div class="form-group">
                    <label for="name" class="control-label col-sm-4">Business Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="company" value="{{ $client->company or old('company') }}" autofocus required @if (isset($client) && $client->archived == 1) disabled @endif />
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="control-label col-sm-4">Address</label>
                    <div class="col-sm-8">
                        <textarea name="address"  rows="4" class="form-control" required @if (isset($client) && $client->archived) disabled @endif>{{ $client->address or old('address') }}</textarea>
                    </div>
                </div>

                <!-- Service Area -->
                <div class="form-group">
                    <label for="service_area_id" class="control-label col-sm-4">Service Area:</label>
                    <div class="col-sm-8">
                        <select name="service_area_id" class="form-control" @if (isset($client) && $client->archived) disabled @endif>
                            <option value="0">Select a Service Area...</option>
                            @if ($serviceAreas)
                                @foreach ($serviceAreas as $area)
                                    <option value="{{ $area->id }}" @if (isset($client) && $client->service_area_id == $area->id) selected @endif>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Contact Name -->
                <div class="form-group">
                    <label for="contact_name" class="control-label col-sm-4">Contact Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="contact_name" value="{{ $client->contact_name or old('contact_name') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>

                <!-- Contact Email -->
                <div class="form-group">
                    <label for="contact_email" class="control-label col-sm-4">Contact Email</label>
                    <div class="col-sm-8">
                        <input type="email" class="form-control" name="contact_email" value="{{ $client->contact_email or old('contact_email') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>

                <!-- Account Number -->
                <div class="form-group">
                    <label for="account_num" class="control-label col-sm-4">Account Number</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="account_num" value="{{ $client->account_num or old('account_num') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>

                <!-- Current Hauler -->
                <div class="form-group">
                    <label for="hauler_id" class="control-label col-sm-4">Current Hauler</label>
                    <div class="col-sm-8">
                        <select name="hauler_id" class="form-control" @if (isset($client) && $client->archived) disabled @endif>
                            <option value="0">Select a Hauler...</option>
                        @if ($haulers)
                            @foreach ($haulers as $hauler)
                                    <option value="{{ $hauler->id }}" @if (isset($client) && $client->hauler_id == $hauler->id) selected @endif>
                                        {{ $hauler->name }}
                                    </option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                </div>

                <!-- Services -->
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-center">#</th>
                            <th class="text-center">yd</th>
                            <th class="text-center">#/wk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Waste -->
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">MSW</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="msw_qty" class="form-control" value="{{ old('msw_qty', $client->msw_qty ?? 0) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="msw_yards" class="form-control" value="{{ number_format($client->msw_yards ?? old('msw_yards'),1 ) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="msw_per_week" class="form-control" value="{{ number_format($client->msw_per_week ?? old('msw_per_week'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">MSW2</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="msw2_qty" class="form-control" value="{{ old('msw2_qty', $client->msw2_qty ?? 0) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="msw2_yards" class="form-control" value="{{ number_format($client->msw2_yards ?? old('msw2_yards'),1 ) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="msw2_per_week" class="form-control" value="{{ number_format($client->msw2_per_week ?? old('msw2_per_week'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                        </tr>
                        <!-- Recycling -->
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">REC</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="rec_qty" class="form-control" value="{{ old('rec_qty', $client->rec_qty ?? 0) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1"  name="rec_yards" class="form-control" value="{{ number_format($client->rec_yards ?? old('rec_yards'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="rec_per_week" class="form-control" value="{{ number_format($client->rec_per_week ?? old('rec_per_week'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">REC2</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="rec2_qty" class="form-control" value="{{ old('rec2_qty', $client->rec2_qty ?? 0) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1"  name="rec2_yards" class="form-control" value="{{ number_format($client->rec2_yards ?? old('rec2_yards'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                            <td>
                                <input type="number" step=".1" name="rec2_per_week" class="form-control" value="{{ number_format($client->rec2_per_week ?? old('rec2_per_week'),1) }}" @if (isset($client) && $client->archived) disabled @endif>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>


        <div class="col-sm-4 col-sm-offset-1 text-center">

            <h2>Price Breakdown</h2>

            <!-- Prior Total -->
            <div class="form-group">
                <label for="contact_name" class="control-label col-sm-5">Prior Total</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="prior_total" value="{{ $client->prior_total or old('prior_total') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Monthly MSW -->
            <div class="form-group">
                <label for="msw_price" class="control-label col-sm-5">Monthly MSW</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="msw_price" id="msw_price" value="{{ $client->msw_price or old('msw_price') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Monthly REC -->
            <div class="form-group">
                <label for="rec_price" class="control-label col-sm-5">Monthly REC</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="rec_price" id="rec_price" value="{{ $client->rec_price or old('rec_price') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Recycle Offset Fee -->
            <div class="form-group">
                <label for="rec_offset" class="control-label col-sm-5">REC Offset Fee</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="rec_offset" id="rec_offset" value="{{ $client->rec_offset or old('rec_offset') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Fuel Surcharge -->
            <div class="form-group">
                <label for="fuel_surcharge" class="control-label col-sm-5">Fuel Surcharge</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="fuel_surcharge" id="fuel_surcharge" value="{{ $client->fuel_surcharge or old('fuel_surcharge') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Env Surcharge -->
            <div class="form-group">
                <label for="env_surcharge" class="control-label col-sm-5">Environmental Surcharge</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="env_surcharge" id="env_surcharge" value="{{ $client->env_surcharge or old('env_surcharge') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Regulatory Cost Recovery -->
            <div class="form-group">
                <label for="recovery_fee" class="control-label col-sm-5">Regulatory Cost Recovery</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="recovery_fee" id="recovery_fee" value="{{ $client->recovery_fee or old('recovery_fee') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Admin Fee -->
            <div class="form-group">
                <label for="admin_fee" class="control-label col-sm-5">Admin Fee</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="admin_fee" id="admin_fee" value="{{ $client->admin_fee or old('admin_fee') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Other Fees -->
            <div class="form-group">
                <label for="other_fees" class="control-label col-sm-5">Other Fees (Lock, etc)</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control fee" name="other_fees" id="other_fees" value="{{ $client->other_fees or old('other_fees') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Net Monthly -->
            <div class="form-group">
                <label for="net_monthly" class="control-label col-sm-5">Net Monthly</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="net_monthly" id="net_monthly" value="{{ $client->net_monthly or old('net_monthly') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="form-group">
                <label for="gross_profit" class="control-label col-sm-5">Gross Profit</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="gross_profit" id="gross_profit" value="{{ $client->gross_profit or old('gross_profit') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="form-group">
                <label for="total" class="control-label col-sm-5">Total</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <input type="text" class="form-control number" name="total" id="total" value="{{ $client->total or old('total') }}" required @if (isset($client) && $client->archived) disabled @endif />
                    </div>
                </div>
            </div>


        </div>
    </div>

        <br>

    <div class="row">
        <div class="text-center">
            @if(request()->is('admin/hauler'))
                <input type="submit" class="btn btn-success" value="Create Client" @if (isset($client) && $client->archived) disabled @endif>
            @else
                <input type="submit" class="btn btn-success" value="Save Client" @if (isset($client) && $client->archived) disabled @endif>
            @endif
        </div>
    </div>

</form>
@endsection

@section('scripts')
    @include('app.admin.shared._city_script')
    <script>
        // Calculates the Net monthly costs and updates the form.
        $('.fee').change(function(){
            var mswPrice = parseFloat($('#msw_price').val());
            var recPrice = parseFloat($('#rec_price').val());
            var recOffset = parseFloat($('#rec_offset').val());
            var fuelSurcharge = parseFloat($('#fuel_surcharge').val());
            var envSurcharge = parseFloat($('#env_surcharge').val());
            var recovery = parseFloat($('#recovery_fee').val());
            var admin = parseFloat($('#admin_fee').val());
            var other = parseFloat($('#other_fees').val());

            var net =
                (mswPrice ? mswPrice : 0)+
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
        $('.fee, .number').change(function(){
            var amount = parseFloat($(this).val())

            $(this).val(amount ? amount.toFixed(2) : 0.00);
        });

        // Update total amount
        $('#net_monthly, #gross_profit').change(function(){
            var net   = parseFloat($('#net_monthly').val());
            var gross = parseFloat($('#gross_profit').val());

            var total = (net ? net : 0) + (gross ? gross : 0);

            $('#total').val(total ? total.toFixed(2) : 0.00);
        });
    </script>
@endsection
