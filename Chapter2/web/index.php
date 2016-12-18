<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// our upload form action
$app->get('/upload/', function() use ($app) {
    $upload_form = <<<EOF
    <html>
    <body>
    <form enctype="multipart/form-data" action="" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
        Choose a Photo:
    <br><br>
    <input name="image" type="file" />
    <br><br>
        <input type="submit" value="Upload Photo" />
    </form>
    </body>
    </html>
EOF;
    return $upload_form;
});

// our action for receiving uploads
$app->post('/upload/', function(Request $request) use ($app) {
    $files = $request->files;

    if ($files->has('image')) {
        $image = $files->get('image');
        $image->move(
            $app['upload_folder'],
            tempnam($app['upload_folder'], 'img_')
        );
    }

    // Redirect the user to the gallery page
    return new RedirectResponse('/', 302);
});

// our image viewer action
$app->get('/img/{name}', function($name, Request $request) use ($app) {
    $path = realpath($app['upload_folder'] . '/' . $name);

    if (!$path || strpos($path, $app['upload_folder']) !== 0) {
        throw new \Exception('File not found');
    }

    return new BinaryFileResponse($path);
});

// our gallery action
$app->get('/', function() use ($app) {
    $images = glob($app['upload_folder'] . '/img*');

    $out = '<html><body>';

    foreach($images as $img) {
        $out .= '<img src="/img/' . basename($img) . '"><br><br>';
    }

    $out .= '<a href="/upload/">Upload Photos &raquo;</a>';
    $out .= '</body></html>';

    return $out;
});

$app->run();
