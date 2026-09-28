<?php

namespace App\Services\MvelLab;

use Illuminate\Support\Facades\Http;
use Exception;

class MvelExecutorService
{
    /**
     * @return array [ 'result', 'type', 'execution_time', 'error' ]
     */
    public function execute(string $expression, array $variables = []): array
    {
        $startTime = microtime(true);
        $url = config('services.mvel.url', 'http://127.0.0.1:8080/execute');
        $timeout = config('services.mvel.timeout', 5);

        try {
            $response = Http::timeout($timeout)->post($url, [
                'expression' => $expression,
                'variables' => $variables,
            ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'result' => $data['result'] ?? null,
                    'result_type' => $data['type'] ?? 'unknown',
                    'execution_time' => $data['execution_time'] ?? $duration,
                    'error' => null,
                ];
            }

            return [
                'result' => null,
                'result_type' => null,
                'execution_time' => $duration,
                'error' => $response->json('error') ?? 'Execution failed with status ' . $response->status(),
            ];

        } catch (Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);
            return [
                'result' => null,
                'result_type' => null,
                'execution_time' => $duration,
                'error' => 'Connection to MVEL executor failed: ' . $e->getMessage(),
            ];
        }
    }
}

