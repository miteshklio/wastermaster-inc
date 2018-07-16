@extends('templates.master')

@section('title')
    <title>Wastemaster | Thanks for Bidding</title>
@stop

@section('content')
    <h2 class="text-center">Thanks for your bid!</h2>

    <br><br>

    <p class="text-center">Your bid for {{ $lead->company }} has been submitted and we will follow up with you if it's accepted.</p>

    <br><br>

    <p class="text-center">Made a mistake? Contact us.</p>

    <p class="text-center">
        <a href="mailto://wastemastercorp@gmail.com"><i class="fa fa-envelope"></i> wastemastercorp@gmail.com</a>
    </p>

@endsection
