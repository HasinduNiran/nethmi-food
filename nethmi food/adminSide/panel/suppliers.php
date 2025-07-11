<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
include '../inc/dashHeader.php'; 
require_once '../config.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 order-md-1" style="margin-top: 6rem; margin-left: 15rem;">
            <h2 class="pull-left">Suppliers</h2>
            
            <!-- Add Supplier Button -->
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#addSupplierModal" style="margin-bottom: 1rem;">+ Add Supplier</button>
            
            <!-- Search Form -->
            <form method="get" action="#" style="margin-bottom: 1rem;">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search by Name, Telephone, or Company" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-dark">Search</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='suppliers.php'">Clear</button>
                    </div>
                </div>
            </form>

            <?php
            // Fetch suppliers based on search query
            $searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';
            $searchQuery = mysqli_real_escape_string($link, $searchQuery);

            $supplierQuery = "SELECT * FROM suppliers 
                             WHERE supplier_name LIKE '%$searchQuery%' 
                             OR telephone LIKE '%$searchQuery%' 
                             OR company LIKE '%$searchQuery%'";
            $supplierResult = mysqli_query($link, $supplierQuery);

            if ($supplierResult && mysqli_num_rows($supplierResult) > 0):
            ?>
                <h3>Supplier Details</h3>
                <!-- Scrollable Table Container -->
                <div style="overflow-x: auto; max-height: 400px;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Supplier ID</th>
                                <th>Supplier Name</th>
                                <th>Telephone</th>
                                <th>Company</th>
                                <th>Created At</th>
                                <th>Credit Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($supplierResult)) : ?>
                                <tr>
                                    <td><?php echo $row['supplier_id']; ?></td>
                                    <td><?php echo $row['supplier_name']; ?></td>
                                    <td><?php echo $row['telephone']; ?></td>
                                    <td><?php echo $row['company']; ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                    <td><?php echo $row['credit_balance']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No suppliers found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include Add Supplier Modal -->
<?php include 'addSupplier.php'; ?>

<?php include '../inc/dashFooter.php'; ?>