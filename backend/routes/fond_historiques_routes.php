<?php

use controllers\FondHistoriquesController;

Flight::route('GET /fond-historiques', [FondHistoriquesController::class, 'getAll']);
Flight::route('GET /fond-historiques/@id', [FondHistoriquesController::class, 'getById']);
Flight::route('POST /fond-historiques', [FondHistoriquesController::class, 'create']);
Flight::route('PUT /fond-historiques/@id', [FondHistoriquesController::class, 'update']);
Flight::route('DELETE /fond-historiques/@id', [FondHistoriquesController::class, 'delete']);
