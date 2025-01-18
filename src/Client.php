<?php

namespace S\HttpClient;

use Exception;
use S\HttpClient\Abstracts\Request;
use S\HttpClient\Abstracts\Response;

final class Client implements Abstracts\Client
{
    private ?string $baseUrl;

    private array $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = null;
        $this->defaultHeaders = [];
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function setDefaultHeaders(array $defaultHeaders): void
    {
        $this->defaultHeaders = $defaultHeaders;
    }

    public function send(Request $request): Response
    {
        try
        {
            $headers = array_merge($this->defaultHeaders, $request->getHeaders());
            $body = $request->getBody();

            $httpHeader = array_map(function ($key, $value)
            {
                return $key . ': ' . $value;
            }, array_keys($headers), $headers);

            $postFields = $this->isContentTypeUrlencoded($request)
                ? http_build_query($body)
                : json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $options = [
                CURLOPT_HTTPHEADER => $httpHeader,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_CUSTOMREQUEST => $request->getMethod(),
                CURLOPT_POSTFIELDS => $postFields
            ];

            $url = $this->baseUrl . $request->getUrl();
            if (!empty($request->getQueryParams()))
                $url .= '?' . http_build_query($request->getQueryParams());

            $ch = curl_init($url);
            curl_setopt_array($ch, $options);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            $content = $result;
            if ($contentType == 'application/json')
                $content = json_decode($content);

            $response = $result === false
                ? \S\HttpClient\Response::fail($httpCode, curl_error($ch))
                : \S\HttpClient\Response::success($httpCode, $content);
        } catch (Exception $e)
        {
            $response = \S\HttpClient\Response::fail(0, $e->getMessage());
        }

        return $response;
    }

    private function isContentTypeUrlencoded(Request $request): bool
    {
        $headers = $request->getHeaders();
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? null;

        return $contentType == 'application/x-www-form-urlencoded';
    }
}