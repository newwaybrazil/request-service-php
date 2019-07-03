<?php

namespace RequestService;

class BaseRequest
{
    public $jsonRequest = false;

    /**
     * method prepareBody
     * prepare body request before send
     * @param array $body
     * @return array
     */
    public function prepareBody(array $body = []): array
    {
        if ($this->jsonRequest) {
            return [
                'json' => $body
            ];
        }

        return $body;
    }

    /**
     * method prepareHeader
     * prepare header request before send
     * @param array $header
     * @return array
     */
    public function prepareHeader(array $header): array
    {
        if ($this->jsonRequest) {
            $header = array_merge(
                $header,
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            );
        }

        return [
            'headers' => $header,
        ];
    }

    /**
     * method prepareUrl
     * prepare the endpoint before send
     * @param string $url
     * @param string $uri
     * @return string
     */
    public function prepareUrl(string $url, string $uri): string
    {
        $protocol = '';
        if (strpos($url, 'http') !== false) {
            $url = explode('//', $url);

            $protocol = $url[0].'//';
            $url = $url[1];
        }

        $url = str_replace('/', '', $url);

        if (strpos($uri, '/') === false || strpos($uri, '/') > 0) {
            $uri = "/$uri";
        }

        return "$protocol$url$uri";
    }
}
