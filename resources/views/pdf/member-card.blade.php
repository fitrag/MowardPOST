<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Member Card</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 54mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        .card {
            width: 85.6mm;
            height: 54mm;
            position: relative;
            background-color: #4f46e5; 
            color: white;
            overflow: hidden;
        }
        /* Use a simple image or solid color if gradients fail, but here we keep simple styling */
        .bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #4f46e5;
            z-index: -2;
        }
        .bg-pattern-1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        .bg-pattern-2 {
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 150px;
            height: 150px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        
        /* Main layout table */
        .layout-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 10;
        }
        .layout-table td {
            padding: 20px;
            vertical-align: top;
        }
        
        /* Header styles */
        .business-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            color: white;
        }
        .card-label {
            font-size: 10px;
            opacity: 0.8;
            margin: 0;
            color: white;
        }
        .tier-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            color: white;
        }
        
        /* Footer styles */
        .label {
            font-size: 8px;
            opacity: 0.8;
            margin-bottom: 2px;
            text-transform: uppercase;
            color: #e0e7ff; /* Light indigo/white */
        }
        .value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: white;
        }
        .card-number {
            font-family: 'Courier', monospace;
            font-size: 16px;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: bold;
            color: white;
        }
        .qr-code-container {
            background-color: white;
            padding: 5px;
            border-radius: 8px;
            width: 64px;
            height: 64px;
            text-align: center;
        }
        .qr-code-container img {
            width: 64px;
            height: 64px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="bg-gradient"></div>
        <div class="bg-pattern-1"></div>
        <div class="bg-pattern-2"></div>
        
        <table class="layout-table">
            <!-- Top Row: Header -->
            <tr>
                <td style="height: 40px; padding-bottom: 0;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 0; vertical-align: top;">
                                <div class="business-name">{{ \App\Models\Setting::getValue('business_name', config('app.name', 'POS System')) }}</div>
                                <div class="card-label">Member Card</div>
                            </td>
                            <td style="padding: 0; text-align: right; vertical-align: top;">
                                <!-- Tier badge removed -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <!-- Bottom Row: Info & QR -->
            <tr>
                <td style="vertical-align: bottom; padding-top: 0;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 0; vertical-align: bottom;">
                                <div class="label">Card Number</div>
                                <div class="card-number">{{ $customer->card_number }}</div>
                                
                                <div class="label">Member Name</div>
                                <div class="value">{{ $customer->name }}</div>
                                
                                <div class="label">Member Since</div>
                                <div class="value" style="font-size: 10px; margin-bottom: 0;">{{ $customer->member_since->format('M Y') }}</div>
                            </td>
                            <td style="padding: 0; width: 70px; vertical-align: bottom; text-align: right;">
                                <div class="qr-code-container">
                                    <img src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($customer->card_number)) }} ">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
