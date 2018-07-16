<h2>Send Emails</h2>

<br>

@if (isset($lead) && $lead->created_at)

    @include('app.admin.leads._haulers')

    @include('app.admin.leads._pre_bid')

    @include('app.admin.leads._post_bid')
@else

    <p class="notice">Submit the lead information form on the left to automate bid request emails.</p>

@endif


