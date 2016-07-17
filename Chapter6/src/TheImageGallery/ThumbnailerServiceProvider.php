<?php

namespace TheImageGallery;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ThumbnailerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $imagesSetting = 'thumbs.path.images';
        $thumbsSetting = 'thumbs.path.thumbs';

        $app[$imagesSetting] = isset($app[$imagesSetting])
            ? $app[$imagesSetting]
            : null;
        $app[$thumbsSetting] = isset($app[$thumbsSetting])
            ? $app[$thumbsSetting]
            : null;

        $app['thumbnailer'] = function () use (
            $app,
            $imagesSetting,
            $thumbsSetting
        ) {
            return new Thumbnailer($app[$imagesSetting], $app[$thumbsSetting]);
        };
    }
}
