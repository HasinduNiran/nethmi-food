<?php
require('../../adminSide/posBackend/fpdf186/fpdf.php');
require_once '../../adminSide/config.php';

$reservation_id = $_GET['reservation_id'] ?? 1;

// Function to fetch reservation information by reservation ID
function getReservationInfoById($link, $reservation_id) {
    $query = "SELECT * FROM reservations_tb WHERE id='$reservation_id'";
    $result = mysqli_query($link, $query);

    if ($result) {
        $reservationInfo = mysqli_fetch_assoc($result);
        return $reservationInfo;
    } else {
        return null;
    }
}

// Fetch reservation information based on the reservation ID
$reservationInfo = getReservationInfoById($link, $reservation_id);

if ($reservationInfo) {
    // Create a PDF using FPDF
    class PDF extends FPDF {
        function Header() {
            // Set font and size for the header
            $this->SetFont('Arial', 'B', 20);
            $logoText = "Mom's touch DINING & BAR";
            $this->SetTextColor(0, 0, 0); // Set text color to black
            $this->Cell(0, 10, $logoText, 0, 1, 'C');
            $this->Ln(10);

            // Set font and size for "Reservation Information" title
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'Reservation Information', 1, 1, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Create a table for reservation information
    $pdf->Cell(40, 10, 'Reservation ID:', 1);
    $pdf->Cell(150, 10, $reservationInfo['id'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Customer Name:', 1);
    $pdf->Cell(150, 10, $reservationInfo['name'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Phone:', 1);
    $pdf->Cell(150, 10, $reservationInfo['phone'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Number of Persons:', 1);
    $pdf->Cell(150, 10, $reservationInfo['num_persons'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Reservation Time:', 1);
    $pdf->Cell(150, 10, $reservationInfo['reservation_time'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Reservation Date:', 1);
    $pdf->Cell(150, 10, $reservationInfo['reservation_date'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Special Request:', 1);
    $pdf->Cell(150, 10, $reservationInfo['message'], 1);
    $pdf->Ln();

    $pdf->Cell(40, 10, 'Created At:', 1);
    $pdf->Cell(150, 10, $reservationInfo['created_at'], 1);
    $pdf->Ln();

    $pdf->Output('Reservation-Copy-ID' . $reservationInfo['id'] . '.pdf', 'D');
} else {
    echo 'Invalid reservation ID or reservation not found.';
}
?>
