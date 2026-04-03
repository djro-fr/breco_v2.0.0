<?php
declare(strict_types=1);
//backend\breco\config\Seeds\TownsSeed.php
use Migrations\BaseSeed;
/**
 * Towns seed.
 */
class TownsSeed extends BaseSeed
{
    public function run(): void
    {
        $file = fopen(dirname(__DIR__, 4) . '/app/csv/towns_import.csv', 'r');
        if (!$file) {
            echo "Erreur : fichier towns_import.csv introuvable\n";
            return;
        }
        fgetcsv($file, 0, ',', '"', '\\'); // Skip header
        $data = [];
        $count = 0;
        while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
            $data[] = [
                'name' => $row[0],
                'postal_code' => $row[1],
                'insee_code' => $row[2],
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ];
            $count++;
        }
        fclose($file);
        $table = $this->table('towns');
        $table->insert($data)->save();
        echo "✅ $count villes importées\n";
    }
}

