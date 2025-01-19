<?php

namespace S\HttpClient;

use S\HttpClient\Abstracts\AbstractClient;
use S\HttpClient\Abstracts\Request;
use S\HttpClient\Abstracts\Response;

final class Client extends AbstractClient
{
    private $beforeSendCallback;

    private $afterSendCallback;

    public function setBeforeSendCallback(callable $beforeSendCallback): void
    {
        $this->beforeSendCallback = $beforeSendCallback;
    }

    public function setAfterSendCallback(callable $afterSendCallback): void
    {
        $this->afterSendCallback = $afterSendCallback;
    }

    public function send(Request $request): Response
    {
        if (isset($this->beforeSendCallback))
        {
            $func = $this->beforeSendCallback;
            $result = call_user_func($func);

            if ($result === false)
                return \S\HttpClient\Response::fail(0, 'Cancel request');
        }

        $response = parent::send($request);

        if (isset($this->afterSendCallback))
        {
            $func = $this->afterSendCallback;
            $response = call_user_func($func, $response);
        }

        return $response;
    }
}