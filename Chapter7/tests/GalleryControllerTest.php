<?php

namespace Beryllium\TheImageGallery;

use org\bovigo\vfs\vfsStream;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GalleryControllerTest extends WebTestCase
{
    public $app;

    public function createApplication()
    {
        $app_env   = 'test';
        $this->app = require __DIR__ . '/../web/index.php';

        $this->dbSetUp();

        $this->app['vfsStream']     = vfsStream::setup('data');
        $this->app['upload_folder'] = vfsStream::url('data');

        return $this->app;
    }

    public function dbSetUp()
    {
        $this->db = $this->app['db'];
        $this->db->query('DELETE FROM images');
    }

    public function testHomepage()
    {
        $client   = $this->createClient();
        $crawler  = $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(
            200,
            $response->getStatusCode()
        );
        $this->assertContains('data-images="[]"', $response->getContent());
    }

    public function testUploads()
    {
        copy(__DIR__ . '/assets/bay_of_fundy.jpg', $this->app['upload_folder'] . '/fundy.jpg');

        $client   = $this->createClient();
        $crawler  = $client->request(
            'POST',
            '/upload/',
            array(),
            array(
                'image' => new UploadedFile(
                    $this->app['upload_folder'] . '/fundy.jpg',
                    'bay_of_fundy.jpg',
                    'image/jpeg',
                    123,
                    null,
                    true
                )
            )
        );
        $response = $client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertFileExists($this->app['upload_folder'] . '/img_1.jpg');
    }
}