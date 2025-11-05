<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $jobsCount = DB::table('jobs')->count();
    echo "Total jobs in queue: " . $jobsCount . "\n";
    
    if ($jobsCount > 0) {
        $jobs = DB::table('jobs')->get();
        echo "Jobs details:\n";
        foreach ($jobs as $job) {
            echo "- Job ID: " . $job->id . ", Queue: " . $job->queue . ", Attempts: " . $job->attempts . "\n";
        }
    }
    
    $failedJobsCount = DB::table('failed_jobs')->count();
    echo "Failed jobs: " . $failedJobsCount . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>