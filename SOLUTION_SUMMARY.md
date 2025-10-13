# Laravel PKS Status Notification System

## Overview
This implementation provides a modern, structured Laravel notification system for PKS status changes that automatically sends email notifications to partners when their submission status is updated by administrators.

## Key Features

### 1. Email Configuration
- Configured Gmail API integration via `.env` and `config/services.php`
- Set up Gmail mailer in `config/mail.php`
- Uses queue-based asynchronous email sending to prevent application slowdown

### 2. Notification System
- Created `PksStatusUpdated` notification class that implements `ShouldQueue`
- Supports both email and database notifications
- Custom email templates with modern, mobile-friendly design
- Dynamic subject lines based on status changes:
  - "Pengajuan PKS Anda sedang diproses"
  - "Pengajuan PKS Anda telah disetujui"
  - "Pengajuan PKS Anda ditolak"

### 3. Email Template
- Created responsive, branded email template using Laravel Markdown
- Professional design with color-coded status indicators
- Clear presentation of PKS details (title, purpose, status, revision notes)
- Direct link to partner dashboard

### 4. Logging
- Added comprehensive logging for email notifications
- Log entries include recipient email and submission ID
- Format: `[PksStatusUpdated] Email sent to {user_email} for submission ID {id}`

### 5. Integration Points
- Updated `PksSubmissionController@updateStatus` method to trigger notifications
- Leveraged existing `GmailServices` class for email delivery
- Integrated with existing PKS submission and user models

## Technical Implementation

### Configuration Files Modified
1. `.env` - Added Gmail mailer configuration
2. `config/mail.php` - Added Gmail mailer configuration
3. `config/services.php` - Verified Gmail API credentials

### New Files Created
1. `app/Notifications/PksStatusUpdated.php` - Notification class
2. `resources/views/emails/pks/status.blade.php` - Email template
3. `app/Console/Commands/TestPksStatusNotification.php` - Test command
4. `database/seeders/TestNotificationSeeder.php` - Test data seeder

### Models Used
- `App\Models\PksSubmission` - PKS submission data
- `App\Models\User` - Partner/user information

## Usage

### Automatic Notifications
When an administrator updates a PKS submission status via the dashboard, the system automatically:
1. Creates a status history record
2. Sends an email notification to the partner
3. Logs the notification event
4. Stores a database notification for in-app viewing

### Manual Testing
```bash
# Create test data
php artisan db:seed --class=TestNotificationSeeder

# Send test notification
php artisan app:test-pks-status-notification --submission-id=16

# Process queued notifications
php artisan queue:work --once
```

## Security & Performance
- All email sending is queued to prevent UI blocking
- Uses partner's registered email address only
- Implements proper authorization checks
- Follows Laravel security best practices

## Customization
The email template can be easily customized by modifying:
- `resources/views/emails/pks/status.blade.php`
- Color schemes and branding in the template
- Additional PKS fields to display in the email

## Dependencies
- Laravel 12+
- Google API Client Library
- Queue system (database driver configured)