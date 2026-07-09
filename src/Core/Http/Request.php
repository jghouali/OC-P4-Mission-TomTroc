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

    public function __construct(string $stringRequest)
    {
        $allowedMethods = [
            'GET',
            'POST',
        ];

        $explodedRequest = explode(' ', $stringRequest);
        if (count($explodedRequest) !== 2) {
            throw new RuntimeException('Malformed Request', 400);
        }

        $httpMethod = $explodedRequest[0];
        if (!in_array($httpMethod, $allowedMethods, true)) {
            throw new RuntimeException("Method '$httpMethod' not allowed", 405);
        }

        $httpUri = $explodedRequest[1];

        if ($httpMethod === 'POST') {
            if (!isset($_POST) || $_POST === null || $_POST === [] || $_POST === '') {
                throw new RuntimeException(
                    'Can\'t process \'POST\' request whitout \'POST\' data.',
                    400
                );
            }
        }

        $explodedUri = explode('?', $httpUri);
        if (count($explodedUri) !== 2 && count($explodedUri) !== 1) {
            throw new RuntimeException('Malformed Request', 400);
        }

        $httpLocation = $explodedUri[0];
        if (substr($httpLocation, 0, 1) !== '/') {
            throw new RuntimeException('Http Location doesnt start with \'/\'', 400);
        }

        if ($httpMethod === 'GET' && count($explodedUri) === 2) {
            $param = $explodedUri[1];

            if (!preg_match('/^[a-zA-Z0-9\-\_]+=[a-zA-Z0-9\-\_]+(?:&[a-zA-Z0-9\-\_]+=[a-zA-Z0-9\-\_]+)*$/', $param)) {
                throw new RuntimeException('Malformed Parameters', 400);
            }

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
            $httpParameters = array_merge($_POST, $_FILES);
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
            $arrayContent = [];
            $stringContent = '';
            foreach ($this->httpParameters as $param => $value) {
                $arrayContent[] = $param . '=' . $value;
            }
            $stringContent = implode('&', $arrayContent);
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
