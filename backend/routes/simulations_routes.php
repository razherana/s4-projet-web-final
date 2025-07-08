<?php

use controllers\SimulationsController;

Flight::route('GET /simulations', [SimulationsController::class, 'getAll']);
Flight::route('GET /simulations/@id', [SimulationsController::class, 'getById']);
Flight::route('GET /simulations/client/@clientId', [SimulationsController::class, 'getByClient']);
Flight::route('POST /simulations', [SimulationsController::class, 'create']);
Flight::route('PUT /simulations/@id', [SimulationsController::class, 'update']);
Flight::route('DELETE /simulations/@id', [SimulationsController::class, 'delete']);
Flight::route('DELETE /simulations/client/@clientId', [SimulationsController::class, 'deleteByClient']);
