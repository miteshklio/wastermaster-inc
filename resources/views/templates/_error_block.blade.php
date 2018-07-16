@if( Session::has('message') )
    <div class="alert alert-success container">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p><?php echo Session::get('message'); ?></p>
    </div>
@endif

@if( Session::has('error') )
    <div class="alert alert-danger container">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p><?php echo Session::get('error'); ?></p>
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger container">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </div>
@endif
