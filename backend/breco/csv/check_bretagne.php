<?php

$inputFile = 'bretagne.csv';
$outputFile = 'parkings_with_postal.csv';

// Ouvrir et détecter le délimiteur
$handle = fopen($inputFile, 'r');
$first_line = fgets($handle);
rewind($handle);

// Détecter le délimiteur
if (strpos($first_line, "\t") !== false) {
    $delimiter = "\t";
    echo "Délimiteur détecté : TAB\n";
} elseif (strpos($first_line, ";") !== false) {
    $delimiter = ";";
    echo "Délimiteur détecté : ; (point-virgule)\n";
} else {
    $delimiter = ",";
    echo "Délimiteur détecté : , (virgule)\n";
}

// Relire avec le bon délimiteur
$input = fopen($inputFile, 'r');
$output = fopen($outputFile, 'w');

// Lire l'en-tête
$header = fgetcsv($input, 0, $delimiter);

echo "Colonnes : " . implode(' | ', $header) . "\n";
echo "Nombre de colonnes : " . count($header) . "\n\n";

// Ajouter la colonne postal_code
$newHeader = [];
foreach ($header as $col) {
    $newHeader[] = $col;
    if ($col === 'insee_code' || $col === 'code_commune') {
        $newHeader[] = 'postal_code';
    }
}
fputcsv($output, $newHeader, $delimiter);

echo "Extraction des codes postaux...\n\n";

$lineNumber = 1;
$extracted = 0;
$fromApi = 0;
$errors = 0;
$skipped = 0;

while (($row = fgetcsv($input, 0, $delimiter)) !== false) {
    $lineNumber++;
    
    // Debug : afficher la première ligne pour vérifier
    if ($lineNumber == 2) {
        echo "DEBUG - Première ligne de données :\n";
        echo "  Nombre de colonnes : " . count($row) . "\n";
        for ($i = 0; $i < count($row); $i++) {
            echo "  [$i] = '{$row[$i]}'\n";
        }
        echo "\n";
    }
    
    // Vérifier que la ligne a assez de colonnes
    if (count($row) < 7) {
        echo "⚠️  Ligne $lineNumber : Ligne incomplète (" . count($row) . " colonnes) - IGNORÉE\n";
        $skipped++;
        continue;
    }
    
    $name = trim($row[0] ?? '');
    $address = trim($row[1] ?? '');
    $town = trim($row[2] ?? '');
    $codeCommune = trim($row[3] ?? '');
    $type = trim($row[4] ?? '');
    $longitude = trim($row[5] ?? '');
    $latitude = trim($row[6] ?? '');
    
    // Vérifier que les champs essentiels ne sont pas vides
    if (empty($name) || empty($town) || empty($codeCommune)) {
        echo "⚠️  Ligne $lineNumber : Données manquantes - IGNORÉE\n";
        $skipped++;
        continue;
    }
    
    $postalCode = '';
    
    // Méthode 1 : Extraire depuis l'adresse
    if ($address !== '' && preg_match('/\b(\d{5})\b/', $address, $matches)) {
        $postalCode = $matches[1];
        echo "✓ Ligne $lineNumber : $town - CP extrait: $postalCode\n";
        $extracted++;
    }
    // Méthode 2 : Utiliser l'API geo.gouv.fr avec les coordonnées GPS
    elseif ($latitude !== '' && $longitude !== '') {
        echo "🔍 Ligne $lineNumber : $town - Interrogation API geo.gouv.fr... ";
        
        $url = "https://geo.api.gouv.fr/communes?lat=$latitude&lon=$longitude&fields=codesPostaux&format=json";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Breco/1.0'
            ]
        ]);
        
        $json = @file_get_contents($url, false, $context);
        
        if ($json !== false) {
            $data = json_decode($json, true);
            
            if (!empty($data) && isset($data[0]['codesPostaux']) && !empty($data[0]['codesPostaux'])) {
                $postalCode = $data[0]['codesPostaux'][0]; // Prendre le premier code postal
                echo "✓ Trouvé: $postalCode\n";
                $fromApi++;
            } else {
                echo "❌ Aucun code postal trouvé\n";
                $errors++;
            }
        } else {
            echo "❌ Erreur API\n";
            $errors++;
        }
        
        // Pause pour ne pas surcharger l'API
        usleep(200000); // 0.2 seconde
    }
    // Méthode 3 : Utiliser l'API avec le code INSEE
    elseif ($codeCommune !== '') {
        echo "🔍 Ligne $lineNumber : $town - API avec code INSEE... ";
        
        $url = "https://geo.api.gouv.fr/communes/$codeCommune?fields=codesPostaux&format=json";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Breco/1.0'
            ]
        ]);
        
        $json = @file_get_contents($url, false, $context);
        
        if ($json !== false) {
            $data = json_decode($json, true);
            
            if (!empty($data) && isset($data['codesPostaux']) && !empty($data['codesPostaux'])) {
                $postalCode = $data['codesPostaux'][0];
                echo "✓ Trouvé: $postalCode\n";
                $fromApi++;
            } else {
                echo "❌ Aucun code postal\n";
                $errors++;
            }
        } else {
            echo "❌ Erreur API\n";
            $errors++;
        }
        
        usleep(200000);
    }
    else {
        echo "⚠️  Ligne $lineNumber : $town - Aucune source disponible\n";
        $errors++;
    }
    
    // Construire la nouvelle ligne
    $newRow = [
        $name,
        $address,
        $town,
        $codeCommune,
        $postalCode,
        $type,
        $longitude,
        $latitude
    ];
    
    fputcsv($output, $newRow, $delimiter);
}

fclose($input);
fclose($output);

echo "\n" . str_repeat("=", 50) . "\n";
echo "RÉSUMÉ\n";
echo str_repeat("=", 50) . "\n";
echo "Total lignes traitées : " . ($lineNumber - 1) . "\n";
echo "✓ Extraits depuis adresse : $extracted\n";
echo "✓ Récupérés via API : $fromApi\n";
echo "⚠️  Codes postaux manquants : $errors\n";
echo "⚠️  Lignes ignorées : $skipped\n";
echo "\n✅ Fichier généré : $outputFile\n";
echo "\nNOTE : Les requêtes API sont limitées à ~200ms entre chaque appel\n";
echo "pour respecter les limites de l'API gouvernementale.\n";
