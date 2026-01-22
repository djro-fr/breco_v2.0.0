<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class RemovePostalCodeAndInseeCodeFromLocations extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('locations');

        // Supprimer l'index postal_code s'il existe
        try {
            $table->removeIndexByName('postal_code');
        } catch (\Exception $e) {
            // Index n'existe pas, continuer
        }

        // Supprimer l'index insee_code s'il existe
        try {
            $table->removeIndexByName('insee_code');
        } catch (\Exception $e) {
            // Index n'existe pas, continuer
        }

        // Supprimer les colonnes si elles existent
        if ($table->hasColumn('postal_code')) {
            $table->removeColumn('postal_code');
        }

        if ($table->hasColumn('insee_code')) {
            $table->removeColumn('insee_code');
        }

        $table->update();
    }
}
