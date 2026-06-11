<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Http;

class Request
{
    protected string $httpUri;
    protected string $httpLocation;
    protected array $httpParameters;

    public function __construct(string $httpUri)
    {

        $httpLocation = explode('?', $httpUri)[0];

        $param = explode('?', $httpUri)[1];
        $httpParameters = [];
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

        $this->httpUri = $httpUri;
        $this->httpLocation = $httpLocation;
        $this->httpParameters = $httpParameters;
    }

    public function getHttpLocation(): string
    {
        return $this->httpLocation;
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

    public function setHttpLocation(string $httpLocation)
    {
        $this->httpLocation = $httpLocation;
    }

    public function setHttpParameters(array $httpParameters)
    {
        $this->httpParameters = $httpParameters;
    }
}
