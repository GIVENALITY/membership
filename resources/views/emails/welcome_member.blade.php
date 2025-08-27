<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $member->hotel->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            background-color: {{ $member->hotel->email_secondary_color ?? '#f8f9fa' }};
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            border-bottom: 4px solid {{ $member->hotel->email_primary_color ?? '#1976d2' }};
        }
        .header h1 {
            color: {{ $member->hotel->email_primary_color ?? '#1976d2' }};
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header .logo {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .content {
            background-color: #ffffff;
            padding: 40px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: {{ $member->hotel->email_secondary_color ?? '#f8f9fa' }};
            padding: 25px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 14px;
            color: #6c757d;
            border-top: 1px solid {{ $member->hotel->email_primary_color ?? '#1976d2' }};
        }
        .member-info {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border: 2px solid {{ $member->hotel->email_primary_color ?? '#1976d2' }};
        }
        .member-info h3 {
            margin: 0 0 15px 0;
            color: #ffffff;
            font-size: 20px;
        }
        .member-info p {
            margin: 8px 0;
            font-size: 16px;
        }
        .member-info strong {
            color: {{ $member->hotel->email_primary_color ?? '#1976d2' }};
        }
        .perks-section {
            margin: 30px 0;
        }
        .perks-section h3 {
            color: {{ $member->hotel->email_primary_color ?? '#1976d2' }};
            margin-bottom: 15px;
            font-size: 20px;
        }
        .perks-section ul {
            margin: 0;
            padding-left: 20px;
        }
        .perks-section li {
            margin: 8px 0;
            font-size: 16px;
        }
        .welcome-message {
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        .welcome-message p {
            margin: 0 0 20px 0;
        }
        .welcome-message p:last-child {
            margin-bottom: 0;
        }
        .card-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        .card-notice i {
            color: #856404;
            font-size: 20px;
            margin-right: 8px;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 25px;
            }
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        @if($member->hotel->email_logo_url)
            <img src="{{ $member->hotel->email_logo_url }}" alt="{{ $member->hotel->name }}" class="logo">
        @endif
        <h1>Welcome to {{ $member->hotel->name }}</h1>
        <p style="margin: 0; font-size: 16px; color: #666;">{{ $member->hotel->email_subtitle ?? 'Exclusive Membership Program' }}</p>
    </div>
    
    <div class="content">
        <div class="welcome-message">
            @php
                $paragraphs = explode("\n\n", $bodyText);
            @endphp
            @foreach($paragraphs as $paragraph)
                @if(trim($paragraph))
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>

        <div class="member-info">
            <h3>Your Membership Details</h3>
            <p><strong>Member Name:</strong> {{ $member->full_name }}</p>
            <p><strong>Membership ID:</strong> {{ $member->membership_id }}</p>
            <p><strong>Membership Type:</strong> {{ optional($member->membershipType)->name ?? 'Standard' }}</p>
            <p><strong>Expires:</strong> {{ $member->expires_at ? $member->expires_at->toFormattedDateString() : 'N/A' }}</p>
        </div>

        @php($perks = optional($member->membershipType)->perks ?? [])
        @if(!empty($perks))
            <div class="perks-section">
                <h3>Your Exclusive Benefits</h3>
                <ul>
                    @foreach($perks as $perk)
                        <li>{{ $perk }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-notice">
            <i>🎫</i>
            <strong>Your membership card is attached to this email.</strong><br>
            <small>Please save it to your device for easy access during your visits.</small>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 0 0 10px 0;">
            <strong>{{ $member->hotel->name }}</strong><br>
            {{ $member->hotel->address ?? '' }}<br>
            {{ $member->hotel->city ?? '' }}, {{ $member->hotel->country ?? '' }}
        </p>
        <p style="margin: 0; font-size: 12px;">
            Thank you for choosing {{ $member->hotel->name }}. We look forward to providing you with exceptional service.
        </p>
        <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">
            &copy; {{ date('Y') }} {{ $member->hotel->name }}. All rights reserved.
        </p>
    </div>
</body>
</html> 