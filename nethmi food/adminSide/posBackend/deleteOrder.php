<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['roll']) || !in_array($_SESSION['roll'], [2, 3, 5])) {
    echo '<script>
            alert("Access Denied: You do not have permission to delete orders.");
            window.location.href = "holdOrder.php";
          </script>';
    exit;
}

if (isset($_GET['bill_id'])) {
    $bill_id = intval($_GET['bill_id']);

    // Delete the order from `held_payments` and `bills` tables
    $deleteHeldPayments = "DELETE FROM held_payments WHERE bill_id = ?";
    $deleteBill = "DELETE FROM bills WHERE bill_id = ?";

    $stmt1 = $link->prepare($deleteHeldPayments);
    $stmt2 = $link->prepare($deleteBill);

    if ($stmt1 && $stmt2) {
        $stmt1->bind_param("i", $bill_id);
        $stmt2->bind_param("i", $bill_id);

        $stmt1->execute();
        $stmt2->execute();

        $stmt1->close();
        $stmt2->close();

        echo '<script>
                alert("Order deleted successfully.");
                window.location.href = "holdOrder.php";
              </script>';
    } else {
        echo '<script>
                alert("Error deleting the order. Please try again.");
                window.location.href = "../adminSide/posBackend/holdOrder.php";
              </script>';
    }
} else {
    echo '<script>
            alert("Invalid request.");
            window.location.href = "../adminSide/posBackend/holdOrder.php";
          </script>';
}
?>
