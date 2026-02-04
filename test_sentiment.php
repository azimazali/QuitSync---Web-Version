<?php

use App\Services\SentimentService;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Sentiment Service...\n";

$service = new SentimentService();
$text = "I am feeling great today! This is a wonderful day.";
echo "Analyzing text: $text\n";

$result = $service->analyze($text);

echo "Result:\n";
print_r($result);

echo "\nCheck standard output and logs for more details.\n";
