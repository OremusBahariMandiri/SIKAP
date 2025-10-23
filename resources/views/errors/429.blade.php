<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Permintaan</title>
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
            color: #fd7e14;
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #fd7e14;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.5 5.5a.5.5 0 0 0-1 0v2a.5.5 0 0 0 .252.434l1.5.866a.5.5 0 1 0 .5-.866L8.5 7.366V5.5z"/>
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
            </svg>
        </div>
        <h1>Terlalu Banyak Permintaan</h1>
        <p>
            Anda telah melakukan terlalu banyak permintaan dalam waktu singkat.
            Mohon tunggu beberapa saat sebelum mencoba kembali.
        </p>

        <div class="details">
            <p>• Sistem kami membatasi jumlah permintaan untuk memastikan keamanan dan kestabilan layanan</p>
            <p>• Cobalah kembali dalam beberapa saat</p>
            <p>• Jika Anda menggunakan script atau bot, pastikan untuk membatasi jumlah permintaan</p>
        </div>

        @if(isset($retryAfter) && $retryAfter > 0)
        <div class="timer">
            <p>Anda dapat mencoba lagi dalam:</p>
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

        <a href="{{ url('/') }}" class="btn">Kembali ke Halaman Utama</a>

        <div class="support">
            Jika Anda mengalami masalah berkelanjutan, hubungi
            <a href="https://wa.me/628136736166">082136736166 (Jamas)</a>
        </div>
    </div>
</body>
</html>