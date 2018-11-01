<?php
require __DIR__.'/../libs/autoload.php';

\Slim\Environment::mock(array(
	'PATH_INFO'=>'/examples/mock/yes'
));
$app = new Raptor\Raptor(array(
    'mock'=>true
));

$app->add(new \Slim\Middleware\ContentTypes());
//print_r($argv[1]);
$app->run();
?>