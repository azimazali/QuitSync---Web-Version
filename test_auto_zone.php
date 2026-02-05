<?php

use App\Models\User;
use App\Models\SmokingLog;
use App\Models\Geofence;
use App\Services\GeofenceService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Auto Geofence Creation...\n";

// 1. Create a dummy user
$user = User::factory()->create();
echo "Created Test User: {$user->id}\n";

// 2. Clear existing logs/geofences for this user (just in case)
SmokingLog::where('user_id', $user->id)->delete();
Geofence::where('user_id', $user->id)->delete();

// 3. Create 1 log (Strict Mode Test)
$baseLat = 3.1579;
$baseLng = 101.7116;

echo "Creating 1 smoking log near KLCC...\n";
for ($i = 0; $i < 1; $i++) {
    // Add tiny jitter to simulate real GPS
    $lat = $baseLat + ($i * 0.0001); // Approx 11 meters difference per step
    $lng = $baseLng + ($i * 0.0001);

    SmokingLog::create([
        'user_id' => $user->id,
        'type' => 'smoked',
        'latitude' => $lat,
        'longitude' => $lng,
        'smoked_at' => now(),
        'quantity' => 1
    ]);
}

// 4. Run the service
echo "Running GeofenceService...\n";
$service = new GeofenceService();
$service->detectAndCreateAutoGeofences($user);

// 5. Check results
$fenceCount = Geofence::where('user_id', $user->id)->where('is_auto', true)->count();

if ($fenceCount > 0) {
    echo "SUCCESS: Created $fenceCount auto geofence(s).\n";
    $fence = Geofence::where('user_id', $user->id)->first();
    echo "  -> Location: {$fence->latitude}, {$fence->longitude}\n";
    echo "  -> Risk Score: {$fence->risk_score}\n";
} else {
    echo "FAILURE: No auto geofences created.\n";

    // Debug info
    echo "Debug: Logs created:\n";
    foreach (SmokingLog::where('user_id', $user->id)->get() as $log) {
        echo "  - {$log->latitude}, {$log->longitude}\n";
    }
}

// Cleanup
$user->delete();
