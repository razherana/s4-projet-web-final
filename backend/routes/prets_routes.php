<?php

use controllers\PretsController;

Flight::route('GET /prets', [PretsController::class, 'getAll']);
Flight::route('GET /prets/@id', [PretsController::class, 'getById']);
Flight::route('POST /prets', [PretsController::class, 'create']);
Flight::route('PUT /prets/@id', [PretsController::class, 'update']);
Flight::route('DELETE /prets/@id', [PretsController::class, 'delete']);
