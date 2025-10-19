<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan Rapat</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Inter', 'Nunito', 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .header {
            background-color: #2563EB;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .header-icon {
            font-size: 24px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .logo {
            max-width: 120px;
            margin-right: auto;
        }
        
        .content {
            padding: 30px;
        }
        
        .greeting {
            font-size: 20px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
            color: #111827;
        }
        
        .intro {
            font-size: 16px;
            color: #374151;
            margin-top: 0;
            margin-bottom: 30px;
        }
        
        .details {
            background-color: #F9FAFB;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border: 1px solid #E5E7EB;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
        }
        
        .detail-icon {
            font-size: 18px;
            margin-right: 15px;
            color: #2563EB;
            min-width: 20px;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 14px;
            font-weight: 600;
            color: #6B7280;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        
        .detail-value {
            font-size: 16px;
            color: #111827;
            margin: 0;
            font-weight: 500;
        }
        
        .actions {
            text-align: center;
            margin: 35px 0;
        }
        
        .primary-button {
            display: inline-block;
            background-color: #2563EB;
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin: 0 10px 15px;
            transition: background-color 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
          .primary-button:hover {
            background-color: #1D4ED8;
        }
        
        .secondary-button {
            display: inline-block;
            background-color: #F9FAFB;
            color: #2563EB;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin: 0 10px 15px;
            transition: background-color 0.2s ease;
            border: 1px solid #E5E7EB;
            cursor: pointer;
        }
        
        .secondary-button:hover {
            background-color: #F3F4F6;
        }
        
        .divider {
            height: 1px;
            background-color: #E5E7EB;
            margin: 30px 0;
        }
        
        .footer {
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6B7280;
        }
        
        /* Responsive adjustments */
        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .details {
                padding: 20px;
            }
            
            .primary-button,
            .secondary-button {
                display: block;
                margin: 0 0 15px 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="header-icon">📅</div>
                <h1>Undangan Rapat</h1>
            </div>
        </div>
        
        <div class="content">
            <h2 class="greeting">Halo {{ $user->name }},</h2>
            
            <p class="intro">Anda diundang untuk berpartisipasi dalam rapat berikut. Mohon konfirmasi kehadiran Anda.</p>
            
            <div class="details">
                <div class="detail-item">
                    <div class="detail-icon">📝</div>
                    <div class="detail-content">
                        <div class="detail-label">Judul Rapat</div>
                        <p class="detail-value">{{ $rapat->judul }}</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">🕒</div>
                    <div class="detail-content">
                        <div class="detail-label">Tanggal & Waktu</div>
                        <p class="detail-value">{{ $rapat->tanggal_waktu->format('d F Y H:i') }}</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">📍</div>
                    <div class="detail-content">
                        <div class="detail-label">Lokasi/Link Meeting</div>
                        <p class="detail-value">{{ $rapat->lokasi }}</p>
                    </div>
                </div>
                
                @if($rapat->deskripsi)
                <div class="detail-item">
                    <div class="detail-icon">💬</div>
                    <div class="detail-content">
                        <div class="detail-label">Deskripsi</div>
                        <p class="detail-value">{{ $rapat->deskripsi }}</p>
                    </div>
                </div>
                @endif

                <p class="intro">Apabila Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut terkait rapat ini,
                silakan menghubungi kami melalui nomor berikut:
                <br>   
                <br>
                <strong>Telp. +62 821-9000-2618 </strong> atau <strong>WhatsApp: 0821-9000-2618</strong>.
                </p>
            </div>
            
            <div class="actions">
                <a href="{{ url('/mitra/rapat/' . $rapat->id) }}" class="primary-button">Lihat Detail Rapat</a>
                <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($rapat->judul) }}&dates={{ $rapat->tanggal_waktu->format('Ymd\THis') }}/{{ $rapat->tanggal_waktu->addHour()->format('Ymd\THis') }}&details={{ urlencode('Rapat: ' . $rapat->judul . '\nLokasi: ' . $rapat->lokasi . '\nDeskripsi: ' . ($rapat->deskripsi ?? '')) }}&location={{ urlencode($rapat->lokasi) }}&sf=true&output=xml" class="secondary-button">📆 Tambahkan ke Kalender</a>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem SI-Huyula. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} SI-Huyula. All rights reserved.</p>
        </div>
    </div>
</body>
</html>