<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    // Redirect the user to the gallery page
    return new RedirectResponse('/gallery', 302);
});

$app->get('/img/{name}/{size}', function($name, $size, Request $request) use ($app) {
    $thumbnailer = $app['thumbnailer'];
    $pathToThumb = $thumbnailer->create($name, $size);

    // throw an exception if we haven't successfully created a thumbnail
    if (empty($pathToThumb) || !file_exists($pathToThumb)) {
        $app->abort(404, 'Image not found');
    }

    return new BinaryFileResponse($pathToThumb);
})->value('size', TheImageGallery\Thumbnailer::SMALL);

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
