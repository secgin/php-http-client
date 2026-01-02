<?php

namespace S\HttpClient\Abstracts;

interface Request
{
    public function getUrl(): string;

    public function getQueryParams(): array;

    public function getHeaders(): array;

    public function getBody(): array;

    public function getMethod(): string;

    public function getOptions(): array;
}