<?php

use controllers\TypePretsController;

Flight::route('GET /type-prets', [TypePretsController::class, 'getAll']);
Flight::route('GET /type-prets/@id', [TypePretsController::class, 'getById']);
Flight::route('POST /type-prets', [TypePretsController::class, 'create']);
Flight::route('PUT /type-prets/@id', [TypePretsController::class, 'update']);
Flight::route('DELETE /type-prets/@id', [TypePretsController::class, 'delete']);
