<?php
require_once "../config.php";

$conn = $link;
$term = $_GET['term'];

$query = "SELECT supplier_id, supplier_name FROM suppliers WHERE supplier_name LIKE ? ORDER BY supplier_name";
$stmt = $conn->prepare($query);
$search_term = "%" . $term . "%";
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

$suppliers = array();
while ($row = $result->fetch_assoc()) {
    $suppliers[] = array(
        "id" => $row["supplier_id"],
        "value" => $row["supplier_name"],
        "label" => $row["supplier_name"]
    );
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($suppliers);
?>