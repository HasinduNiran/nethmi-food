<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
include '../inc/dashHeader.php';
require_once '../config.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2" style="margin-top: 6rem;">
            <h2 class="text-center text-lg-start">Search Voided Orders</h2>
            <form method="get" action="#">
                <div class="row gy-2">
                    <div class="col-12 col-md-8">
                        <input 
                            required 
                            type="text" 
                            id="search_query" 
                            name="search_query" 
                            class="form-control" 
                            placeholder="Enter Bill ID, Item Name, or Reason">
                    </div>
                    <div class="col-12 col-md-4 text-md-start text-center">
                        <button type="submit" class="btn btn-dark w-100">Search</button>
                    </div>
                </div>
            </form><br>

            <?php
            // Fetch voided orders based on search query
            $searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';
            $searchQuery = mysqli_real_escape_string($link, $searchQuery);

            $voidOrderQuery = "SELECT d.*, s.staff_name AS deleted_by_name
                               FROM deleted_items d
                               LEFT JOIN staffs s ON d.deleted_by = s.staff_id
                               WHERE d.bill_id LIKE '%$searchQuery%' 
                                  OR d.item_name LIKE '%$searchQuery%' 
                                  OR d.reason LIKE '%$searchQuery%'
                               ORDER BY d.deleted_at DESC";

            $voidOrderResult = mysqli_query($link, $voidOrderQuery);

            if ($voidOrderResult && mysqli_num_rows($voidOrderResult) > 0):
            ?>
                <h3 class="mt-4">Voided Orders</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Bill ID</th>
                                <th>Table ID</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Deleted By</th>
                                <th>Deleted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($voidOrderResult)) : ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['bill_id']; ?></td>
                                    <td><?php echo $row['table_id']; ?></td>
                                    <td><?php echo $row['item_name']; ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['reason']; ?></td>
                                    <td><?php echo $row['deleted_by_name'] ? $row['deleted_by_name'] : 'Unknown'; ?></td>
                                    <td><?php echo $row['deleted_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="mt-3">No voided orders found with the given details.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../inc/dashFooter.php'; ?>
