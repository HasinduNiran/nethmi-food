<?php
require_once 'db_config.php';

// Get item ID and hotel type from GET request
$item_id = isset($_GET['item_id']) ? $conn->real_escape_string($_GET['item_id']) : '';
$hotel_type = isset($_GET['hotel_type']) ? intval($_GET['hotel_type']) : 0;

// Add debug output to a log file
// $debug_log = fopen('item_fetch_debug.log', 'a');
// fwrite($debug_log, "\n----- " . date('Y-m-d H:i:s') . " -----\n");
// fwrite($debug_log, "Request for item_id: $item_id, hotel_type: $hotel_type\n");

// First check if the item exists in the menu table
$menu_check_query = "SELECT 1 FROM menu WHERE item_id = '$item_id' LIMIT 1";
$menu_check_result = $conn->query($menu_check_query);

if ($menu_check_result && $menu_check_result->num_rows > 0) {
    //fwrite($debug_log, "Item found in menu table\n");
    
    // Item exists in menu table, fetch with portion prices
    $item_query = "SELECT 
        item_id,
        item_name,
        item_category,
        TRUE as has_portions,
        CASE 
            -- Use hotel_type 1 prices for any hotel_type not in the list
            WHEN $hotel_type NOT IN (1, 4, 6, 7, 11) THEN regular_price
            WHEN $hotel_type = 1 THEN regular_price
            WHEN $hotel_type IN (4, 6) THEN uber_pickme_regular
            WHEN $hotel_type = 7 THEN takeaway_regular
            WHEN $hotel_type = 11 THEN delivery_service_regular
            ELSE regular_price
        END AS regular_price,
        
        CASE 
            WHEN $hotel_type NOT IN (1, 4, 6, 7, 11) THEN medium_price
            WHEN $hotel_type = 1 THEN medium_price
            WHEN $hotel_type IN (4, 6) THEN uber_pickme_medium
            WHEN $hotel_type = 7 THEN takeaway_medium
            WHEN $hotel_type = 11 THEN delivery_service_medium
            ELSE medium_price
        END AS medium_price,
        
        CASE 
            WHEN $hotel_type NOT IN (1, 4, 6, 7, 11) THEN large_price
            WHEN $hotel_type = 1 THEN large_price
            WHEN $hotel_type IN (4, 6) THEN uber_pickme_large
            WHEN $hotel_type = 7 THEN takeaway_large
            WHEN $hotel_type = 11 THEN delivery_service_large
            ELSE large_price
        END AS large_price,
        
        CASE 
            WHEN $hotel_type NOT IN (1, 4, 6, 7, 11) THEN regular_price
            WHEN $hotel_type = 1 THEN regular_price
            WHEN $hotel_type IN (4, 6) THEN uber_pickme_regular
            WHEN $hotel_type = 7 THEN takeaway_regular
            WHEN $hotel_type = 11 THEN delivery_service_regular
            ELSE regular_price
        END AS display_price
    FROM 
        menu
    WHERE 
        item_id = '$item_id'
    LIMIT 1";
} else {
    //fwrite($debug_log, "Item not found in menu table, checking bakery_menu_stocks\n");
    
    // Item not found in menu, check bakery_menu_stocks
    $item_query = "SELECT 
        item_id,
        item_name,
        bakery_category as item_category,
        FALSE as has_portions,
        CASE 
            WHEN $hotel_type NOT IN (1, 4, 6, 7, 11) THEN dining_price
            WHEN $hotel_type IN (4, 6) THEN uber_pickme_price
            WHEN $hotel_type = 1 THEN dining_price
            WHEN $hotel_type = 7 THEN takeaway_price
            WHEN $hotel_type = 11 THEN delivery_service_item_price
            ELSE dining_price
        END AS display_price
    FROM 
        bakery_menu_stocks
    WHERE 
        item_id = '$item_id'
    LIMIT 1";
}

$item_result = $conn->query($item_query);

if ($item_result && $item_result->num_rows > 0) {
    $item = $item_result->fetch_assoc();
    
    //fwrite($debug_log, "Item found. has_portions=" . ($item['has_portions'] ? 'true' : 'false') . "\n");
    
    // Check if the prices are NULL or 0 for regular menu items
    if ($item['has_portions']) {
        // Count how many valid portion prices we have
        $valid_portions = 0;
        $has_regular = isset($item['regular_price']) && $item['regular_price'] > 0;
        $has_medium = isset($item['medium_price']) && $item['medium_price'] > 0;
        $has_large = isset($item['large_price']) && $item['large_price'] > 0;
        
        if ($has_regular) $valid_portions++;
        if ($has_medium) $valid_portions++;
        if ($has_large) $valid_portions++;
        
        //fwrite($debug_log, "Valid portions: $valid_portions (regular: " . ($has_regular ? 'yes' : 'no') . 
            // ", medium: " . ($has_medium ? 'yes' : 'no') . 
            // ", large: " . ($has_large ? 'yes' : 'no') . ")\n");
        
        // If only one portion has a price or no portions have a price, don't show portion selector
        if ($valid_portions <= 0) {
            $item['has_portions'] = false;
            //fwrite($debug_log, "Setting has_portions to false due to <= 1 valid portions\n");
        }
    }
    
    // Make sure has_portions is properly formatted as a boolean for JSON
    $item['has_portions'] = ($item['has_portions'] === true || $item['has_portions'] === 1 || $item['has_portions'] === '1') ? true : false;
    
    // fwrite($debug_log, "Final has_portions value: " . ($item['has_portions'] ? 'true' : 'false') . "\n");
    // fwrite($debug_log, "Response: " . json_encode($item) . "\n");
    
    echo json_encode($item);
} else {
    //fwrite($debug_log, "No item found for ID: $item_id\n");
    echo json_encode(null);
}

// fclose($debug_log);
$conn->close();
?>