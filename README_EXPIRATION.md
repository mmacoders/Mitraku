# PKS Expiration Warning System

## Overview

This system automatically warns partners when their PKS (Perjanjian Kerja Sama) documents are about to expire. The warning is triggered 7 days before the expiration date.

## Features

1. **Email Notifications**: Automatically sends email warnings to partners
2. **UI Indicators**: Shows visual warnings in the admin and partner dashboards
3. **Daily Checks**: Runs automatically every day to check for expiring documents

## Implementation Details

### Backend

1. **Console Command**: `app/Console/Commands/CheckPksExpiration.php`
   - Checks for PKS submissions expiring within 7 days
   - Sends notifications to partners

2. **Notification Class**: `app/Notifications/PksExpirationWarning.php`
   - Handles email and database notifications
   - Uses existing Gmail notification channel

3. **Console Kernel**: `app/Console/Kernel.php`
   - Registers and schedules the expiration check command

### Frontend

1. **Admin Dashboard**: `resources/js/Pages/admin/kelola-pks/Index.vue`
   - Shows expiration warnings in the PKS table
   - Highlights expiring documents with red badges

2. **Partner Dashboard**: `resources/js/Pages/mitra/Dashboard.vue`
   - Shows expiration warnings in the PKS table
   - Highlights expiring documents with red badges

3. **Detail Modal**: `resources/js/Components/PksSubmissionDetailModal.vue`
   - Shows detailed expiration information
   - Displays countdown to expiration

### Email Templates

1. **HTML Template**: `resources/views/emails/pks/status-html.blade.php`
   - Includes expiration warning section
   - Shows days until expiration

## How It Works

1. The system runs daily at 9:00 AM (can be configured)
2. It checks all approved PKS submissions with validity periods
3. For submissions expiring within 7 days:
   - Sends email notification to the partner
   - Creates database notification
4. UI shows visual indicators for expiring documents

## Setup

1. Make sure the console kernel is registered in your application
2. Schedule the command to run daily:
   ```bash
   php artisan pks:check-expiration
   ```
3. Or add to your crontab:
   ```
   0 9 * * * cd /path-to-your-project && php artisan pks:check-expiration >> /dev/null 2>&1
   ```

## Testing

Run the expiration check manually:
```bash
php artisan pks:check-expiration
```

Check the logs for any issues:
```bash
tail -f storage/logs/laravel.log
```

## Customization

### Change Warning Period

Modify the console command to change from 7 days to another value:
```php
// In CheckPksExpiration.php
whereDate('validity_period_end', '<=', $today->copy()->addDays(7)) // Change 7 to your desired value
```

### Customize Email Template

Edit `resources/views/emails/pks/status-html.blade.php` to change the email content.

### Customize UI

Edit the Vue components to change how expiration warnings are displayed:
- Admin table: `resources/js/Pages/admin/kelola-pks/Index.vue`
- Partner dashboard: `resources/js/Pages/mitra/Dashboard.vue`
- Detail modal: `resources/js/Components/PksSubmissionDetailModal.vue`

## Troubleshooting

### No Notifications Sent

1. Check if the command is running: `php artisan pks:check-expiration`
2. Check the logs: `storage/logs/laravel.log`
3. Verify that PKS submissions have valid `validity_period_end` dates
4. Ensure the PKS status is 'disetujui'

### Email Not Received

1. Check the Gmail service configuration
2. Verify the partner's email address
3. Check spam/junk folders
4. Review the Gmail service logs