<?php

require_once __DIR__ . '/vendor/autoload.php';

use Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider;

$app = new Silex\Application();

$mongoDB = new Doctrine\MongoDB\Connection();
$mongoDB->initialize();

// register Provider and configure DB
$app->register(new DoctrineMongoDbProvider(), array(
   'mongodb.options' => array(
       'server' => 'mongodb://localhost:27017',
       'options' => array(
           'db' => 'stories'
       )
   )
));

// try out app
$app->get('/start', function() use ($app){
   return $app->escape('welcome');
});


// create
$app->get('/create/story/{name}', function ($name) use ($app) {
   $document = array('name' => $name);

   $result = $app['mongodb']
       ->selectDatabase('stories')
       ->selectCollection('stories')
       ->insert($document);

   if ($result) {
       return $app->escape('The story with name: ' . $name . ' has been inserted succesfully');
   }
});


// read
$app->get('/find/story/{name}', function ($name) use ($app) {

   $result = $app['mongodb']
       ->selectDatabase('stories')
       ->selectCollection('stories')
       ->find(array('name' => $name))
       ->toArray();

   return $app->json($result);
});


// read all
$app->get('/findall/stories/', function () use ($app) {

    $result = $app['mongodb']
        ->selectDatabase('stories')
        ->selectCollection('stories')
        ->find()
        ->sort(array('_id' => 1))
        ->toArray();

    return $app->json($result);
});


// update
$app->get('/update/story/{oldName}/{newName}', function ($oldName, $newName) use ($app) {

    $result = $app['mongodb']
        ->selectDatabase('stories')
        ->selectCollection('stories')
        ->update(array('name' => $oldName), array('name' => $newName));

    if ($result) {
        return $app->escape('The story with name: ' . $oldName . ' has been updated succesfully with the new name: ' . $newName);
    }
});


// remove
$app->get('/remove/story/{name}', function ($name) use ($app) {

    $result = $app['mongodb']
        ->selectDatabase('stories')
        ->selectCollection('stories')
        ->remove(array('name' => $name));

    if ($result) {
        return $app->escape('The story with name: ' .  $name . ' has been removed sucessfully');
    }

});

$app->run();