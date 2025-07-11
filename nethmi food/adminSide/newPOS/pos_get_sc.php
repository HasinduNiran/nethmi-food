<?php
include "../config.php";

if (!isset($_GET['mainCatId'])) {
    die(json_encode(["error" => "mainCatId is required"]));
}

$mainCatId = intval($_GET['mainCatId']);

$sql = "SELECT * FROM sub_menu_type WHERE parent_type_id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $mainCatId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

$stmt->close();
$link->close();
?>
