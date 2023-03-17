<?php

use App\Service\Http;

require_once __DIR__.'/../vendor/autoload.php';


Http::errorResponse('Сервер временно недоступен. Попробуйте повторить действие чуть позже.');
