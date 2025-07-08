<?php

require 'vendor/autoload.php';

require 'env.php';
require 'db.php';

// Import all routes
require 'routes/auth_routes.php';
require 'routes/fonds_routes.php';
require 'routes/source_fonds_routes.php';
require 'routes/type_prets_routes.php';
require 'routes/clients_routes.php';
require 'routes/prets_routes.php';
require 'routes/pret_retour_historiques_routes.php';
require 'routes/users_routes.php';
require 'routes/fond_historiques_routes.php';
require 'routes/pdf_export_routes.php';
require 'routes/simulations_routes.php';

// Handle CORS preflight requests (OPTIONS method)
Flight::route('OPTIONS /*', function() {
    Flight::response()
        ->header('Access-Control-Allow-Origin', '*') // Adjust for specific origins in production
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Tracy-Ajax')
        ->header('Access-Control-Max-Age', '86400')
        ->status(204) // No Content for OPTIONS
        ->send();
});

// Add CORS headers to all responses
Flight::before('start', function() {
    // CORS headers for all requests
    Flight::response()
        ->header('Access-Control-Allow-Origin', '*') // Adjust for specific origins in production
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Tracy-Ajax');

    // Additional security headers
    Flight::response()
        ->header('X-Frame-Options', 'SAMEORIGIN')
        ->header("Content-Security-Policy", "default-src 'self'")
        ->header('X-XSS-Protection', '1; mode=block')
        ->header('X-Content-Type-Options', 'nosniff')
        ->header('Referrer-Policy', 'no-referrer-when-downgrade')
        ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload')
        ->header('Permissions-Policy', 'geolocation=()');
});

Flight::start();
