<?php
// Simulate what the Userpanel appointments controller does
$db = new PDO('mysql:host=localhost;dbname=vet_system', 'root', '');

echo "=== USERPANEL VETS QUERY ===\n\n";

// This is what the controller does
$stmt = $db->prepare("SELECT * FROM veterinarians WHERE is_active = 1");
$stmt->execute();
$vets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total active vets: " . count($vets) . "\n\n";

foreach ($vets as $vet) {
    echo "ID: " . $vet['id'] . " - " . $vet['name'] . " (" . $vet['specialization'] . ")\n";
}

echo "\n=== SERVICES ===\n\n";
$stmt = $db->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($services as $service) {
    echo "ID: " . $service['id'] . " - " . $service['service_name'] . "\n";
}

echo "\n=== GROOMING MATCH TEST ===\n";
$groomingService = null;
foreach ($services as $s) {
    if ($s['id'] == 10) {
        $groomingService = $s;
        break;
    }
}

$groomingVets = [];
foreach ($vets as $v) {
    if ($v['specialization'] === $groomingService['service_name']) {
        $groomingVets[] = $v;
    }
}

echo "Service: " . $groomingService['service_name'] . "\n";
echo "Matching vets: " . count($groomingVets) . "\n";
foreach ($groomingVets as $v) {
    echo "  - " . $v['name'] . "\n";
}
