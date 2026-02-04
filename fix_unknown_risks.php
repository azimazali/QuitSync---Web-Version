<?php

use App\Models\Post;
use App\Services\SentimentService;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting re-analysis of posts with 'unknown' risk level...\n";

$service = new SentimentService();
$posts = Post::where('risk_level', 'unknown')->get();

if ($posts->isEmpty()) {
    echo "No posts found with 'unknown' risk level.\n";
    exit;
}

echo "Found " . $posts->count() . " posts to update.\n";

foreach ($posts as $post) {
    echo "Analyzing post ID {$post->id}: {$post->title}...\n";

    $analysis = $service->analyze($post->body);
    $tags = $service->analyzeEntities($post->body);
    $category = $service->classifyContent($post->body);

    $post->update([
        'sentiment_score' => $analysis['score'],
        'sentiment_magnitude' => $analysis['magnitude'],
        'risk_level' => $analysis['risk_level'],
        'tags' => $tags,
        'category' => $category,
    ]);

    echo "  -> New Risk Level: {$analysis['risk_level']}\n";
}

echo "Re-analysis complete.\n";
