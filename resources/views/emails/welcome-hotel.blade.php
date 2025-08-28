<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Membership MS</title>
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
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        .hotel-name {
            font-size: 24px;
            color: #000;
            margin-bottom: 10px;
        }
        .welcome-text {
            font-size: 18px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            border-left: 4px solid #000;
            padding-left: 15px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓";
            color: #000;
            font-weight: bold;
            margin-right: 10px;
        }
        .highlight-box {
            background-color: #f8f9fa;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Membership MS</div>
            <div class="hotel-name">{{ $hotel->name }}</div>
            <div class="welcome-text">Welcome to your restaurant loyalty management system!</div>
        </div>

        <div class="section">
            <p>Dear {{ $adminUser->name }},</p>
            <p>Welcome to <strong>Membership MS</strong>! We're excited to help you build a thriving loyalty program for your restaurant. Your account has been successfully created and you're ready to start managing your members.</p>
        </div>

        <div class="section">
            <div class="section-title">🎯 What Membership MS Can Do For You</div>
            <ul class="feature-list">
                <li><strong>Member Management:</strong> Create and manage unlimited member accounts with detailed profiles</li>
                <li><strong>Loyalty Tracking:</strong> Automatically track dining visits and calculate discount rates</li>
                <li><strong>Smart Discounts:</strong> Progressive discount system based on visit frequency (5% to 20%)</li>
                <li><strong>Visit Recording:</strong> Two-step process to record visits and process checkouts</li>
                <li><strong>Receipt Management:</strong> Upload and store receipts for all transactions</li>
                <li><strong>Member Search:</strong> Quick search by name, ID, phone, or email</li>
                <li><strong>Birthday Alerts:</strong> Automatic notifications for member birthdays</li>
                <li><strong>Email Automation:</strong> Welcome emails and birthday greetings</li>
                <li><strong>Membership Cards:</strong> Generate personalized membership cards</li>
                <li><strong>Detailed Reports:</strong> Comprehensive analytics and insights</li>
                <li><strong>Branding Customization:</strong> Upload your logo and customize colors</li>
                <li><strong>Multi-User Access:</strong> Add staff members with different roles</li>
            </ul>
        </div>

        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #000;">🚀 Quick Start Guide</h3>
            <ol>
                <li><strong>Customize Your Brand:</strong> Upload your logo and set your brand colors</li>
                <li><strong>Create Membership Types:</strong> Set up different membership tiers with pricing</li>
                <li><strong>Add Your First Members:</strong> Start building your loyalty database</li>
                <li><strong>Record Your First Visit:</strong> Test the dining visit recording system</li>
                <li><strong>Explore Reports:</strong> Check out the analytics and insights</li>
            </ol>
        </div>

        <div class="section">
            <div class="section-title">💡 Pro Tips for Success</div>
            <ul class="feature-list">
                <li>Capture member preferences and allergies for better service</li>
                <li>Use the birthday alerts to send personalized greetings</li>
                <li>Regularly review your member analytics to understand patterns</li>
                <li>Train your staff on the two-step visit recording process</li>
                <li>Keep member information updated for accurate communications</li>
            </ul>
        </div>

        <div class="contact-info">
            <h4 style="margin-top: 0; color: #000;">📞 Need Help?</h4>
            <p>Our support team is here to help you get the most out of Membership MS:</p>
            <ul>
                <li><strong>Email:</strong> support@members.co.tz</li>
                <li><strong>Phone:</strong> +255 XXX XXX XXX</li>
                <li><strong>Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM EAT</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/dashboard') }}" class="cta-button">Get Started Now</a>
        </div>

        <div class="footer">
            <p><strong>Membership MS</strong> - Powered by Kinara Technologies</p>
            <p>Thank you for choosing us to help grow your restaurant's loyalty program!</p>
        </div>
    </div>
</body>
</html> 