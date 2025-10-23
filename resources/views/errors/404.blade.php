<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan</title>
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
            color: #6c757d;
            font-size: 80px;
            margin-bottom: 30px;
        }
        h1 {
            color: #343a40;
            margin-bottom: 24px;
            font-size: 28px;
        }
        .error-code {
            font-size: 80px;
            font-weight: bold;
            color: #e9ecef;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 16px;
            line-height: 1.6;
            font-size: 16px;
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
        .search-box {
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .search-input {
            padding: 10px 15px;
            width: 70%;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        .search-btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>
        </div>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>
            Maaf, kami tidak dapat menemukan halaman yang Anda cari.
            Mungkin halaman telah dipindahkan, dihapus, atau tidak pernah ada.
        </p>

        <a href="{{ url('/') }}" class="btn">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>