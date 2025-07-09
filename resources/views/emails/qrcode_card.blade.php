<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $student->firstname }}'s SYBORG ID Card</title>
    <style>
        body {
            margin: 0;
            background-color: #7c0a0a;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            padding: 30px;
        }

        .card {
            width: 400px;
            height: 600px;
            background: url('/storage/card_bg.png') center/cover no-repeat, #7c0a0a;
            color: white;
            position: relative;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            overflow: hidden;
            padding: 20px;
        }

        .front, .back {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .top {
            text-align: center;
        }

        .top img.logo {
            width: 50px;
            margin-bottom: 5px;
        }

        .org-title {
            font-size: 20px;
            font-weight: bold;
        }

        .course {
            font-size: 14px;
            margin-top: 3px;
        }

        .center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr img {
            width: 180px;
            height: 180px;
            background: white;
            padding: 10px;
            border-radius: 8px;
        }

        .student-name {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
        }

        .signature {
            text-align: center;
            font-size: 12px;
        }

        .signature .line {
            margin-top: 30px;
            border-top: 1px solid #fff;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 5px;
        }

        /* BACK SIDE */
        .logo-large {
            width: 100px;
            margin: 0 auto;
            margin-top: 60px;
        }

        .slogan {
            text-align: center;
            font-size: 14px;
            font-style: italic;
            margin: 20px auto;
            padding: 0 10px;
        }

        .links {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            background: #fff;
            color: #7c0a0a;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <!-- FRONT CARD -->
    <div class="card front" id="front-card">
        <div class="top">
            <img src="https://syborg-server-wlpe4.ondigitalocean.app/uploads/Syborg_Logo/syborg_logo.png" class="logo" alt="Logo">
            <div class="org-title">SYSTEM BUILDERS<br>ORGANIZATION</div>
            <div class="course">BSCS</div>
        </div>
        <div class="center">
            <div class="qr">
                @if ($qr_code_url)
                    <img src="{{ $qr_code_url }}" alt="QR Code">
                @else
                    <p>No QR Code Available</p>
                @endif
            </div>
            <div class="student-name">{{ strtoupper($student->firstname . ' ' . $student->lastname) }}</div>
        </div>
        <div class="signature">
            <div class="line"></div>
            <div><strong>DR. PHILIPCRIS C. ENCARNACION</strong></div>
            <div>CCS DEAN</div>
        </div>
    </div>

    <!-- BACK CARD -->
    <div class="card back" id="back-card">
        <div class="top">
            <div class="org-title">SYSTEM BUILDERS<br>ORGANIZATION</div>
        </div>
        <div class="center">
            <img src="https://syborg-server-wlpe4.ondigitalocean.app/uploads/Syborg_Logo/syborg_logo.png" class="logo" alt="Logo">
            <div class="slogan">
                “Where code, knowledge,<br>and innovation converge.”
            </div>
        </div>
        <div class="links">
            <p>https://facebook.com/SyBorgSCC</p>
            <p>https://facebook.com/ccs.saintcolumban</p>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="action-buttons">
        <button onclick="downloadCard('front-card')" class="btn">Download Front</button>
        <button onclick="downloadCard('back-card')" class="btn">Download Back</button>
        <button onclick="window.print()" class="btn">Print Both</button>
    </div>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function downloadCard(id) {
            const el = document.getElementById(id);
            html2canvas(el).then(canvas => {
                const link = document.createElement('a');
                link.download = id + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>