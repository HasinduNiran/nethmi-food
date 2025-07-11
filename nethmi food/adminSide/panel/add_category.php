<?php
require_once "../config.php";

$data = json_decode(file_get_contents('php://input'), true);
$category = $data['category'];

$sql = "INSERT INTO inventory (category) VALUES (?)";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param('s', $category);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $link->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => $link->error]);
}
?>