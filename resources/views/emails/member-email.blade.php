<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailData['subject'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: {{ $hotel->email_secondary_color ?? '#f8f9fa' }};
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            border-bottom: 3px solid {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        .header h1 {
            color: {{ $hotel->email_primary_color ?? '#1976d2' }};
            margin: 0 0 10px 0;
        }
        .header .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: {{ $hotel->email_secondary_color ?? '#f8f9fa' }};
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        .member-info {
            background-color: {{ $hotel->email_accent_color ?? '#e3f2fd' }};
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        .member-info h3 {
            margin: 0 0 10px 0;
            color: {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        .member-info p {
            margin: 5px 0;
        }
        .email-content {
            margin: 20px 0;
        }
        .email-content h1, .email-content h2, .email-content h3 {
            color: {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        .email-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }
        .email-content table, .email-content th, .email-content td {
            border: 1px solid #ddd;
        }
        .email-content th, .email-content td {
            padding: 8px;
            text-align: left;
        }
        .email-content th {
            background-color: {{ $hotel->email_secondary_color ?? '#f2f2f2' }};
        }
        .email-content ul, .email-content ol {
            margin: 15px 0;
            padding-left: 20px;
        }
        .email-content a {
            color: {{ $hotel->email_primary_color ?? '#1976d2' }};
            text-decoration: none;
        }
        .email-content a:hover {
            text-decoration: underline;
        }
        .email-content img {
            max-width: 100%;
            height: auto;
        }
        .signature {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
        .signature strong {
            color: {{ $hotel->email_primary_color ?? '#1976d2' }};
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        @if($hotel->email_logo_url)
            <img src="{{ $hotel->email_logo_url }}" alt="{{ $hotel->name }}" class="logo">
        @endif
        <h1>{{ $hotel->name }}</h1>
        <p>{{ $hotel->email_subtitle ?? 'Member Communication' }}</p>
    </div>
    
    <div class="content">
        <div class="member-info">
            <h3>Dear {{ $member->first_name }}{{ $member->last_name ? ' ' . $member->last_name : '' }},</h3>
            @if(!$isCustomRecipient)
                <p><strong>Membership ID:</strong> {{ $member->membership_id }}</p>
                <p><strong>Membership Type:</strong> {{ $member->membershipType->name ?? 'N/A' }}</p>
            @else
                <p><strong>Email:</strong> {{ $member->email }}</p>
            @endif
        </div>
        
        <div class="email-content">
            {!! $emailData['content'] !!}
        </div>
        
        <p>Thank you for being part of our community!</p>
        
        <div class="signature">
            <p>Best regards,<br>
            <strong>{{ $hotel->name }} Team</strong></p>
        </div>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $member->email }} on {{ $emailData['sent_at']->format('M d, Y \a\t g:i A') }}</p>
        @if(!$isCustomRecipient)
            <p>If you have any questions, please contact us.</p>
        @else
            <p>You received this email as a custom recipient. If you have any questions, please contact {{ $emailData['hotel_name'] }}.</p>
        @endif
        <p>&copy; {{ date('Y') }} {{ $emailData['hotel_name'] }}. All rights reserved.</p>
    </div>
</body>
</html>
