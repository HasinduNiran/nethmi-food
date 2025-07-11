<?php
// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'customer' => null
];

try {

    require_once 'db_config.php';

    $errors = [];
    
    if (empty($_POST['customer_name'])) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($_POST['customer_phone'])) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $_POST['customer_phone'])) {
        $errors['phone'] = 'Please enter a valid phone number';
    } else {
        // Check if phone number already exists
        $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE phone_number = ?");
        $stmt->bind_param("s", $_POST['customer_phone']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors['phone'] = 'A customer with this phone number already exists';
        }
    }
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
        echo json_encode($response);
        exit;
    }
    
    // Process the input
    $name = $_POST['customer_name'];
    $phone = $_POST['customer_phone'];
    $address = isset($_POST['customer_address']) ? $_POST['customer_address'] : null;
    
    // Get current Sri Lanka date and time
    $currentDate = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO customers (name, phone_number, address, last_visit) 
                          VALUES (?, ?, ?, ?)");
    
    $stmt->bind_param("ssss", $name, $phone, $address, $currentDate);
    
    if ($stmt->execute()) {
        $customerId = $conn->insert_id;
        
        // Return success response with Sri Lanka timestamp
        $response['success'] = true;
        $response['message'] = 'Customer added successfully!';
        $response['customer'] = [
            'customer_id' => $customerId,
            'name' => $name,
            'phone_number' => $phone,
            'address' => $address,
            'last_visit' => $currentDate  // Include the Sri Lanka timestamp in response
        ];
    } else {
        throw new Exception("Error executing query: " . $conn->error);
    }
    
} catch(Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
} finally {

    if (isset($stmt)) {
        $stmt->close();
    }

    echo json_encode($response);
    
    if (isset($conn)) {
        $conn->close();
    }
}
?>