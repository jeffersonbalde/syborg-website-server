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
        
        <p>Your official QR code identification card is ready:</p>
        <a href="{{ route('student.qrcode', parameters: ['id' => $student->id]) }}" 
           target="_blank" 
           style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
           View Your QR Code Card
        </a>

        <p>Thank you for joining the SYBORG Club!</p>
        <p>Best,<br><strong>SYBORG Team</strong></p>
    </div>
</body>
</html>