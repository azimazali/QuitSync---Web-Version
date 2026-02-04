<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fix:risks', function () {
    $this->info("Starting re-analysis of posts with 'unknown' risk level...");

    $service = new \App\Services\SentimentService();
    $posts = \App\Models\Post::where('risk_level', 'unknown')->get();

    if ($posts->isEmpty()) {
        $this->info("No posts found with 'unknown' risk level.");
        return;
    }

    $this->info("Found " . $posts->count() . " posts to update.");

    foreach ($posts as $post) {
        $this->line("Analyzing post ID {$post->id}: {$post->title}...");

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

        $this->info("  -> New Risk Level: {$analysis['risk_level']}");
    }

    $this->info("Re-analysis complete.");
})->purpose('Re-analyze posts with unknown risk level');
