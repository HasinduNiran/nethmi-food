<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
include '../inc/dashHeader.php'; 
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $total_service_charge = $_POST['total_service_charge'];
    $company_percentage = $_POST['company_percentage'];
    $company_share = $total_service_charge * ($company_percentage / 100);
    $balance_service_charge = $total_service_charge - $company_share;

    // Save company share and balance into a database (if needed)

    // Save employee deductions
    $employees = $_POST['employees'];
    foreach ($employees as $employee) {
        $employee_name = $employee['name'];
        $deduction_percentage = $employee['deduction_percentage'];
        $deduction_amount = $balance_service_charge * ($deduction_percentage / 100);

        // Save employee-specific data into a database
        $stmt = $conn->prepare("INSERT INTO employee_service_charges (date, employee_name, deduction_percentage, deduction_amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $date, $employee_name, $deduction_percentage, $deduction_amount);
        $stmt->execute();
    }
}

// Fetch and display data from the database
$result = $conn->query("SELECT * FROM employee_service_charges ORDER BY date DESC");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h3>Service Charge Management</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="date">Select Date</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="total_service_charge">Total Service Charge</label>
                    <input type="number" id="total_service_charge" name="total_service_charge" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="company_percentage">Company Service Charge Percentage</label>
                    <input type="number" id="company_percentage" name="company_percentage" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Employee Deductions</label>
                    <div id="employee-deductions">
                        <div class="employee-row">
                            <input type="text" name="employees[0][name]" placeholder="Employee Name" class="form-control mb-2" required>
                            <input type="number" name="employees[0][deduction_percentage]" placeholder="Deduction Percentage" class="form-control mb-2" required>
                        </div>
                    </div>
                    <button type="button" id="add-employee" class="btn btn-secondary">Add Employee</button>
                </div>
                <button type="submit" class="btn btn-primary">Calculate and Save</button>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h3>Employee Service Charge Distribution</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee Name</th>
                        <th>Deduction Percentage</th>
                        <th>Deduction Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['employee_name']) ?></td>
                            <td><?= htmlspecialchars($row['deduction_percentage']) ?>%</td>
                            <td><?= htmlspecialchars($row['deduction_amount']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Add dynamic employee rows
    let employeeIndex = 1;
    document.getElementById('add-employee').addEventListener('click', function() {
        const employeeRow = document.createElement('div');
        employeeRow.classList.add('employee-row');
        employeeRow.innerHTML = `
            <input type="text" name="employees[${employeeIndex}][name]" placeholder="Employee Name" class="form-control mb-2" required>
            <input type="number" name="employees[${employeeIndex}][deduction_percentage]" placeholder="Deduction Percentage" class="form-control mb-2" required>
        `;
        document.getElementById('employee-deductions').appendChild(employeeRow);
        employeeIndex++;
    });
</script>

<?php include '../inc/dashFooter.php'; ?>
