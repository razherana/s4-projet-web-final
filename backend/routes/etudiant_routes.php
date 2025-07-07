<?php

use controllers\EtudiantController;

Flight::route('GET /etudiants', [EtudiantController::class, 'getAll']);
Flight::route('GET /etudiants/@id', [EtudiantController::class, 'getById']);
Flight::route('POST /etudiants', [EtudiantController::class, 'create']);
Flight::route('PUT /etudiants/@id', [EtudiantController::class, 'update']);
Flight::route('DELETE /etudiants/@id', [EtudiantController::class, 'delete']);
