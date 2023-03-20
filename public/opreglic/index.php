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
            'name' => [Validation::class, 'isFullName'],
            'email' => [Validation::class, 'isEmail'],
            'organization' => [Validation::class, 'isOrganization'],
        ]
    );
 
} catch (InvalidRequestException $e) {
    Http::errorResponse('Данные указаны неверно');    
}

try { 
  
    $upstream_response = HttpUpstream::execute(
        [
            'reqtype' => 'opreglic',
            'content'=>  [
                'name' => urlencode($request['name']),
                'email' => urlencode($request['email']),
                'organization' => urlencode($request['organization']),
                'phone' => $request['phone'],
            ]
           // 'client_ip' => $_SERVER['REMOTE_ADDR'],
        ]
    );
    
    Http::response($upstream_response);
    
} catch (UpstreamException $e) {
    Http::errorResponse(
        'В настоящий момент сервис недоступен. Попробуйте отправить данные через некоторое время.'
    );
}
