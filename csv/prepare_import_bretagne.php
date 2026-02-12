<?php

$inputFile = 'bretagne.csv';
$outputTownsFile = 'towns_import.csv';
$outputLocationsFile = 'locations_import.csv';

$handle = fopen($inputFile, 'r');
$first_line = fgets($handle);
rewind($handle);

if (strpos($first_line, "\t") !== false) {
    $delimiter = "\t";
} elseif (strpos($first_line, ";") !== false) {
    $delimiter = ";";
} else {
    $delimiter = ",";
}

$input = fopen($inputFile, 'r');
$header = fgetcsv($input, 0, $delimiter);

echo "Préparation des fichiers d'import...\n\n";

$towns = [];
$locations = [];

while (($row = fgetcsv($input, 0, $delimiter)) !== false) {
    $name = trim($row[0] ?? '');
    $address = trim($row[1] ?? '');
    $townName = trim($row[2] ?? '');
    $inseeCode = trim($row[3] ?? '');
    $postalCode = trim($row[4] ?? '');
    $locationType = trim($row[5] ?? 'Parking');  
    $longitude = trim($row[6] ?? '');
    $latitude = trim($row[7] ?? '');
    
    if (empty($name) || empty($townName) || empty($inseeCode)) {
        continue;
    }
    
    $townName = mb_convert_case(mb_strtolower($townName, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    $townKey = $inseeCode;
    
    // Unique towns
    if (!isset($towns[$townKey])) {
        $towns[$townKey] = [
            'name' => $townName,
            'postal_code' => $postalCode,
            'insee_code' => $inseeCode,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
    
    // Default type if empty
    if (empty($locationType)) {
        $locationType = 'Parking';
    }
    
    // Locations
    $locations[] = [
        'name' => $name,
        'address' => $address,
        'gps_lat' => $latitude,
        'gps_lng' => $longitude,
        'town_name' => $townName,
        'type' => $locationType,
    ];
    
    echo "✓ $name ($townName - $locationType)\n";
}

fclose($input);

// towns file
$outputTowns = fopen($outputTownsFile, 'w');
// Write UTF-8 BOM for Excel compatibility
fprintf($outputTowns, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($outputTowns, ['name', 'postal_code', 'insee_code', 'latitude', 'longitude']);

foreach ($towns as $town) {
    fputcsv($outputTowns, [
        $town['name'],
        $town['postal_code'],
        $town['insee_code'],
        $town['latitude'],
        $town['longitude'],
    ]);
}

fclose($outputTowns);

// locations file
$outputLocations = fopen($outputLocationsFile, 'w');
// Write UTF-8 BOM for Excel compatibility
fprintf($outputLocations, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($outputLocations, ['name', 'address', 'gps_lat', 'gps_lng', 'town_name', 'type']);

foreach ($locations as $location) {
    fputcsv($outputLocations, [
        $location['name'],
        $location['address'],
        $location['gps_lat'],
        $location['gps_lng'],
        $location['town_name'],
        $location['type'],
    ]);
}

fclose($outputLocations);

echo "\n=== Résumé ===\n";
echo "Villes : " . count($towns) . "\n";
echo "Lieux : " . count($locations) . "\n";
echo "\n✅ Fichiers générés :\n";
echo "   - $outputTownsFile\n";
echo "   - $outputLocationsFile\n";