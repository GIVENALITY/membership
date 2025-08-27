<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - {{ $event->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .event-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .event-details h3 {
            color: #555;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .value {
            color: #333;
        }
        .registration-code {
            background: #e3f2fd;
            border: 2px solid #2196F3;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #1976D2;
        }
        .message {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .hotel-name {
            color: #888;
            font-size: 14px;
            margin-top: 20px;
        }
        
        .hotel-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }
        
        .event-poster {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .poster-img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hotel Logo -->
        @if($hotel->logo)
            <div class="hotel-logo">
                <img src="{{ asset('storage/' . $hotel->logo) }}" alt="{{ $hotel->name }}" class="logo-img">
            </div>
        @endif
        
        <!-- Event Poster -->
        @if($event->image)
            <div class="event-poster">
                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="poster-img">
            </div>
        @endif
        
        <div class="success-icon">✓</div>
        
        <h1>Registration Successful!</h1>
        
        <p class="message">
            Thank you for registering for our event. Your registration has been confirmed and you will receive a confirmation email shortly.
        </p>

        <div class="event-details">
            <h3>Event Details</h3>
            <div class="detail-row">
                <span class="label">Event:</span>
                <span class="value">{{ $event->title }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Date:</span>
                <span class="value">{{ $event->start_date ? $event->start_date->format('F j, Y') : 'TBD' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Time:</span>
                <span class="value">{{ $event->start_date ? $event->start_date->format('g:i A') : 'TBD' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Location:</span>
                <span class="value">{{ $event->location ?? 'TBD' }}</span>
            </div>
        </div>

        <div class="registration-code">
            Registration Code: {{ $registration->registration_code }}
        </div>

        <p class="message">
            Please keep this registration code for your records. You may be asked to provide it when checking in at the event.
        </p>

        <div class="hotel-name">
            {{ $hotel->name }}
        </div>
    </div>
</body>
</html>
