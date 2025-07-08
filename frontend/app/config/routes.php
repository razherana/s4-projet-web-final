<?php

use flight\Engine;
use flight\net\Router;
use app\controllers\AuthController;
use app\controllers\AdminController;
use app\controllers\ClientController;
use app\controllers\FondsController;
use app\controllers\SourceFondsController;
use app\controllers\TypePretsController;
use app\controllers\ClientsController;
use app\controllers\PretsController;
use app\controllers\PretRetourHistoriquesController;
use app\controllers\UsersController;
use app\controllers\FondHistoriquesController;
use app\controllers\PdfExportController;
use app\middlewares\AdminMiddleware;
use app\middlewares\ClientMiddleware;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Home redirect
$router->get('/', function () use ($app) {
  
  if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['user_id'] === 1) {
      $app->redirect('/admin/dashboard');
    } else {
      $app->redirect('/client/dashboard');
    }
  } else {
    $app->redirect('/login');
  }
});

// Protected admin routes
$router->group('/admin', function() use ($router, $app) {
  $router->get('/', function () use ($app) {
    $app->redirect('/admin/dashboard');
  });
  $router->get('/dashboard', [AdminController::class, 'dashboard']);
  $router->get('/interets', [AdminController::class, 'interets']);
  $router->get('/prets', [AdminController::class, 'prets']);
  $router->post('/prets/create', [AdminController::class, 'createLoan']);
  $router->get('/clients', [AdminController::class, 'clients']);
  $router->post('/clients/create', [AdminController::class, 'createClient']);
  $router->post('/clients/payment', [AdminController::class, 'processPayment']);
  $router->get('/fonds', [AdminController::class, 'fonds']);
  $router->get('/settings', [AdminController::class, 'settings']);
}, [AdminMiddleware::class]);

// Protected client routes
$router->group('/client', function() use ($router, $app) {
  $router->get('/', function () use ($app) {
    $app->redirect('/client/dashboard');
  });
  $router->get('/dashboard', [ClientController::class, 'dashboard']);
  $router->get('/loans', [ClientController::class, 'loans']);
  $router->post('/loans', [ClientController::class, 'processPayment']);
  $router->get('/simulate', [ClientController::class, 'simulate']);
  $router->post('/loans/create', [ClientController::class, 'createLoan']);
}, [ClientMiddleware::class]);

// Fonds routes
$router->get('/fonds', [FondsController::class, 'list']);
$router->post('/fonds', [FondsController::class, 'store']);
$router->put('/fonds/@id', [FondsController::class, 'update']);
$router->delete('/fonds/@id', [FondsController::class, 'delete']);

// Source Fonds routes
$router->get('/source-fonds', [SourceFondsController::class, 'list']);
$router->post('/source-fonds', [SourceFondsController::class, 'store']);
$router->put('/source-fonds/@id', [SourceFondsController::class, 'update']);
$router->delete('/source-fonds/@id', [SourceFondsController::class, 'delete']);

// Type Prets routes
$router->get('/type-prets', [TypePretsController::class, 'list']);
$router->post('/type-prets', [TypePretsController::class, 'store']);
$router->put('/type-prets/@id', [TypePretsController::class, 'update']);
$router->delete('/type-prets/@id', [TypePretsController::class, 'delete']);

// Clients routes
$router->get('/clients', [ClientsController::class, 'list']);
$router->post('/clients', [ClientsController::class, 'store']);
$router->put('/clients/@id', [ClientsController::class, 'update']);
$router->delete('/clients/@id', [ClientsController::class, 'delete']);

// Prets routes
$router->get('/prets', [PretsController::class, 'list']);
$router->post('/prets', [PretsController::class, 'store']);
$router->put('/prets/@id', [PretsController::class, 'update']);
$router->delete('/prets/@id', [PretsController::class, 'delete']);

// Pret Retour Historiques routes
$router->get('/pret-retour-historiques', [PretRetourHistoriquesController::class, 'list']);
$router->post('/pret-retour-historiques', [PretRetourHistoriquesController::class, 'store']);
$router->put('/pret-retour-historiques/@id', [PretRetourHistoriquesController::class, 'update']);
$router->delete('/pret-retour-historiques/@id', [PretRetourHistoriquesController::class, 'delete']);

// Interets route
$router->get('/interets', [PretRetourHistoriquesController::class, 'interets']);

// Users routes
$router->get('/users', [UsersController::class, 'list']);
$router->post('/users', [UsersController::class, 'store']);
$router->put('/users/@id', [UsersController::class, 'update']);
$router->delete('/users/@id', [UsersController::class, 'delete']);

// Fond Historiques routes
$router->get('/fond-historiques', [FondHistoriquesController::class, 'list']);
$router->post('/fond-historiques', [FondHistoriquesController::class, 'store']);
$router->put('/fond-historiques/@id', [FondHistoriquesController::class, 'update']);
$router->delete('/fond-historiques/@id', [FondHistoriquesController::class, 'delete']);
$router->get('/fond-historiques/create', [FondHistoriquesController::class, 'create']);
$router->post('/fond-historiques/create', [FondHistoriquesController::class, 'store']);

// PDF Export routes
$router->get('/export/loan/@id/payments', [PdfExportController::class, 'exportLoanPayments']);
