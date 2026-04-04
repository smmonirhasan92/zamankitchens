<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (is_array($data)) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE categories SET order_index = ? WHERE id = ?");
        foreach ($data as $item) {
            $stmt->execute([(int)$item['order_index'], (int)$item['id']]);
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
