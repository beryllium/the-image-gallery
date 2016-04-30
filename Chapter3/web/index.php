<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Declare our primary action
$app->get('/', function() use ($app) {
    return $app['twig']->render('upload_form.html.twig');
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

    // This is just temporary.
    // Replace with a RedirectResponse to Gallery
    return print_r($request->files, true);
});

$app->get('/img/{name}', function($name, Request $request) use ($app) {
    if (!file_exists($app['upload_folder'] . '/' . $name)) {
        throw new \Exception('File not found');
    }

    $response = new BinaryFileResponse($app['upload_folder'] . '/' . $name);
    $response->headers->set('Content-Type', 'image/png');
    return $response;
});

$app->get('/view', function() use ($app) {
    $imageGlob = glob($app['upload_folder'] . '/img*');

    $images = array_map(
        function($val) { return basename($val); },
        $imageGlob
    );

    return $app['twig']->render('gallery.html.twig', array(
        'images' => $images,
    ));
});

$app->run();
