<?php

namespace App\Service;

use App\Exception\UpstreamException;

class HttpUpstream
{
    /**
     * @throws UpstreamException
     */
    public static function execute(array $request): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['Content-Type: application/json; charset=utf-8']
        );
        curl_setopt($ch, CURLOPT_URL, $_ENV['HTTP_UPSTREAM_URL']);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode(
                $request,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new UpstreamException($error);
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if (is_array($result)) {
            return $result;
        }

        throw new UpstreamException('Result is not an array');
    }
}
