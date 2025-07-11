<?php
// addSupplier.php

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = mysqli_real_escape_string($link, $_POST['supplier_name']);
    $telephone = mysqli_real_escape_string($link, $_POST['telephone']);
    $company = mysqli_real_escape_string($link, $_POST['company']);
    $credit_balance = mysqli_real_escape_string($link, $_POST['credit_balance']);

    $insertQuery = "INSERT INTO suppliers (supplier_name, telephone, company, credit_balance) 
                    VALUES ('$supplier_name', '$telephone', '$company', '$credit_balance')";

    if (mysqli_query($link, $insertQuery)) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add supplier.']);
    }
    exit;
}
?>

<!-- Modal HTML -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm">
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telephone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required>
                    </div>
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" id="company" name="company" required>
                    </div>
                    <div class="form-group">
                        <label for="credit_balance">Credit Balance</label>
                        <input type="number" step="0.01" class="form-control" id="credit_balance" name="credit_balance" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-dark" id="saveSupplier">Save Supplier</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveSupplier').on('click', function() {
        var formData = $('#addSupplierForm').serialize();

        $.ajax({
            url: 'addSupplier.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    alert(result.message);
                    $('#addSupplierModal').modal('hide');
                    location.reload(); // Reload the page to show the new supplier
                } else {
                    alert(result.message);
                }
            }
        });
    });
});
</script>