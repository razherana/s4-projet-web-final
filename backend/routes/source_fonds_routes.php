<?php

use controllers\SourceFondsController;

Flight::route('GET /source-fonds', [SourceFondsController::class, 'getAll']);
Flight::route('GET /source-fonds/@id', [SourceFondsController::class, 'getById']);
Flight::route('POST /source-fonds', [SourceFondsController::class, 'create']);
Flight::route('PUT /source-fonds/@id', [SourceFondsController::class, 'update']);
Flight::route('DELETE /source-fonds/@id', [SourceFondsController::class, 'delete']);
