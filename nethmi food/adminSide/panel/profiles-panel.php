<?php
session_start();
require_once '../posBackend/checkIfLoggedIn.php';
include '../inc/dashHeader.php';
require_once '../config.php';
require_once 'add_customer.php';
?>

<style>
    .suggestions {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .suggestion-item {
        padding: 8px 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f0f0f0;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 order-md-1" style="margin-top: 6rem; margin-left: 15rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="pull-left">Search Customer</h2>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#creditPaymentModal">
                    Pay Credit
                </button>
            </div>
            <form method="get" action="#">
                <div class="row">
                    <div class="col-md-6">
                        <input required type="text" id="search_query" style="width: 200px" name="search_query" class="form-control" placeholder="Enter Customer Name or Phone">
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#addCustomerModal">+</button>
                        <button type="submit" class="btn btn-dark">Search</button>
                    </div>
                </div>
            </form><br>

            <?php
            $searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';
            $searchQuery = mysqli_real_escape_string($link, $searchQuery);

            $customerQuery = "SELECT * FROM customers WHERE name LIKE '%$searchQuery%' OR phone_number LIKE '%$searchQuery%'";
            $customerResult = mysqli_query($link, $customerQuery);

            if ($customerResult && mysqli_num_rows($customerResult) > 0):
            ?>
                <h3>Customer Details</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Nic</th>
                            <th>Address</th>
                            <th>Visit Count</th>
                            <th>Last Visit</th>
                            <th>Relation</th>
                            <th>Credit Limit</th>
                            <th>Credit Balance</th>
                            <th>Advance Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($customerResult)) : ?>
                            <tr>
                                <td><?php echo $row['customer_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['phone_number']; ?></td>
                                <td><?php echo $row['nic']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td><?php echo $row['visit_count']; ?></td>
                                <td><?php echo $row['last_visit']; ?></td>
                                <td><?php echo $row['relation']; ?></td>
                                <td><?php echo $row['credit_limit']; ?></td>
                                <td><?php echo $row['credit_balance']; ?></td>
                                <td><?php echo $row['advance_payment']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#advancePaymentHistoryModal" data-customer-id="<?php echo $row['customer_id']; ?>">
                                        View History
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No customer found with the given details.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Advance Payment History -->
<div class="modal fade" id="advancePaymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="advancePaymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advancePaymentHistoryModalLabel">Advance Payment History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody id="paymentHistoryBody">
                        <!-- Payment history will be dynamically populated here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Credit Payment -->
<div class="modal fade" id="creditPaymentModal" tabindex="-1" role="dialog" aria-labelledby="creditPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditPaymentModalLabel">Pay Customer Credit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="creditPaymentForm">
                    <div class="form-group">
                        <label for="creditSearchPhone">Search Customer by Phone</label>
                        <input type="text" class="form-control" id="creditSearchPhone" placeholder="Enter phone number">
                        <div id="creditCustomerSuggestions" class="suggestions"></div>
                    </div>
                    <div class="form-group">
                        <label for="creditCustomerName">Customer Name</label>
                        <input type="text" class="form-control" id="creditCustomerName" readonly>
                    </div>
                    <div class="form-group">
                        <label for="creditCustomerId">Customer ID</label>
                        <input type="hidden" id="creditCustomerId">
                        <input type="text" class="form-control" id="creditCustomerIdDisplay" readonly>
                    </div>
                    <div class="form-group">
                        <label for="creditAmount">Amount</label>
                        <input type="number" class="form-control" id="creditAmount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="creditDescription">Description</label>
                        <textarea class="form-control" id="creditDescription" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/dashFooter.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Handle Advance Payment History Modal
        $('#advancePaymentHistoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var customerId = button.data('customer-id');

            $.ajax({
                url: 'fetch_payment_history.php',
                type: 'GET',
                data: {
                    customer_id: customerId
                },
                success: function(response) {
                    var paymentHistory = JSON.parse(response);
                    var paymentHistoryBody = $('#paymentHistoryBody');
                    paymentHistoryBody.empty();

                    paymentHistory.forEach(function(payment) {
                        paymentHistoryBody.append(`
                        <tr>
                            <td>${payment.payment_id}</td>
                            <td>${payment.payment_amount}</td>
                            <td>${payment.payment_date}</td>
                            <td>${payment.notes}</td>
                        </tr>
                    `);
                    });
                }
            });
        });

        // Handle Credit Payment Modal - Reset fields when opened
        $('#creditPaymentModal').on('show.bs.modal', function(event) {
            $('#creditCustomerId').val('');
            $('#creditCustomerIdDisplay').val('');
            $('#creditCustomerName').val('');
            $('#creditSearchPhone').val('');
            $('#creditAmount').val('');
            $('#creditDescription').val('');
            $('#creditCustomerSuggestions').empty().hide();
        });

        // Search customer by phone number
        $('#creditSearchPhone').on('input', function() {
            var query = $(this).val().trim();
            var suggestions = $('#creditCustomerSuggestions');

            if (query.length > 0) {
                $.ajax({
                    url: '../newPOS/search_customer.php',
                    type: 'GET',
                    data: {
                        query: query
                    },
                    success: function(response) {
                        var customers = JSON.parse(response);
                        suggestions.empty();

                        if (customers.length === 0) {
                            suggestions.append('<div class="suggestion-item">No matching customers</div>');
                        } else {
                            customers.forEach(function(customer) {
                                var div = $('<div>').text(`${customer.name} (${customer.phone_number})`).addClass('suggestion-item');
                                div.on('click', function() {
                                    $('#creditCustomerName').val(customer.name);
                                    $('#creditCustomerId').val(customer.customer_id);
                                    $('#creditCustomerIdDisplay').val(customer.customer_id);
                                    $('#creditSearchPhone').val(customer.phone_number);
                                    suggestions.empty().hide();
                                });
                                suggestions.append(div);
                            });
                        }
                        suggestions.show();
                    }
                });
            } else {
                suggestions.empty().hide();
            }
        });

        // Handle credit payment form submission
        $('#creditPaymentForm').on('submit', function(e) {
            e.preventDefault();
            var customerId = $('#creditCustomerId').val();
            var amount = $('#creditAmount').val();
            var description = $('#creditDescription').val();

            if (!customerId || !amount) {
                alert('Customer ID and Amount are required.');
                return;
            }

            $.ajax({
                url: 'save_credit_payment.php',
                type: 'POST',
                data: {
                    customer_id: customerId,
                    amount: amount,
                    description: description
                },
                success: function(response) {
                    console.log('Raw response:', response); // Debug raw response
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        // Replace alert with SweetAlert2
                        Swal.fire({
                            title: 'Success!',
                            text: 'Credit payment recorded successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            $('#creditPaymentModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        // Also improve error alerts
                        Swal.fire({
                            title: 'Error!',
                            text: result.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to save credit payment.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>