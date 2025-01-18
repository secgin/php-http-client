<?php

namespace S\HttpClient\Abstracts;

interface Response
{
    public function getHttpCode(): int;

    /**
     * @return mixed
     */
    public function getContent();

    public function isSuccess(): bool;

    public function getMessage(): ?string;
}