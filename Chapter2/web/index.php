<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Declare our primary action
$app->get('/', function() use ($app) {
    $upload_form = <<<EOF
    <html>
    <body>
    <form enctype="multipart/form-data" action="" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
        Upload this file:
    <br><br>
    <input name="image" type="file" />
    <br><br>
        <input type="submit" value="Send File" />
    </form>
    </body>
    </html>
EOF;
    return $upload_form;
});

$app->post('/', function(Request $request) use ($app) {
    $file_bag = $request->files;

    if ($file_bag->has('image')) {
        $image = $file_bag->get('image');
        $image->move(
            $app['upload_folder'],
            tempnam($app['upload_folder'], 'img_')
        );
    }

    // Redirect the user to the gallery page
    return new RedirectResponse('/gallery', 302);
});

$app->get('/img/{name}', function($name, Request $request) use ($app) {
    if (!file_exists($app['upload_folder'] . '/' . $name)) {
        throw new \Exception('File not found');
    }

    return new BinaryFileResponse($app['upload_folder'] . '/' . $name);
});

$app->get('/gallery/', function() use ($app) {
    $images = glob($app['upload_folder'] . '/img*');

    $out = '<html><body>';

    foreach($images as $img) {
        $out .= '<img src="/img/' . basename($img) . '"><br><br>';
    }

    $out .= '</body></html>';

    return $out;
});

$app->run();
