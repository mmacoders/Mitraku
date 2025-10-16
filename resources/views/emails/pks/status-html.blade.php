<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Pengajuan PKS</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        {{-- Greeting --}}
        <h1 style="color: #3b82f6; font-size: 24px; margin-bottom: 20px;">
            Halo {{ $pksSubmission->user->name ?? 'User' }},
        </h1>

        {{-- Introduction --}}
        <p style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            Status pengajuan PKS Anda telah diperbarui. Berikut detailnya:
        </p>

        {{-- Expiration Warning --}}
        @if($newStatus == 'disetujui' && $pksSubmission->validity_period_end)
            @php
                $expirationDate = \Carbon\Carbon::parse($pksSubmission->validity_period_end);
                $daysUntilExpiration = $expirationDate->diffInDays(\Carbon\Carbon::now());
            @endphp
            @if($expirationDate->isFuture() && $daysUntilExpiration <= 7)
                <div style="background-color: #fffbeb; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #fbbf24;">
                    <h2 style="color: #92400e; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                        ⚠️ Peringatan Kedaluwarsa
                    </h2>
                    <p style="font-size: 16px; line-height: 1.5; margin-bottom: 10px; color: #92400e;">
                        PKS Anda akan kedaluwarsa dalam {{ $daysUntilExpiration }} hari pada tanggal {{ $expirationDate->format('d F Y') }}.
                    </p>
                    <p style="font-size: 16px; line-height: 1.5; margin-bottom: 0; color: #92400e;">
                        Silakan perpanjang PKS Anda sebelum masa berlaku habis.
                    </p>
                </div>
            @endif
        @endif

        {{-- PKS Details --}}
        <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            <h2 style="color: #1e293b; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                Detail Pengajuan
            </h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; font-weight: 600; color: #64748b;">Judul PKS:</td>
                    <td style="padding: 5px 0; color: #1e293b;">{{ $pksSubmission->title }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: 600; color: #64748b;">Tujuan Pengajuan:</td>
                    <td style="padding: 5px 0; color: #1e293b;">{{ $pksSubmission->description }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: 600; color: #64748b;">Status Terbaru:</td>
                    <td style="padding: 5px 0; color: #1e293b;">
                        @if($newStatus == 'proses')
                            <span style="color: #f59e0b; font-weight: 600;">Dalam Proses</span>
                        @elseif($newStatus == 'disetujui')
                            <span style="color: #10b981; font-weight: 600;">Disetujui</span>
                        @elseif($newStatus == 'ditolak')
                        <span style="color: #ef4444; font-weight: 600;">Ditolak</span>
                        @elseif($newStatus == 'pembahasan')
                        <span style="color: #745dcd; font-weight: 600;">Pembahasan</span>
                        @elseif($newStatus == 'revisi')
                            <span style="color: #f59e0b; font-weight: 600;">Revisi</span>
                        @endif
                    </td>
                </tr>
                @if($newStatus == 'disetujui' && $pksSubmission->validity_period_start && $pksSubmission->validity_period_end)
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Masa Berlaku:</td>
                    <td style="padding: 8px 0; color: #1e293b;">
                        {{ \Carbon\Carbon::parse($pksSubmission->validity_period_start)->format('d F Y') }} - 
                        {{ \Carbon\Carbon::parse($pksSubmission->validity_period_end)->format('d F Y') }}
                        @if($pksSubmission->validity_period_end)
                            @php
                                $expirationDate = \Carbon\Carbon::parse($pksSubmission->validity_period_end);
                                $daysUntilExpiration = $expirationDate->diffInDays(\Carbon\Carbon::now());
                            @endphp
                            @if($expirationDate->isFuture() && $daysUntilExpiration <= 7)
                                <span style="color: #ef4444; font-weight: 600;">(Akan kedaluwarsa dalam {{ $daysUntilExpiration }} hari)</span>
                            @elseif($expirationDate->isPast())
                                <span style="color: #ef4444; font-weight: 600;">(Sudah kedaluwarsa)</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @endif
                @if($pksSubmission->revision_notes)
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; color: #64748b;">Catatan Revisi:</td>
                    <td style="padding: 8px 0; color: #1e293b;">{{ $pksSubmission->revision_notes }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Action Button --}}
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/mitra/dashboard" 
               style="display: inline-block; padding: 12px 24px; background-color: {{ $newStatus == 'disetujui' ? '#10b981' : ($newStatus == 'ditolak' ? '#ef4444' : '#3b82f6') }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 600;">
                Lihat Dashboard
            </a>
        </div>

        {{-- Outro --}}
        <p style="font-size: 16px; line-height: 1.5; margin-top: 20px;">
            Terima kasih telah menggunakan sistem Mitraku.
        </p>

        {{-- Salutation --}}
        <p style="font-size: 16px; line-height: 1.5; margin-top: 20px;">
            Hormat kami,<br>
            Administrator
        </p>
    </div>
</body>
</html>