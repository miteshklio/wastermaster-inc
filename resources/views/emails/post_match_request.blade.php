<p>Dear {{ $hauler->name }},</p>

<p>Your company has been servicing {{ $lead->company }} at
    {{ $lead->address }} for the last several years.  Thank you.</p>

<p>It recently came to our attention that a different waste hauler is offering a very competitive price
    of ${{ $bid->net_monthly }} for the same service levels.  Will you consider matching this price
    if we sign a new agreement?</p>

<p>If so, please fill out this form: <a href="{{ $form_url }}">{{ $form_url }}</a> within 3 business days.</p>

<p>Thank you.</p>
