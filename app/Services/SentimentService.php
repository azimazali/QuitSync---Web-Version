<?php

namespace App\Services;

use Google\Cloud\Language\V1\Document;
use Google\Cloud\Language\V1\Document\Type;
use Google\Cloud\Language\V1\Client\LanguageServiceClient;
use Google\Cloud\Language\V1\AnalyzeSentimentRequest;
use Illuminate\Support\Facades\Log;

class SentimentService
{
    protected $client;

    public function __construct()
    {
        $credentialsPath = storage_path('google-cloud-credentials.json');

        if (!file_exists($credentialsPath)) {
            Log::error("Google Cloud Credentials file NOT found at: " . $credentialsPath);
        } else {
            // Explicitly set the environment variable to the absolute path
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
        }

        // Fix for Laragon/Local SSL issues (cURL error 77)
        // Bypass SSL verification for local dev
        $httpConfig = ['verify' => false];

        try {
            $this->client = new LanguageServiceClient([
                'credentials' => $credentialsPath,
                'transportConfig' => [
                    'rest' => [
                        'http' => $httpConfig
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to initialize Google Cloud Language Client: " . $e->getMessage());
            $this->client = null;
        }
    }

    public function analyze(string $text): array
    {
        if (!$this->client) {
            return [
                'score' => 0.0,
                'magnitude' => 0.0,
                'risk_level' => 'unknown' // fallback
            ];
        }

        try {
            $document = (new Document())
                ->setContent($text)
                ->setType(Type::PLAIN_TEXT);


            $request = (new AnalyzeSentimentRequest())
                ->setDocument($document);

            $response = $this->client->analyzeSentiment($request);
            $sentiment = $response->getDocumentSentiment();

            $score = $sentiment->getScore();
            $magnitude = $sentiment->getMagnitude();
            $risk = $this->calculateRisk($score);

            return [
                'score' => $score,
                'magnitude' => $magnitude,
                'risk_level' => $risk,
            ];

        } catch (\Exception $e) {
            Log::error("Sentiment Analysis Error: " . $e->getMessage());
            return [
                'score' => 0.0,
                'magnitude' => 0.0,
                'risk_level' => 'unknown'
            ];
        }
    }

    protected function calculateRisk(float $score): string
    {
        // Score ranges from -1.0 (negative) to 1.0 (positive)
        if ($score < -0.6) {
            return 'high';
        } elseif ($score < -0.25) {
            return 'moderate';
        } else {
            return 'low';
        }
    }
}
