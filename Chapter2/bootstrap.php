<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['debug']         = true;
$app['upload_folder'] = __DIR__ . '/uploads';

return $app;

