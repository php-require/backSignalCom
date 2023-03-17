<?php

use App\Exception\InvalidRequestException;
use App\Exception\UpstreamException;
use App\Service\Http;
use App\Service\HttpUpstream;
use App\Service\Validation;

require_once __DIR__.'/../../vendor/autoload.php';


if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
    Http::optionsResponse(['OPTIONS', 'POST']);
}

try {
    $request = Http::getRequest(
        [
            'requestId' => [Validation::class, 'isRequestId'],
        ]
    );
} catch (InvalidRequestException $e) {
    Http::errorResponse('Неверно указан requestId');
}