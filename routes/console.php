<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fix:risks', function () {
    $this->info("Starting re-analysis of posts...");

    $service = new \App\Services\SentimentService();
    // Scan 'unknown' AND 'low' because some 'low' might actually be 'moderate' with new logic
    $posts = \App\Models\Post::whereIn('risk_level', ['unknown', 'low'])->get();

    if ($posts->isEmpty()) {
        $this->info("No posts found to update.");
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
