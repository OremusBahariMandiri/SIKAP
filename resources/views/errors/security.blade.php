<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Keamanan</title>
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
            color: #ffc107;
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
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
        </div>
        <h1>{{ $title ?? 'Peringatan Keamanan' }}</h1>
        <p>
            {{ $message ?? 'Sistem keamanan kami mendeteksi adanya aktivitas yang tidak biasa.' }}
        </p>

        @if(isset($details) && !empty($details))
        <div class="details">
            @foreach($details as $detail)
                <p>• {{ $detail }}</p>
            @endforeach
        </div>
        @endif

        <a href="{{ url('/') }}" class="btn">Kembali ke Halaman Utama</a>

        <div class="support">
            Jika Anda yakin ini adalah kesalahan, silakan hubungi
            <a href="https://wa.me/62812136736166">0821-3673-6166 (Jamas)</a>
        </div>
    </div>
</body>
</html>