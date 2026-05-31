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

class OpenAiService implements AIServiceInterface
{
    protected string $apiKey;
    protected string $model;
    protected float $temperature;

    public function __construct(string $apiKey, string $model = 'gpt-4-1-mini', float $temperature = 0.7)
    {
        $this->apiKey      = $apiKey;
        $this->model       = $model;
        $this->temperature = $temperature;
    }

    public function generate(string $prompt, array $options = []): void
    {
        $endpoint = 'https://api.openai.com/v1/responses';

        $body = [
            'stream'       => true,
            'model'        => $options['model'] ?? $this->model,
            'temperature'  => $options['temperature'] ?? $this->temperature,
            'instructions' => $options['instructions'] ?? '',
            'input'        => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'store' => false,
        ];

        $headers =  [
            'Authorization: Bearer ' . $this->apiKey,
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

                        if ($json && isset($json['type']) && $json['type'] === 'response.output_text.delta') {
                            echo "event: chunk\n";
                            echo "data: " . rtrim($json['delta'], "\r\n") . "\n\n";
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
