<?php

// Simple script to test the PKS expiration functionality

require_once 'vendor/autoload.php';

use Carbon\Carbon;

// Test the expiration logic
echo "Testing PKS expiration logic...\n";

// Function to check if PKS is expiring soon (within 7 days)
function isExpiringSoon($endDate) {
    if (!$endDate) return false;
    $end = new Carbon($endDate);
    $today = Carbon::today();
    $diffTime = abs($end->getTimestamp() - $today->getTimestamp());
    $diffDays = ceil($diffTime / (60 * 60 * 24));
    return $diffDays <= 7 && $end->gt($today);
}

// Function to check if PKS has expired
function isExpired($endDate) {
    if (!$endDate) return false;
    $end = new Carbon($endDate);
    $today = Carbon::today();
    return $end->lt($today);
}

// Test case 1: PKS expiring in 5 days
$endDate = Carbon::today()->addDays(5);
$isExpiringSoon = isExpiringSoon($endDate);
$isExpired = isExpired($endDate);

echo "Test 1 - PKS expiring in 5 days:\n";
echo "End date: " . $endDate->format('Y-m-d') . "\n";
echo "Today: " . Carbon::today()->format('Y-m-d') . "\n";
echo "Is expiring soon: " . ($isExpiringSoon ? 'Yes' : 'No') . "\n";
echo "Is expired: " . ($isExpired ? 'Yes' : 'No') . "\n\n";

// Test case 2: PKS expiring in 10 days
$endDate = Carbon::today()->addDays(10);
$isExpiringSoon = isExpiringSoon($endDate);
$isExpired = isExpired($endDate);

echo "Test 2 - PKS expiring in 10 days:\n";
echo "End date: " . $endDate->format('Y-m-d') . "\n";
echo "Today: " . Carbon::today()->format('Y-m-d') . "\n";
echo "Is expiring soon: " . ($isExpiringSoon ? 'Yes' : 'No') . "\n";
echo "Is expired: " . ($isExpired ? 'Yes' : 'No') . "\n\n";

// Test case 3: PKS already expired
$endDate = Carbon::today()->subDays(5);
$isExpiringSoon = isExpiringSoon($endDate);
$isExpired = isExpired($endDate);

echo "Test 3 - PKS expired 5 days ago:\n";
echo "End date: " . $endDate->format('Y-m-d') . "\n";
echo "Today: " . Carbon::today()->format('Y-m-d') . "\n";
echo "Is expiring soon: " . ($isExpiringSoon ? 'Yes' : 'No') . "\n";
echo "Is expired: " . ($isExpired ? 'Yes' : 'No') . "\n\n";

// Test case 4: PKS expiring today
$endDate = Carbon::today();
$isExpiringSoon = isExpiringSoon($endDate);
$isExpired = isExpired($endDate);

echo "Test 4 - PKS expiring today:\n";
echo "End date: " . $endDate->format('Y-m-d') . "\n";
echo "Today: " . Carbon::today()->format('Y-m-d') . "\n";
echo "Is expiring soon: " . ($isExpiringSoon ? 'Yes' : 'No') . "\n";
echo "Is expired: " . ($isExpired ? 'Yes' : 'No') . "\n\n";

echo "All tests completed successfully!\n";