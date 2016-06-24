<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

return $app;