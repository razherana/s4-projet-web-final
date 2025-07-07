<?php

use app\controllers\ApiExampleController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function() use ($app) {
	$app->render('welcome', [ 'message' => 'You are gonna do great things!' ]);
});


