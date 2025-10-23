<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditangguhkan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        .warning-icon {
            color: #dc3545;
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 24px;
            font-size: 28px;
        }
        p {
            margin-bottom: 16px;
            line-height: 1.6;
            font-size: 16px;
        }
        .details {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 16px;
            margin-top: 24px;
            text-align: left;
        }
        .details p {
            margin: 8px 0;
        }
        .timer {
            margin-top: 30px;
            font-size: 18px;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 24px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #0069d9;
        }
        .support {
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        .error-code {
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
        </div>
        <h1>Akses Anda Ditangguhkan</h1>
        <p>
            Sistem keamanan kami mendeteksi aktivitas tidak wajar dari alamat IP Anda. Untuk melindungi sistem,
            akses Anda telah ditangguhkan sementara.
        </p>
        <p>
            Hal ini mungkin disebabkan oleh:
        </p>
        <div class="details">
            <p>• Terlalu banyak permintaan dalam waktu singkat (Spam)</p>
            <p>• Pola akses yang mencurigakan atau tidak lazim</p>
            <p>• Penggunaan alat atau script otomatis</p>
            <p>• Koneksi VPN atau proxy yang terdeteksi sebagai mencurigakan</p>
        </div>

        @if(isset($retryAfter) && $retryAfter > 0)
        <div class="timer">
            <p>Akses akan dipulihkan dalam:</p>
            <span id="countdown">{{ gmdate("i:s", $retryAfter) }}</span>
        </div>

        <script>
            // Countdown timer
            var seconds = {{ $retryAfter }};
            function updateCountdown() {
                var minutes = Math.floor(seconds / 60);
                var remainingSeconds = seconds % 60;
                document.getElementById('countdown').textContent =
                    (minutes < 10 ? '0' : '') + minutes + ':' +
                    (remainingSeconds < 10 ? '0' : '') + remainingSeconds;

                if (seconds <= 0) {
                    clearInterval(countdownTimer);
                    window.location.reload();
                } else {
                    seconds--;
                }
            }
            var countdownTimer = setInterval(updateCountdown, 1000);
            updateCountdown();
        </script>
        @endif

        <a href="{{ url('/') }}" class="btn">Coba Lagi</a>

        <div class="support">
            Jika Anda yakin ini adalah kesalahan, silakan hubungi
            <a href="https://wa.me/6282136736166">0821-3673-6166 (Jamas)</a>
        </div>
    </div>
</body>
</html>