<?php

namespace S\HttpClient;

final class Response implements Abstracts\Response
{
    private int $httpCode;

    private $content;

    private ?string $message;

    private bool $success;

    /**
     * @param bool        $success
     * @param string      $httpCode
     * @param mixed       $content
     * @param string|null $message
     */
    private function __construct(bool $success, string $httpCode, $content, ?string $message)
    {
        $this->success = $success;
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->message = $message;
    }

    /**
     * @param int   $httpCode
     * @param mixed $content
     *
     * @return self
     */
    public static function success(int $httpCode, $content): self
    {
        $message = null;
        $success = $httpCode >= 200 && $httpCode < 300;

        return new Response($success, $httpCode, $content, $message);
    }

    public static function fail(int $httpCode, ?string $message, $content = null): self
    {
        return new Response(false, $httpCode, $content, $message);
    }

    #region Response Interface
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isSuccess(): bool
    {
        return $this->success and ($this->httpCode >= 200 and $this->httpCode < 300);
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
    #endregion
}