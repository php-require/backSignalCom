<?php

namespace App\Service;

use App\Exception\InvalidRequestException;

class Http
{
    const HTTP_STATUS = [
        200 => 'OK',
        400 => 'Bad Request',
        502 => 'Wrong Request',
    ];

    /**
     * @throws InvalidRequestException
     */
    public static function getRequest(array $constraints = []): array
    {
        $request_body = file_get_contents('php://input');
        $request = (array)json_decode($request_body, true);   
    
        if (!Validation::checkArray($request, $constraints)) {    
            throw new InvalidRequestException();
        }
        return $request;
    }
   
    public static function optionsResponse(array $methods): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: '.join(', ', $methods));
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Max-Age: 600');
        exit(0);
    }

    /**
     * @param array|object $data
     * @param int $statusCode
     */
    public static function response($data, int $statusCode = 200): void
    {
        header('HTTP/1.1 '.$statusCode.' '.self::HTTP_STATUS[$statusCode]);
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(         
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        exit(0);
    }

    public static function errorResponse(string $error, int $statusCode = 400): void
    {
        self::response(['error' => $error], $statusCode);
    }
}
