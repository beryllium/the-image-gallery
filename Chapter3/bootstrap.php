<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['debug']         = true;
$app['upload_folder'] = __DIR__ . '/uploads';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

return $app;