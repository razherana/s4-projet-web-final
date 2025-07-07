<?php

use controllers\PretRetourHistoriquesController;

// Interets calculation route
Flight::route('GET /pret-retour-historiques/interets', [PretRetourHistoriquesController::class, 'getInterets']);

Flight::route('GET /pret-retour-historiques', [PretRetourHistoriquesController::class, 'getAll']);
Flight::route('GET /pret-retour-historiques/@id', [PretRetourHistoriquesController::class, 'getById']);
Flight::route('POST /pret-retour-historiques', [PretRetourHistoriquesController::class, 'create']);
Flight::route('PUT /pret-retour-historiques/@id', [PretRetourHistoriquesController::class, 'update']);
Flight::route('DELETE /pret-retour-historiques/@id', [PretRetourHistoriquesController::class, 'delete']);

