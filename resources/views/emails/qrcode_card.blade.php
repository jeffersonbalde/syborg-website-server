<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $student->firstname }}'s SYBORG ID Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .system-card {
            width: 600px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }
        .card-body {
            padding: 30px;
            text-align: center;
        }
        .organization-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .organization-subtitle {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
        }
        .college-name {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            color: #333;
        }
        .student-name {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0 5px;
            color: #222;
        }
        .student-id {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }
        .qr-container {
            margin: 25px auto;
            padding: 20px;
            border: 1px dashed #ccc;
            display: inline-block;
            background: white;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 250px;
            display: inline-block;
            padding-top: 5px;
        }
        .signature-name {
            font-size: 16px;
            font-weight: bold;
        }
        .signature-title {
            font-size: 14px;
            color: #555;
        }
        .action-buttons {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-download {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        .btn-print {
            background-color: #2196F3;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="system-card" id="qrcode-card">
        <div class="card-header">
            <div class="organization-title">SYSTEM BUILDERS ORGANIZATION</div>
            <div class="organization-subtitle">BSCS</div>
        </div>
        <div class="card-body">
            <div class="college-name">COLLEGE OF COMPUTING STUDIES</div>
            <div class="organization-subtitle">SAINT COLUMBAN COLLEGE, PAGADIAN CITY</div>
            
            <div class="student-name">{{ strtoupper($student->firstname . ' ' . $student->lastname) }}</div>
            <div class="student-id">{{ $student->student_id }}</div>
            
            <div class="qr-container">
                @if ($qr_code_url)
                    <img src="{{ $qr_code_url }}" alt="QR Code" style="width:180px; height:180px;">
                @else
                    <p>No QR code available.</p>
                @endif
            </div>
            
            <div>
                <div class="signature-line"></div>
                <div class="signature-name">DR. PHILIPCRIS C. ENCARNACION</div>
                <div class="signature-title">GGS DEAN</div>
            </div>

            <div class="action-buttons">
                <button onclick="downloadCard()" class="btn btn-download">Download Card</button>
                <button onclick="window.print()" class="btn btn-print">Print Card</button>
            </div>
        </div>
    </div>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function downloadCard() {
            html2canvas(document.getElementById('qrcode-card')).then(canvas => {
                const link = document.createElement('a');
                link.download = 'SYBORG-ID-Card-{{ $student->student_id }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>