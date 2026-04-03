<?php

declare(strict_types=1);

//backend\breco\config\Seeds\LocationsSeed.php

use Migrations\BaseSeed;

/**
 * Locations seed.
 */
class LocationsSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $townsResult = $this->query('SELECT id, name FROM towns');
        $towns = [];

        foreach ($townsResult as $town) {
            $towns[$town['name']] = $town['id'];
        }

        if (empty($towns)) {
            echo "❌ Erreur : Aucune ville trouvée. Lancez d'abord TownsSeed\n";
            return;
        }

        $file = fopen('/app/csv/locations_import.csv', 'r');

        if (!$file) {
            echo "Erreur : fichier locations_import.csv introuvable\n";
            return;
        }

        fgetcsv($file, 0, ',', '"', '\\'); // Skip header

        $data = [];
        $count = 0;
        $errors = 0;

        while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
            $townName = $row[4];

            if (!isset($towns[$townName])) {
                echo "⚠️  Ville non trouvée : $townName\n";
                $errors++;
                continue;
            }

            $data[] = [
                'town_id' => $towns[$townName],
                'name' => $row[0],
                'address' => $row[1],
                'gps_lat' => $row[2],
                'gps_lng' => $row[3],
                'type' => $row[5],
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ];
            $count++;
        }

        fclose($file);

        $table = $this->table('locations');
        $table->insert($data)->save();

        echo "✅ $count lieux importés\n";
        if ($errors > 0) {
            echo "⚠️  $errors erreurs (villes non trouvées)\n";
        }
    }
}
