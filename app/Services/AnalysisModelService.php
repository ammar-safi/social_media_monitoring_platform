<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalysisModelService
{
    /**
     * The base URL of the external analysis service.
     */
    protected string $baseUrl;

    /**
     * Default timeout for requests in seconds.
     */
    protected int $timeout;

    /**
     * AnalysisModelService constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.analysis', env('ANALYSIS_SERVICE'));
        $this->timeout = (int) config('services.analysis_timeout', 30);
    }

    /**
     * Sends a POST request to the external API.
     */
    public function post(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('post', $endpoint, $params);
    }

    /**
     * Sends a GET request to the external API.
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('get', $endpoint, $params);
    }

    /**
     * Sends a PUT request to the external API.
     */
    public function put(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('put', $endpoint, $params);
    }

    /**
     * Sends a PATCH request to the external API.
     */
    public function patch(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('patch', $endpoint, $params);
    }

    /**
     * Sends a DELETE request to the external API.
     */
    public function delete(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('delete', $endpoint, $params);
    }

    /**
     * Generalized function to handle all request types.
     */
    protected function  sendRequest(string $method, string $endpoint, array $params = []): array
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->$method($url, $params);

            $response->throw();

            $responseData = $response->json() ?? [];


            if (isset($responseData['status']) && !$responseData['status']) {
                $message = $responseData['message'] ?? 'An unknown error occurred';
                Log::error("API Error [{$method}] {$endpoint}", [
                    'message' => $message,
                    'params' => $params,
                ]);

                return ['error' => $message, 'success' => false];
            }

            return array_merge($responseData, ['success' => true]);
        } catch (RequestException $e) {
            Log::error("HTTP Request Failed [{$method}] {$endpoint}", [
                'message' => $e->getMessage(),
                'status' => $e->response?->status(),
                'body' => $e->response?->body(),
            ]);

            return [
                'error' => 'Failed to communicate with analysis service',
                'success' => false,
            ];
        } catch (\Exception $e) {
            Log::error("Unexpected Error [{$method}] {$endpoint}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'error' => 'An unexpected error occurred',
                'success' => false,
            ];
        }
    }
}
