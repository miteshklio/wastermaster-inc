@extends('templates.master')

@section('title')
    <title>Wastemaster | New Hauler</title>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            @if(request()->is('admin/hauler'))
                <h2>Create New Hauler</h2>
            @else
                <h2>Update Hauler</h2>
            @endif

                @if(request()->is('admin/hauler'))
                    <form action="{{ route('haulers::create') }}" method="post" class="">
                @else
                    <form action="{{ route('haulers::update', ['id' => $hauler->id]) }}" method="post" class="">
                @endif

                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" name="name" value="{{ $hauler->name or old('name') }}" autofocus required />
                </div>

                <!-- Service Area -->
                <div class="form-group">
                    <label for="service_area_id">Service Area:</label>
                    <select name="service_area_id" class="form-control" @if (isset($hauler) && $hauler->archived) disabled @endif>
                        <option value="0">Select a Service Area...</option>
                        @if ($serviceAreas)
                            @foreach ($serviceAreas as $area)
                                <option value="{{ $area->id }}" @if (isset($hauler) && $hauler->service_area_id == $area->id) selected @endif>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="row">
                    <div class="col-sm-3 col-xs-12">
                        <div class="checkbox">
                            <label for="recycle">
                                <input type="checkbox" name="recycle" @if (old('recycle', isset($hauler) ? $hauler->svc_recycle : null)) checked @endif> REC
                            </label>
                        </div>
                    </div>

                    <div class="col-sm-3 col-xs-12">
                        <div class="checkbox">
                            <label for="waste">
                                <input type="checkbox" name="waste" @if (old('recycle', isset($hauler) ? $hauler->svc_waste : null)) checked @endif> MSW
                            </label>
                        </div>
                    </div>
                </div>

                <br>

                <div class="form-group">
                    <label for="emails">Emails</label>
                    <textarea name="emails"  rows="3" class="form-control">{{ isset($hauler) ? $hauler->listEmails() : old('emails') }}</textarea>
                    <p class="small">Separate multiple addresses by a comma.</p>
                </div>

                <br>

                <div class="text-center">
                    @if(request()->is('admin/hauler'))
                        <input type="submit" class="btn btn-success" value="Create Hauler">
                    @else
                        <input type="submit" class="btn btn-success" value="Save Hauler">
                    @endif
                </div>

            </form>
        </div>
    </div>

@endsection

@section('scripts')
    @include('app.admin.shared._city_script')
@endsection
