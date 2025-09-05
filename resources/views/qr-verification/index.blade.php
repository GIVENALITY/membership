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
            margin: 50px auto;
            max-width: 600px;
        }
        .verification-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .verification-body {
            padding: 40px;
        }
        .qr-input {
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .qr-input:hover {
            border-color: #007bff;
            background: #e3f2fd;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .instructions {
            background: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <div class="verification-header">
                <i class="fas fa-qrcode fa-3x mb-3"></i>
                <h2 class="mb-0">QR Code Verification</h2>
                <p class="mb-0">Scan or enter QR code data to verify member authenticity</p>
            </div>
            
            <div class="verification-body">
                <form method="POST" action="{{ route('qr.verify') }}">
                    @csrf
                    
                    <div class="qr-input">
                        <i class="fas fa-camera fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted mb-3">Enter QR Code Data</h5>
                        <textarea 
                            name="qr_data" 
                            class="form-control" 
                            rows="4" 
                            placeholder="Paste QR code data here or scan with your device..."
                            required
                        ></textarea>
                    </div>
                    
                    <div class="instructions">
                        <h6><i class="fas fa-info-circle me-2"></i>How to use:</h6>
                        <ol class="mb-0">
                            <li>Scan the QR code on the member's card</li>
                            <li>Copy the scanned data and paste it above</li>
                            <li>Click "Verify QR Code" to check authenticity</li>
                        </ol>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fas fa-search me-2"></i>Verify QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
