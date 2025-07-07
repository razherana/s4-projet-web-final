<?php

use controllers\FondsController;

Flight::route('GET /fonds', [FondsController::class, 'getAll']);
Flight::route('GET /fonds/@id', [FondsController::class, 'getById']);
Flight::route('POST /fonds', [FondsController::class, 'create']);
Flight::route('PUT /fonds/@id', [FondsController::class, 'update']);
Flight::route('DELETE /fonds/@id', [FondsController::class, 'delete']);
