# PKS Expiration Warning System

This document explains how the PKS expiration warning system works and how to set it up.

## Overview

The PKS expiration warning system automatically checks for PKS submissions that are about to expire and sends notifications to partners 7 days before the expiration date.

## Components

### 1. Console Command

The system uses a console command `pks:check-expiration` that:

1. Finds all PKS submissions with status 'disetujui' (approved)
2. Checks if the validity period end date is within 7 days from today
3. Sends a notification to the partner for each expiring PKS

### 2. Notification System

The system uses a custom notification class `PksExpirationWarning` that:

1. Sends an email notification to the partner
2. Stores a database notification for the partner
3. Uses the existing Gmail notification channel

### 3. UI Indicators

The system shows visual indicators in the UI:

1. In the admin PKS management table
2. In the partner dashboard table
3. In the PKS detail modal

## Setup

### 1. Register the Console Kernel

The console kernel is automatically registered in the application.

### 2. Schedule the Command

To enable automatic checking, add the following to your server's crontab:

```
0 9 * * * cd /path-to-your-project && php artisan pks:check-expiration >> /dev/null 2>&1
```

Or use Laravel's built-in scheduler by adding this to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('pks:check-expiration')
             ->dailyAt('09:00')
             ->description('Check for expiring PKS submissions');
}
```

### 3. Run the Command Manually

You can run the command manually to test it:

```bash
php artisan pks:check-expiration
```

## Testing

The system includes unit and feature tests:

1. `tests/Unit/PksExpirationTest.php` - Tests the core logic
2. `tests/Feature/PksExpirationWarningTest.php` - Tests the complete flow

Run tests with:

```bash
php artisan test
```

## Customization

### Changing the Warning Period

To change the warning period from 7 days to another value, modify the console command:

```php
// In CheckPksExpiration.php
whereDate('validity_period_end', '<=', Carbon::today()->addDays(7)) // Change 7 to your desired value
```

### Customizing the Email Template

The email template is located at `resources/views/emails/pks/status-html.blade.php`. The expiration warning is already integrated into this template.

### Customizing UI Indicators

The UI indicators are implemented in:

1. `resources/js/Pages/admin/kelola-pks/Index.vue` - Admin table
2. `resources/js/Pages/mitra/Dashboard.vue` - Partner dashboard
3. `resources/js/Components/PksSubmissionDetailModal.vue` - Detail modal

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

## Maintenance

Regular maintenance tasks:

1. Monitor the logs for errors
2. Check that the cron job is running
3. Verify that notifications are being sent
4. Update the warning period if business requirements change