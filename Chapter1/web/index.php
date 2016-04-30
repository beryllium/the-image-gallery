<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Declare our primary action
$app->get('/', function() use ($app) {
    return new Response('Mr Watson, come here, I want to see you.', 200);
});

$app->run();
