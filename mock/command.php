<?php
require __DIR__.'/../libs/autoload.php';

\Slim\Environment::mock(array(
    'PATH_INFO'=>'/command/raptor'
));

$app = new Raptor\Raptor(array(
    'mock'=>true
));

$app->add(new \Slim\Middleware\ContentTypes());

$app->run();
?>