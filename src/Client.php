<?php

namespace S\HttpClient;

use S\HttpClient\Abstracts\AbstractClient;
use S\HttpClient\Abstracts\Request;
use S\HttpClient\Abstracts\Response;

final class Client extends AbstractClient
{
    /**
     * @var callable
     */
    private $beforeSendCallback;

    /**
     * @var callable
     */
    private $afterSendCallback;

    /**
     * @var array<string, Request>
     */
    private array $requests = [];

    public function setBeforeSendCallback(callable $beforeSendCallback): void
    {
        $this->beforeSendCallback = $beforeSendCallback;
    }

    public function setAfterSendCallback(callable $afterSendCallback): void
    {
        $this->afterSendCallback = $afterSendCallback;
    }

    public function setRequest(string $name, Request $request): void
    {
        $this->requests[$name] = $request;
    }

    public function getRequest(string $name): Request
    {
        $request = $this->requests[$name] ?? null;
        if ($request == null)
            throw new \Exception('Not found request ('.$name.')');

        return $request;
    }

    public function send(Request $request, bool $disableAfterSendCallback = false): Response
    {
        if (isset($this->beforeSendCallback))
        {
            $func = $this->beforeSendCallback;
            $result = call_user_func($func);

            if ($result === false)
                return \S\HttpClient\Response::fail(0, 'Cancel request');
        }

        $response = parent::send($request);

        if (!$disableAfterSendCallback and isset($this->afterSendCallback))
        {
            $func = $this->afterSendCallback;
            $response = call_user_func($func, $this, $request, $response);
        }

        return $response;
    }
}