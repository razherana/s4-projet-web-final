<?php

require 'vendor/autoload.php';

require 'env.php';

require 'db.php';

require 'routes/etudiant_routes.php';
require 'routes/fonds_routes.php';
require 'routes/source_fonds_routes.php';
require 'routes/type_prets_routes.php';
require 'routes/clients_routes.php';
require 'routes/prets_routes.php';
require 'routes/pret_retour_historiques_routes.php';
require 'routes/users_routes.php';
require 'routes/fond_historiques_routes.php';

Flight::start();
