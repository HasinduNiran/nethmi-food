<?php
require_once 'db_config.php';

// Get the next bill ID
$sql = "SELECT MAX(bill_id) + 1 AS next_bill_id FROM bills";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nextBillId = $row["next_bill_id"];
    if ($nextBillId === NULL) {
        $nextBillId = 1;
    }
} else {
    $nextBillId = 1;
}

echo json_encode(["next_bill_id" => $nextBillId]);

$conn->close();
?>