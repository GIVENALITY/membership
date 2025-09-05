<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Verification - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
            max-width: 800px;
        }
        .verification-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .verification-header.invalid {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .member-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 20px;
            border-left: 5px solid #28a745;
        }
        .member-card.invalid {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .status-badge {
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
        }
        .member-info {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .qr-data {
            background: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            word-break: break-all;
            margin: 15px 0;
        }
        .action-buttons {
            text-align: center;
            padding: 20px;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            @if($valid)
                <!-- Valid QR Code -->
                <div class="verification-header">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h2 class="mb-0">QR Code Verified</h2>
                    <p class="mb-0">Member authentication successful</p>
                </div>
                
                <div class="member-card">
                    <div class="text-center mb-4">
                        <h3 class="mb-2">{{ $member['name'] }}</h3>
                        <span class="badge status-badge bg-success">
                            <i class="fas fa-user-check me-2"></i>Active Member
                        </span>
                    </div>
                    
                    <div class="member-info">
                        <div class="info-row">
                            <span class="info-label">Membership ID:</span>
                            <span class="info-value fw-bold">{{ $member['membership_id'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $member['email'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $member['phone'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Membership Type:</span>
                            <span class="info-value">{{ $member['membership_type'] ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Hotel:</span>
                            <span class="info-value">{{ $member['hotel_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="badge bg-success">{{ ucfirst($member['status']) }}</span>
                        </div>
                        @if($member['expires_at'])
                        <div class="info-row">
                            <span class="info-label">Expires:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($member['expires_at'])->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Verified on {{ now()->format('M d, Y \a\t g:i A') }}
                        </small>
                    </div>
                </div>
            @else
                <!-- Invalid QR Code -->
                <div class="verification-header invalid">
                    <i class="fas fa-times-circle fa-3x mb-3"></i>
                    <h2 class="mb-0">QR Code Invalid</h2>
                    <p class="mb-0">Member authentication failed</p>
                </div>
                
                <div class="member-card invalid">
                    <div class="text-center mb-4">
                        <h3 class="mb-2 text-danger">Verification Failed</h3>
                        <span class="badge status-badge bg-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>Invalid QR Code
                        </span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        This QR code could not be verified. Possible reasons:
                        <ul class="mb-0 mt-2">
                            <li>QR code is expired or invalid</li>
                            <li>Member account is inactive</li>
                            <li>QR code has been tampered with</li>
                            <li>Member no longer exists in the system</li>
                        </ul>
                    </div>
                    
                    @if($qrData)
                    <div class="qr-data">
                        <strong>Scanned Data:</strong><br>
                        {{ $qrData }}
                    </div>
                    @endif
                </div>
            @endif
            
            <div class="action-buttons">
                <button onclick="window.close()" class="btn btn-secondary btn-custom">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <button onclick="window.location.reload()" class="btn btn-primary btn-custom">
                    <i class="fas fa-redo me-2"></i>Scan Another
                </button>
                @if($valid)
                <a href="{{ route('members.show', $member['id']) }}" class="btn btn-success btn-custom">
                    <i class="fas fa-user me-2"></i>View Full Profile
                </a>
                @endif
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
