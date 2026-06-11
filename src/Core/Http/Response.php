<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Http;

class Response
{
    protected string $httpContent;
    protected int $httpCode;
    protected array $httpHeaders;

    public function __construct(
        string $httpContent,
        int $httpCode = 200,
        array $httpHeaders = ['Content-Type:' => 'text/html']
    ) {
        $this->httpContent = $httpContent;
        $this->httpHeaders = $httpHeaders;
        $this->httpCode = $httpCode;
    }

    public function getHttpContent()
    {
        return $this->httpContent;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getHttpHeader()
    {
        return $this->httpHeaders;
    }

    public function setHttpContent(string $httpContent)
    {
        $this->httpContent = $httpContent;
    }

    public function setHttpCode(int $httpCode)
    {
        $this->httpCode = $httpCode;
    }

    public function setHttpHeader(array $httpHeaders)
    {
        $this->httpHeaders = $httpHeaders;
    }

    public function send()
    {
        http_response_code($httpCode ?? $this->httpCode);
        foreach ($httpHeaders ?? $this->httpHeaders as $header => $value) {
            header($header . ' ' . $value);
        }
        echo $httpContent ?? $this->httpContent;
    }
}
