<?php

use App\Models\User;
use App\Models\PksSubmission;
use App\Notifications\PksStatusUpdated;

test('it can create pks status notification', function () {
    // Create a mock user and submission
    $user = new User();
    $user->email = 'partner@test.com';
    $user->name = 'Test Partner';
    
    $submission = new PksSubmission();
    $submission->title = 'Test PKS Submission';
    $submission->description = 'Test description';
    $submission->status = 'proses';
    $submission->setRelation('user', $user);
    
    // Create the notification
    $notification = new PksStatusUpdated($submission, 'proses', 'disetujui');
    
    // Check that the notification was created successfully
    expect($notification)->toBeInstanceOf(PksStatusUpdated::class);
    
    // Check that the via method returns the correct channels
    expect($notification->via($user))->toEqual(['mail', 'database']);
});