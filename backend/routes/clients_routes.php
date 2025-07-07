<?php

use controllers\ClientsController;

Flight::route('GET /clients', [ClientsController::class, 'getAll']);
Flight::route('GET /clients/@id', [ClientsController::class, 'getById']);
Flight::route('POST /clients', [ClientsController::class, 'create']);
Flight::route('PUT /clients/@id', [ClientsController::class, 'update']);
Flight::route('DELETE /clients/@id', [ClientsController::class, 'delete']);
