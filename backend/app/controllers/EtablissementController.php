<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Etablissement;
use flight\Engine;
use PDO;

class EtablissementController
{
    /** @var Engine */
    protected Engine $app;
    protected PDO $db;

    /**
     * Constructor
     */
    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->db = $app->db();
    }

    /**
     * Get all etablissements
     */
    public function getAllEtablissements(): void
    {
        $etablissements = Etablissement::getAllEtablissements();
        $this->app->json($etablissements);  
    }
}
