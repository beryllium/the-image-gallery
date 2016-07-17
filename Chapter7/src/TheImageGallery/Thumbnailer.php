<?php

namespace TheImageGallery;

use Imanee\Imanee;

class Thumbnailer
{
    public $imageLocation;
    public $thumbLocation;

    const SMALL    = 'small';
    const MEDIUM   = 'medium';
    const ORIGINAL = 'original';

    public function __construct($imageLocation, $thumbnailLocation)
    {
        $this->imageLocation = $imageLocation;
        $this->thumbLocation = $thumbnailLocation;
    }

    public function pathFromId($id, $extension = 'jpg')
    {
        return $this->imageLocation . DIRECTORY_SEPARATOR
        . 'img_' . $id . '.' . $extension;
    }

    public function thumbPath($id, $prefix, $extension = 'jpg')
    {
        return $this->thumbLocation . DIRECTORY_SEPARATOR
        . $prefix . '_' . $id . '.' . $extension;
    }

    public function createFromId($id, $size)
    {
        $path = $this->pathFromId($id);

        if (!file_exists($path)) {
            throw new \RuntimeException('File not found');
        }

        $thumb_prefix = null;
        $thumb_width  = null;
        $thumb_height = null;

        switch ($size) {
            default:
                throw new \RuntimeException('Unknown size');
                break;

            case self::SMALL:
                $thumb_prefix = 'sm';
                $thumb_width  = 320;
                $thumb_height = 240;
                break;

            case self::MEDIUM:
                $thumb_prefix = 'md';
                $thumb_width  = 1024;
                $thumb_height = 768;
                break;

            case self::ORIGINAL:
                return $path;
                break;
        }

        $thumb_path = $this->thumbPath($id, $thumb_prefix);
        if (!file_exists($thumb_path)) {
            $img = new Imanee($path);
            $img->thumbnail($thumb_width, $thumb_height, true);
            file_put_contents($thumb_path, $img->output('jpg'));
        }

        return $thumb_path;
    }
}