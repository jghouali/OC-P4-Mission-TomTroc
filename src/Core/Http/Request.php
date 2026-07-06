<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Http;

use RuntimeException;

class Request
{
    protected string $httpMethod;
    protected string $httpUri;
    protected string $httpLocation;
    protected array $httpParameters;

    public function __construct(string $stringRequest, ?array $postData = null)
    {
        $allowedMethods = [
            'GET',
            'POST',
        ];

        $httpMethod = explode(' ', $stringRequest, 2)[0];
        $httpUri = explode(' ', $stringRequest, 2)[1];

        if (!in_array($httpMethod, $allowedMethods, true)) {
            throw new RuntimeException("Method '$httpMethod' not allowed for request '$httpUri'", 405);
        }

        if ($httpMethod === 'POST' && $postData === null) {
            throw new RuntimeException('Can\'t process \'POST\' request whitout \'POST\' data.', 400);
        }

        $httpLocation = explode('?', $httpUri)[0];

        if ($httpMethod === 'GET') {
            $param = substr(str_replace($httpLocation, '', $httpUri), 1);

            $httpParameters = [];
            if (preg_match('/\w=/', $param)) {
                foreach (
                    array_map(
                        function ($n) {
                            return explode('=', $n);
                        },
                        explode('&', $param)
                    ) as $array
                ) {
                    $httpParameters[$array[0]] = $array[1];
                }
            }
        } else {
            $httpParameters = array_merge($postData, $_FILES);
        }

        $this->httpMethod = $httpMethod;
        $this->httpUri = $httpUri;
        $this->httpLocation = $httpLocation;
        $this->httpParameters = $httpParameters;
    }

    public function getHttpLocation(): string
    {
        return $this->httpLocation;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getHttpParameters(bool $inArray): string|array
    {
        if ($inArray) {
            return $this->httpParameters;
        } else {
            $stringContent = '';
            foreach ($this->httpParameters as $param => $value) {
                $stringContent = "$stringContent" . "$param=$value";
            }
            return $stringContent;
        }
    }

    public function setHttpMethod(string $httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    public function setHttpLocation(string $httpLocation)
    {
        $this->httpLocation = $httpLocation;
    }

    public function setHttpParameters(array $httpParameters)
    {
        $this->httpParameters = $httpParameters;
    }
}
