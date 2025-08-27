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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
        }
        .member-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .member-info h3 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }
        .member-info p {
            margin: 5px 0;
        }
        .email-content {
            margin: 20px 0;
        }
        .email-content h1, .email-content h2, .email-content h3 {
            color: #1976d2;
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
            background-color: #f2f2f2;
        }
        .email-content ul, .email-content ol {
            margin: 15px 0;
            padding-left: 20px;
        }
        .email-content a {
            color: #1976d2;
            text-decoration: none;
        }
        .email-content a:hover {
            text-decoration: underline;
        }
        .email-content img {
            max-width: 100%;
            height: auto;
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
        <h1>{{ $emailData['hotel_name'] }}</h1>
        <p>Member Communication</p>
    </div>
    
    <div class="content">
        <div class="member-info">
            <h3>Dear {{ $member->first_name }} {{ $member->last_name }},</h3>
            <p><strong>Membership ID:</strong> {{ $member->membership_id }}</p>
            <p><strong>Membership Type:</strong> {{ $member->membershipType->name ?? 'N/A' }}</p>
        </div>
        
        <div class="email-content">
            {!! $emailData['content'] !!}
        </div>
        
        <p>Thank you for being part of our community!</p>
        
        <p>Best regards,<br>
        <strong>{{ $emailData['hotel_name'] }} Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $member->email }} on {{ $emailData['sent_at']->format('M d, Y \a\t g:i A') }}</p>
        <p>If you have any questions, please contact us.</p>
        <p>&copy; {{ date('Y') }} {{ $emailData['hotel_name'] }}. All rights reserved.</p>
    </div>
</body>
</html>
