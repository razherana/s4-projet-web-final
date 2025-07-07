<?php

use app\controllers\EtablissementController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function () {
  echo "Test";
});

$router->group('/etablissements', function () use ($router) {
    $router->get('/', [EtablissementController::class, 'getAllEtablissements']);
});