<?php

namespace S\HttpClient\Abstracts;

interface Client
{
    public function send(Request $request): Response;
}