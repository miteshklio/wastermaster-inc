@extends('templates.master')

@section('title')
    <title>Wastemaster | New Hauler</title>
@stop

@section('content')
    <div class="row">

        <!-- Details Column -->
        <div class="col-sm-5">
            @if(! $editMode)
                <h2>Create New Lead</h2>
            @else
                <h2>Update Lead</h2>
            @endif

                <br>

            @if(isset($lead) && $lead->archived)
                <div class="alert alert-warning">
                    This Lead has been archived.
                </div>
            @endif

            @if(! $editMode)
                <form action="{{ route('leads::create') }}" method="post" class="form-horizontal">
            @else
                <form action="{{ route('leads::update', ['id' => $lead->id]) }}" method="post" class="form-horizontal">
            @endif

                {{ csrf_field() }}

                @if ($editMode)
                    <!-- Time Created -->
                    <div class="form-group">
                        <label for="name" class="control-label col-sm-4">Time Created</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="created_at" value="{{ date('M j, Y g:ia', strtotime($lead->created_at)) }}" disabled />
                        </div>
                    </div>
                @endif

                <!-- Company Name -->
                <div class="form-group">
                    <label for="name" class="control-label col-sm-4">Business Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="company" value="{{ $lead->company or old('company') }}" autofocus required @if (isset($lead) && $lead->archived == 1) disabled @endif />
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="control-label col-sm-4">Address</label>
                    <div class="col-sm-8">
                        <textarea name="address"rows="4" class="form-control" required @if (isset($lead) && $lead->archived) disabled @endif>{{ $lead->address or old('address') }}</textarea>
                    </div>
                </div>

                <!-- Service Area -->
                <div class="form-group">
                    <label for="service_area_id" class="control-label col-sm-4">Service Area:</label>
                    <div class="col-sm-8">
                        <select name="service_area_id" class="form-control" @if (isset($lead) && $lead->archived) disabled @endif>
                            <option value="0">Select a Service Area...</option>
                            @if ($serviceAreas)
                                @foreach ($serviceAreas as $area)
                                    <option value="{{ $area->id }}" @if (isset($lead) && $lead->service_area_id == $area->id) selected @endif>
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
                        <input type="text" class="form-control" name="contact_name" value="{{ $lead->contact_name or old('contact_name') }}" required @if (isset($lead) && $lead->archived) disabled @endif />
                    </div>
                </div>

                <!-- Contact Email -->
                <div class="form-group">
                    <label for="contact_email" class="control-label col-sm-4">Contact Email</label>
                    <div class="col-sm-8">
                        <input type="email" class="form-control" name="contact_email" value="{{ $lead->contact_email or old('contact_email') }}" required @if (isset($lead) && $lead->archived) disabled @endif />
                    </div>
                </div>

                <!-- Account Number -->
                <div class="form-group">
                    <label for="account_num" class="control-label col-sm-4">Account Number</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="account_num" value="{{ $lead->account_num or old('account_num') }}" required @if (isset($lead) && $lead->archived) disabled @endif />
                    </div>
                </div>

                <!-- Current Hauler -->
                <div class="form-group">
                    <label for="hauler_id" class="control-label col-sm-4">Current Hauler</label>
                    <div class="col-sm-8">
                        <select name="hauler_id" class="form-control" @if (isset($lead) && $lead->archived) disabled @endif>
                            <option value="0">Select a Hauler...</option>
                        @if ($haulers)
                            @foreach ($haulers as $hauler)
                                    <option value="{{ $hauler->id }}" @if (isset($lead) && $lead->hauler_id == $hauler->id) selected @endif>
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
                                <input type="text" name="msw_qty" class="form-control" value="{{ old('msw_qty', $lead->msw_qty ?? 0) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->msw_yards ?? old('msw_yards'); ?>
                                <input type="number" step=".1" name="msw_yards" class="form-control" value="{{ number_format($amount, 1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->msw_per_week ?? old('msw_per_week'); ?>
                                <input type="number" step=".1" name="msw_per_week" class="form-control" value="{{ number_format($amount, 1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">MSW2</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="msw2_qty" class="form-control" value="{{ old('msw2_qty', $lead->msw2_qty ?? 0) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->msw2_yards ?? old('msw2_yards'); ?>
                                <input type="number" step=".1" name="msw2_yards" class="form-control" value="{{ number_format($amount, 1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->msw2_per_week ?? old('msw2_per_week'); ?>
                                <input type="number" step=".1" name="msw2_per_week" class="form-control" value="{{ number_format($amount, 1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
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
                                <input type="text" name="rec_qty" class="form-control" value="{{ old('rec_qty', $lead->rec_qty ?? 0) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->rec_yards ?? old('rec_yards'); ?>
                                <input type="number" step=".1" name="rec_yards" class="form-control" value="{{ number_format($amount,1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->rec_per_week ?? old('rec_per_week'); ?>
                                <input type="number" step=".1" name="rec_per_week" class="form-control" value="{{ number_format($amount,1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <label class="form-inline control-label">REC2</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="rec2_qty" class="form-control" value="{{ old('rec2_qty', $lead->rec2_qty ?? 0) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->rec2_yards ?? old('rec2_yards'); ?>
                                <input type="number" step=".1" name="rec2_yards" class="form-control" value="{{ number_format($amount,1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                            <td>
                                <?php $amount = $lead->rec2_per_week ?? old('rec2_per_week'); ?>
                                <input type="number" step=".1" name="rec2_per_week" class="form-control" value="{{ number_format($amount,1) }}" @if (isset($lead) && $lead->archived) disabled @endif>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Notes -->
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="notes">Notes</label>
                        <textarea name="notes" class="form-control" rows=6 @if (isset($lead) && $lead->archived) disabled @endif>{{ $lead->notes or old('notes') }}</textarea>
                    </div>
                </div>


                <!-- Total Monthly -->
                <div class="form-group">
                    <label for="monthly_price" class="control-label col-sm-4">Total Monthly</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            <input type="text" class="form-control" name="monthly_price" value="{{ $lead->monthly_price or old('monthly_price') }}" required @if (isset($lead) && $lead->archived) disabled @endif />
                        </div>
                    </div>
                </div>

                <br>

                <div class="text-center">
                    @if(request()->is('admin/hauler'))
                        <input type="submit" name="submit" class="btn btn-success" value="Create Lead" @if (isset($lead) && $lead->archived) disabled @endif>
                    @else
                        <input type="submit" name="submit" class="btn btn-success" value="Save Lead" @if (isset($lead) && $lead->archived) disabled @endif>
                    @endif
                </div>

            </form>
        </div>


        <div class="col-sm-3 col-sm-offset-2 text-center">

            @include('app.admin.leads._send_emails')

        </div>
    </div>

@endsection

@section('scripts')
    @include('app.admin.shared._city_script')
@endsection
