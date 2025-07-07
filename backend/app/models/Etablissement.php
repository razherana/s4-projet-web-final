<?php

namespace app\models;

class Etablissement
{
    /**
     * Get all etablissements
     *
     * @return array
     */
    public static function getAllEtablissements(): array
    {
        $db = \Flight::db();
        $stmt = $db->query("SELECT * FROM s4_final_etablissements");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}