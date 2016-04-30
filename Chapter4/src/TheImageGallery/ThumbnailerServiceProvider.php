<?php

namespace TheImageGallery;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class ThumbnailerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['thumbnailer'] = function () use ($app) {
            return new Thumbnailer($app['image_location'], $app['thumbnail_location']);
        };
    }
}
