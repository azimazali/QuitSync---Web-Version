<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

// Clear existing data for clean test
App\Models\SmokingLog::where('user_id', $user->id)->delete();
App\Models\Geofence::where('user_id', $user->id)->delete();

echo "Creating 5 logs...\n";
for ($i = 0; $i < 5; $i++) {
    App\Models\SmokingLog::create([
        'user_id' => $user->id,
        'smoked_at' => now(),
        'latitude' => 40.7128 + ($i * 0.0001),
        'longitude' => -74.0060 + ($i * 0.0001),
        'quantity' => 1
    ]);
}

echo "Running detection...\n";
try {
    app(App\Services\GeofenceService::class)->detectAndCreateAutoGeofences($user);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$count = App\Models\Geofence::where('is_auto', true)->count();
echo "Auto Geofences Found: $count\n";

if ($count > 0) {
    $g = App\Models\Geofence::where('is_auto', true)->first();
    echo "Geofence: " . $g->radius . "m, Risk: " . $g->risk_score . "\n";
}
