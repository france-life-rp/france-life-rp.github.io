<?php
header('Content-Type: application/json');

$host = 'database.game-01.dragonhosting.fr:3306'; // Remplace par ton hôte de base de données
$db = 's1_boutique'; // Remplace par le nom de ta base de données
$user = 'u1_m3vxpsDMhN'; // Remplace par ton nom d'utilisateur de base de données
$pass = 'N9spo@^3KVJ7Hp^ld2ZuID6M'; // Remplace par ton mot de passe de base de données

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['payerID']) && isset($data['duration']) && isset($data['transactionID'])) {
    $payerID = $mysqli->real_escape_string($data['payerID']);
    $duration = intval($data['duration']);
    $transactionID = $mysqli->real_escape_string($data['transactionID']);
    
    // Ajouter l'utilisateur à la base de données avec le niveau VIP et la durée
    $stmt = $mysqli->prepare("INSERT INTO vip_users (payer_id, duration, transaction_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE duration = duration + ?");
    $stmt->bind_param("sisi", $payerID, $duration, $transactionID, $duration);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data received.']);
}

$mysqli->close();
?>
