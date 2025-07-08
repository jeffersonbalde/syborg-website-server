<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SYBORG Approval</title>
</head>
<body style="font-family: Arial; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #4CAF50;">Welcome, {{ $student->firstname }}!</h2>
        <p>Your SYBORG registration has been approved. You can now log in using your institutional email.</p>
        
        <p>Here’s your QR code:</p>
        @if ($qr_code_url)
            <img src="{{ $qr_code_url }}" alt="QR Code" style="width:200px; height:200px;">
        @else
            <p>No QR code available.</p>
        @endif


        <p>Thank you for joining the SYBORG Club!</p>
        <p>Best,<br><strong>SYBORG Team</strong></p>
    </div>
</body>
</html>
