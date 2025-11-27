<?php
$db = new PDO('mysql:host=localhost;dbname=vet_system', 'root', '');

echo "=== TESTING DATA ===\n";
echo "\nServices with specialization:\n";
$stmt = $db->query('SELECT id, service_name FROM services');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "ID: " . $row['id'] . " - Name: " . $row['service_name'] . "\n";
}

echo "\nVeterinarians with specialization:\n";
$stmt = $db->query('SELECT id, name, specialization FROM veterinarians WHERE is_active = 1');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Spec: " . $row['specialization'] . "\n";
}
