@extends('templates.master')

@section('title')
    <title>Wastemaster | Service Area</title>
@stop

@section('content')
    @if(! $editMode)
        <form action="{{ route('areas::create') }}" method="post" class="form-horizontal">
    @else
        <form action="{{ route('areas::update', ['id' => $area->id]) }}" method="post" class="form-horizontal">
    @endif
        {{ csrf_field() }}

    <div class="row">

        <!-- Details Column -->
        <div class="col-sm-8">
            @if(! $editMode)
                <h2>Create New Service Area</h2>
            @else
                <h2>Update Service Area</h2>
            @endif

            <br><br>

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="control-label col-sm-4">Area Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="name" value="{{ $area->name or old('bane') }}" autofocus required />
                    </div>
                </div>

        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-4 col-sm-offset-6">
            @if(! $editMode)
                <input type="submit" class="btn btn-success" value="Create Service Area" >
            @else
                <input type="submit" class="btn btn-success" value="Save Service Area">
            @endif
        </div>
    </div>
@endsection
