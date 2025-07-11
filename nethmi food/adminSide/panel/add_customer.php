<?php
// addCustomerModal.php
require_once '../config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (!isset($_POST['name']) || !isset($_POST['phone_number'])) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
        exit;
    }

    // Escape and retrieve form data with default values for optional fields
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $address = mysqli_real_escape_string($link, $_POST['address'] ?? '');
    $phone_number = mysqli_real_escape_string($link, $_POST['phone_number']);
    $nic = mysqli_real_escape_string($link, $_POST['nic'] ?? '');
    $relation = mysqli_real_escape_string($link, $_POST['relation'] ?? '');
    $credit_limit = mysqli_real_escape_string($link, $_POST['credit_limit'] ?? '0');
    $advance_payment = mysqli_real_escape_string($link, $_POST['advance_payment'] ?? '0');

    // Check if phone number already exists
    $checkQuery = "SELECT phone_number FROM customers WHERE phone_number = '$phone_number'";
    $checkResult = mysqli_query($link, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Phone number already exists']);
        exit;
    }

    // Get current Sri Lanka date and time
    $current_datetime = date('Y-m-d H:i:s');

    // Insert query with Sri Lanka timestamp
    $insertQuery = "INSERT INTO customers (name, address, phone_number, nic, relation, credit_limit, advance_payment, visit_count, last_visit) 
                    VALUES ('$name', '$address', '$phone_number', '$nic', '$relation', '$credit_limit', '$advance_payment', 0, '$current_datetime')";

    if (mysqli_query($link, $insertQuery)) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Customer added successfully!',
            'timestamp' => $current_datetime
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add customer: ' . mysqli_error($link)]);
    }
    exit;
}
?>

<!-- Modal HTML -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="nic">NIC</label>
                        <input type="text" class="form-control" id="nic" name="nic">
                    </div>
                    <div class="form-group">
                        <label for="relation">Relation</label>
                        <input type="text" class="form-control" id="relation" name="relation">
                    </div>
                    <div class="form-group">
                        <label for="credit_limit">Credit Limit</label>
                        <input type="number" class="form-control" id="credit_limit" name="credit_limit">
                    </div>
                    <!-- <div class="form-group">
                        <label for="advance_payment">Advance Payment</label>
                        <input type="number" class="form-control" id="advance_payment" name="advance_payment">
                    </div> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-dark" id="saveCustomer">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveCustomer').on('click', function() {
        // Basic client-side validation
        var name = $('#name').val();
        var phone = $('#phone_number').val();
        
        if (!name || !phone) {
            alert('Name and Phone Number are required fields');
            return;
        }

        var formData = $('#addCustomerForm').serialize();

        $.ajax({
            url: 'add_customer.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var result = JSON.parse(response);
                alert(result.message);
                if (result.status === 'success') {
                    $('#addCustomerModal').modal('hide');
                    location.reload();
                }
            },
            error: function() {
                alert('An error occurred while saving the customer');
            }
        });
    });
});
</script>