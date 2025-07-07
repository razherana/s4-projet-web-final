<?php

use controllers\UsersController;

Flight::route('GET /users', [UsersController::class, 'getAll']);
Flight::route('GET /users/@id', [UsersController::class, 'getById']);
Flight::route('POST /users', [UsersController::class, 'create']);
Flight::route('PUT /users/@id', [UsersController::class, 'update']);
Flight::route('DELETE /users/@id', [UsersController::class, 'delete']);
