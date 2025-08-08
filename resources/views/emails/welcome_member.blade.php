<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Welcome to Membership MS</title>
</head>
<body style="font-family: Arial, sans-serif; color:#222;">
  <h2>Welcome to Membership MS</h2>
  <p>{!! $bodyText !!}</p>

  <hr>
  <p>
    <strong>Member:</strong> {{ $member->full_name }}<br>
    <strong>Membership ID:</strong> {{ $member->membership_id }}<br>
    <strong>Type:</strong> {{ optional($member->membershipType)->name ?? 'N/A' }}<br>
    <strong>Expires:</strong> {{ $member->expires_at ? $member->expires_at->toFormattedDateString() : 'N/A' }}
  </p>

  @php($perks = optional($member->membershipType)->perks ?? [])
  @if(!empty($perks))
    <h3 style="margin-top:24px;">Your Membership Perks</h3>
    <ul>
      @foreach($perks as $perk)
        <li>{{ $perk }}</li>
      @endforeach
    </ul>
  @endif

  <p style="margin-top:24px;">Your membership card is attached to this email.</p>
</body>
</html> 