<?php

namespace S\HttpClient;

final class Request implements Abstracts\Request
{
    private string $url;

    private string $method;

    private array $headers;

    private array $body;

    private array $queryParams;

    public function __construct(string $url, string $method = 'GET')
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = [];
        $this->body = [];
        $this->queryParams = [];
    }

    public static function post(string $uri): self
    {
        return new self($uri, 'POST');
    }

    public static function get(string $uri): self
    {
        return new self($uri, 'GET');
    }

    public function setBasicAuthentication(string $username, string $password): self
    {
        return $this->addHeader(
            'Authorization',
            'Basic ' . base64_encode($username . ':' . $password));
    }

    public function setBasicAuthenticationByCode(string $code): self
    {
        return $this->addHeader('Authorization', 'Basic ' . $code);
    }

    public function setBearerAuthentication(string $token): self
    {
        return $this->addHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * @param string $name
     * @param string|array $value
     * @return $this
     */
    public function addHeader(string $name, $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setQueryParams(array $params): self
    {
        foreach ($params as $key => $value)
        {
            if (strpos($this->url, "{" . $key . "}") !== false)
            {
                $this->url = @str_replace("{" . $key . "}", $value, $this->url);
                unset($params[$key]);
            }
        }

        $this->queryParams = $params;
        return $this;
    }

    #region Request Interface
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
    #endregion
}