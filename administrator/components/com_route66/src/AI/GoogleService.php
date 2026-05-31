<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\AI;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class GoogleService implements AIServiceInterface
{
    protected string $apiKey;
    protected string $model;
    protected float $temperature;

    public function __construct(string $apiKey, string $model = 'gemini-2.5-flash', float $temperature = 0.7)
    {
        $this->apiKey      = $apiKey;
        $this->model       = $model;
        $this->temperature = $temperature;
    }

    public function generate(string $prompt, array $options = []): void
    {
        $model = $options['model'] ?? $this->model;

        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':streamGenerateContent?alt=sse';

        $body = [
            'system_instruction' => [
                'parts' => [
                    'text' => $options['instructions'] ?? '',
                ],
            ],
            'contents' => [
                'parts' => [
                    'text' => $prompt,
                ],
            ],
            'generationConfig' => [
                'temperature'    => $options['temperature'] ?? $this->temperature,
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
            ],
        ];

        $headers =  [
            'x-goog-api-key: ' . $this->apiKey,
            'Content-Type: application/json',
        ];


        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION  => function ($ch, $chunk) {

                $lines = explode("\n", $chunk);

                foreach ($lines as $line) {

                    if (str_starts_with($line, 'data: ')) {

                        $data = substr($line, 6);
                        $json = json_decode($data, true);

                        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                            echo "event: chunk\n";
                            echo "data: " . rtrim($json['candidates'][0]['content']['parts'][0]['text'], "\r\n") . "\n\n";
                            @ob_flush();
                            @flush();
                        }
                    }
                }

                return \strlen($chunk);
            },
        ]);

        curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \RuntimeException(curl_error($ch));
        }

        curl_close($ch);
    }

}
