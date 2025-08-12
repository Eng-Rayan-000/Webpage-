<?php
header('Content-Type: application/json');
require 'config.php';

// read JSON body
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

// flip status: 0→1 or 1→0
$stmt = $pdo->prepare('
    UPDATE people
      SET status = 1 - status
    WHERE id = :id
');
$stmt->execute([':id' => (int)$data['id']]);

// get the new status
$stmt2 = $pdo->prepare('SELECT status FROM people WHERE id = :id');
$stmt2->execute([':id' => (int)$data['id']]);
$new = $stmt2->fetchColumn();

echo json_encode([
    'success'    => true,
    'new_status' => (int)$new
]);
