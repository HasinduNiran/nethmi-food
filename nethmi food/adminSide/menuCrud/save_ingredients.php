<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (is_array($data)) {
        $conn = $link;

        foreach ($data as $item) {
            $menu_item_id = $item['menu_item_id'];
            $ingredient_id = $item['ingredient_id'];
            $quantity = $item['quantity'];
            $measurement = $item['measurement'];

            $insert_query = "INSERT INTO menu_ingredients (menu_item_id, ingredient_id, quantity, measurement) 
                            VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sids", $menu_item_id, $ingredient_id, $quantity, $measurement);

            if (!$stmt->execute()) {
                http_response_code(500);
                echo "Error: " . $stmt->error;
                $stmt->close();
                $conn->close();
                exit();
            }

            $stmt->close();
        }

        echo "Ingredients saved successfully";
        $conn->close();
    } else {
        http_response_code(400);
        echo "Invalid data format";
    }
} else {
    http_response_code(405);
    echo "Method not allowed";
}
?>