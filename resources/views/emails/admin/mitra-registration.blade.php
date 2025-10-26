<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pendaftaran Mitra Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        {{-- Greeting --}}
        <h1 style="color: #3b82f6; font-size: 24px; margin-bottom: 20px;">
            Halo Admin,
        </h1>

        {{-- Introduction --}}
        <p style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            Ada pendaftaran mitra baru yang perlu diperiksa. Berikut detailnya:
        </p>

        {{-- Mitra Details --}}
        <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            <h2 style="color: #1e293b; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                Detail Mitra Baru
            </h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Nama Mitra:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $mitra->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Email:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $mitra->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Telepon:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $mitra->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Perusahaan:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $mitra->company ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Tanggal Pendaftaran:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $mitra->created_at->format('d F Y H:i') }}</td>
                </tr>
            </table>
        </div>

        {{-- Action Button --}}
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/admin/kelola-mitra" 
               style="display: inline-block; padding: 12px 24px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-weight: 600;">
                Lihat Daftar Mitra
            </a>
        </div>

        {{-- Outro --}}
        <p style="font-size: 16px; line-height: 1.5; margin-top: 20px;">
            Email ini dikirim otomatis oleh sistem SI-Huyula. Mohon tidak membalas email ini.
        </p>
    </div>
</body>
</html>