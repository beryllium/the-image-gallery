<?php

require __DIR__ . '/vendor/autoload.php';

$app                  = new Silex\Application();
$app['env']           = isset($app_env) ? $app_env : 'dev';
$app['debug']         = true;
$app['upload_folder'] = '/uploads';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new TheImageGallery\ThumbnailerServiceProvider(), array(
    'thumbs.path.images' => $app['upload_folder'],
    'thumbs.path.thumbs' => $app['upload_folder']
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path'   => __DIR__
            . ($app['env'] === 'test' ? '/gallery_test.db' : '/gallery.db'),
    ),
));

$schema = new Doctrine\DBAL\Schema\Schema;
$images = $schema->createTable('images');

$images->addColumn('id', 'integer', array('autoincrement' => true));
$images->addColumn('original_name', 'string');
$images->addColumn('height',        'integer');
$images->addColumn('width',         'integer');
$images->addColumn('type',          'string');
$images->addColumn('date_added',    'datetime');
$images->setPrimaryKey(array('id'));

$db       = $app['db'];
$existing = $db->getSchemaManager()->createSchema();
$compare  = new Doctrine\DBAL\Schema\Comparator;
$diff     = $compare->compare($existing, $schema);

foreach ($diff->toSaveSql($db->getDatabasePlatform()) as $query) {
    $db->query($query);
}

return $app;