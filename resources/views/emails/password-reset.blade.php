<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Reset Password</h1>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="email-content">
                <p>Halo {{ $user->name ?? 'User' }},</p>
                <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
                <p>Klik tombol di bawah ini untuk mereset password Anda:</p>
            </div>
            
            <!-- Action Button -->
            <a href="{{ $resetUrl }}" class="action-button">
                Reset Password
            </a>
            
            <div class="email-content">
                <p>Tautan reset password ini akan kedaluwarsa dalam {{ $count }} menit.</p>
                <p>Jika Anda tidak meminta reset password, tidak perlu melakukan apa pun.</p>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <p>Terima kasih telah menggunakan sistem SI-Huyula. Kami menghargai kerja sama Anda.</p>
                <p class="signature">Hormat kami,<br>Administrator</p>
            </div>
        </div>
    </div>
</body>
</html>