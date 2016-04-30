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
    $db       = $app['db'];
    $file_bag = $request->files;

    if ($file_bag->has('image')) {
        $image = $file_bag->get('image');
        $info  = getimagesize($image->getPathname());

        if (!$info) {
            throw new Exception('Bad image file');
        }

        // create database entry
        $query = "INSERT INTO images (original_name, height, width, type, date_added) VALUES (:name, :height, :width, :type, datetime('now'))";
        $data = [
            'name'   => $image->getClientOriginalName(),
            'height' => $info[1],
            'width'  => $info[0],
            'type'   => $image->getMimeType()
        ];
        $result = $db->executeQuery($query, $data);

        // get the image ID
        $id = $db->lastInsertId();

        // move the file to the storage location, using the ID as the filename
        $image->move($app['upload_folder'], 'img_' . $id . '.jpg');
    }

    // This is just temporary.
    // Replace with a RedirectResponse to Gallery
    return print_r($request->files, true);
});

$app->get('/img/{name}/{size}', function($name, $size, Request $request) use ($app) {
    $prefix    = $app['upload_folder'] . '/';
    $full_name = $prefix . $name;

    if (!file_exists($app['upload_folder'] . '/' . $name)) {
        throw new \Exception('File not found');
    }

    $thumb_name   = '';
    $thumb_width  = null;
    $thumb_height = null;

    switch ($size) {
        default:
        case 'small':
            $thumb_name   = $prefix . 'sm_' . $name . '.jpg';
            $thumb_width  = 320;
            $thumb_height = 240;
            break;

        case 'medium':
            $thumb_name   = $prefix . 'md_' . $name . '.jpg';
            $thumb_width  = 1024;
            $thumb_height = 768;
            break;
    }

    $response = null;

    if ('original' == $size) {
        $response = new BinaryFileResponse($full_name);
    } else {
        if (!file_exists($thumb_name)) {
            $img = new \Imanee\Imanee($full_name);
            $img->thumbnail($thumb_width, $thumb_height, true);
            file_put_contents($thumb_name, $img->output('jpg'));
        }

        $response = new BinaryFileResponse($thumb_name);
        $response->headers->set('Content-Type', 'image/jpg');
    }

    return $response;
})->value('size', 'small');

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
