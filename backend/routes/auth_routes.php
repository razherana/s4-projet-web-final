<?php

use controllers\AuthController;

Flight::route('POST /auth/login', [AuthController::class, 'login']);
Flight::route('POST /auth/verify', [AuthController::class, 'verify']);
Flight::route('POST /auth/logout', [AuthController::class, 'logout']);
