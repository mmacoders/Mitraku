<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Pengajuan PKS</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Inter', 'Roboto', 'Open Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background-color: #16A34A;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .email-body {
            padding: 30px 20px;
        }
        
        .email-content {
            margin-bottom: 30px;
        }
        
        .email-content p {
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        
        /* Submission details card */
        .submission-details {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .submission-details h2 {
            color: #1F2937;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6B7280;
            width: 150px;
            flex-shrink: 0;
        }
        
        .detail-value {
            color: #1F2937;
            flex: 1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-approved {
            background-color: #DCFCE7;
            color: #16A34A;
        }
        
        .status-expired {
            color: #EF4444;
            font-weight: 600;
            font-size: 14px;
        }
        
        /* Action button */
        .action-button {
            display: block;
            width: fit-content;
            margin: 30px auto;
            padding: 14px 28px;
            background-color: #16A34A;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.2s ease;
        }
        
        .action-button:hover {
            background-color: #15803D;
        }
        
        /* Footer */
        .email-footer {
            background-color: #F9FAFB;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #E5E7EB;
        }
        
        .email-footer p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6B7280;
        }
        
        .email-footer .signature {
            font-style: italic;
            color: #4B5563;
            font-size: 14px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Body -->
<div class="email-body">
    <!-- Greeting -->
    <div class="email-content">
        <p>Halo {{ $pksSubmission->user->name ?? 'User' }},</p>
        <p>Status pengajuan PKS Anda telah diperbarui. Berikut detailnya:</p>
    </div>

    <!-- PKS Details Card -->
    <div style="max-width:600px;margin:0 auto;background:#F9FAFB;border:1px solid #E5E7EB;
            border-radius:10px;padding:20px 24px;box-shadow:0 2px 4px rgba(0,0,0,0.04);
            font-family:Arial, sans-serif;margin-top:16px;">

    <h2 style="margin-top:0;margin-bottom:12px;font-size:18px;font-weight:600;color:#111827;
               border-bottom:1px solid #E5E7EB;padding-bottom:6px;">
        Detail Pengajuan
    </h2>

    <div style="font-size:14px;color:#374151;line-height:1.5;">
        <div style="margin-bottom:10px;">
            <strong style="display:inline-block;width:150px;color:#6B7280;">Judul PKS:</strong>
            <span>{{ $pksSubmission->title }}</span>
        </div>

        <div style="margin-bottom:10px;">
            <strong style="display:inline-block;width:150px;color:#6B7280;">Tujuan Pengajuan:</strong>
            <span>{{ $pksSubmission->description }}</span>
        </div>

        <div style="margin-bottom:10px;">
            <strong style="display:inline-block;width:150px;color:#6B7280;">Status Terbaru:</strong>
            <span>
                @switch($newStatus)
                    @case('proses')
                        <span style="background:#FEF3C7;color:#92400E;padding:3px 8px;
                                     border-radius:8px;font-weight:600;">Dalam Proses</span>
                        @break
                    @case('disetujui')
                        <span style="background:#DCFCE7;color:#166534;padding:3px 8px;
                                     border-radius:8px;font-weight:600;">Disetujui</span>
                        @break
                    @case('ditolak')
                        <span style="background:#FEE2E2;color:#B91C1C;padding:3px 8px;
                                     border-radius:8px;font-weight:600;">Ditolak</span>
                        @break
                    @case('pembahasan')
                        <span style="background:#E0E7FF;color:#3730A3;padding:3px 8px;
                                     border-radius:8px;font-weight:600;">Pembahasan</span>
                        @break
                    @case('revisi')
                        <span style="background:#FEF3C7;color:#B45309;padding:3px 8px;
                                     border-radius:8px;font-weight:600;">Revisi</span>
                        @break
                @endswitch
            </span>
        </div>

        @if($newStatus == 'disetujui' && $pksSubmission->validity_period_start && $pksSubmission->validity_period_end)
            @php
                $startDate = \Carbon\Carbon::parse($pksSubmission->validity_period_start);
                $endDate = \Carbon\Carbon::parse($pksSubmission->validity_period_end);
                $today = \Carbon\Carbon::now();
                $daysUntilExpiration = ceil($today->floatDiffInDays($endDate));
            @endphp

            <div style="margin-bottom:4px;">
                <strong style="display:inline-block;width:150px;color:#6B7280;">Masa Berlaku:</strong>
                <span>{{ $startDate->format('d F Y') }} – {{ $endDate->format('d F Y') }}</span>
            </div>

            @if($endDate->isPast())
                <div style="color:#DC2626;margin-left:150px;">Masa berlaku telah berakhir.</div>
            @endif
        @endif
    </div>
</div>

          
                @if($pksSubmission->revision_notes)
                <div class="detail-row">
                    <div class="detail-label">Catatan Revisi:</div>
                    <div class="detail-value">{{ $pksSubmission->revision_notes }}</div>
                </div>
                @endif
            </div>
            
            <!-- Action Button -->
            <a href="{{ config('app.url') }}/mitra/dashboard" class="action-button">
                Lihat Dashboard
            </a>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p>Terima kasih telah menggunakan sistem SI-Huyula. Kami menghargai kerja sama Anda.</p>
            <p class="signature">Hormat kami,<br>Administrator</p>
        </div>
    </div>
</body>
</html>