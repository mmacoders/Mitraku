<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan Rapat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #3B82F6;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .content {
            padding: 30px;
        }
        .content h1 {
            color: #3B82F6;
            margin-top: 0;
        }
        .details {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: bold;
            color: #4B5563;
        }
        .button {
            display: inline-block;
            background-color: #3B82F6;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background-color: #F3F4F6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/logo-app.png') }}" alt="Logo" class="logo" onerror="this.style.display='none'">
            <h1>Undangan Rapat</h1>
        </div>
        
        <div class="content">
            <h2>Halo {{ $user->name }},</h2>
            
            <p>Anda telah diundang ke rapat dengan detail sebagai berikut:</p>
            
            <div class="details">
                <div class="detail-item">
                    <div class="detail-label">Judul Rapat:</div>
                    <div>{{ $rapat->judul }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Tanggal & Waktu:</div>
                    <div>{{ $rapat->tanggal_waktu->format('d F Y H:i') }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Lokasi/Link Meeting:</div>
                    <div>{{ $rapat->lokasi }}</div>
                </div>
                
                @if($rapat->deskripsi)
                <div class="detail-item">
                    <div class="detail-label">Deskripsi:</div>
                    <div>{{ $rapat->deskripsi }}</div>
                </div>
                @endif
            </div>
            
            <p>Silakan klik tombol di bawah untuk melihat detail rapat:</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/mitra/rapat/' . $rapat->id) }}" class="button">Lihat Detail Rapat</a>
            </div>
            
            <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.</p>
            
            <p>Terima kasih telah menggunakan aplikasi kami!</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem PKS. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Mitraku. All rights reserved.</p>
        </div>
    </div>
</body>
</html>