<?php
// KOT Printer IP and Port
$printer_ip = "192.168.123.100"; // Your Xprinter IP
$printer_port = 9100; // RAW TCP/IP Port

// Prepare KOT content
$kot_content = "===============================\n";
$kot_content .= "     Kitchen Order Ticket      \n";
$kot_content .= "===============================\n";
$kot_content .= "Bill ID: $bill_id\n";
$kot_content .= "Order Type: $hotel_type\n";
$kot_content .= "Table No: $table_number\n";
if ($isUberOrPickMe && !empty($reference_number)) {
    $kot_content .= "$serviceName Ref No: $reference_number\n";
}
$kot_content .= "-------------------------------\n";

foreach ($cart as $item) {
    $name = $item['name'];
    $qty = intval($item['quantity']) + intval($item['fc'] ?? 0);
    if ($qty <= 0) continue; // Skip zero quantity
    $kot_content .= sprintf("%-20s x%2d\n", $name, $qty);
}
$kot_content .= "-------------------------------\n";
$kot_content .= "Print Time: " . date("Y-m-d H:i:s") . "\n";
$kot_content .= "===============================\n\n\n";

// Add ESC/POS command for paper cut (if supported by Xprinter)
$kot_content .= chr(29) . chr(86) . chr(1); // Full cut

// Send to printer
$fp = fsockopen($printer_ip, $printer_port, $errno, $errstr, 10);
if (!$fp) {
    echo "Error: Could not connect to printer - $errstr ($errno)";
    exit;
}
fwrite($fp, $kot_content);
fclose($fp);

echo "✅ KOT sent successfully to printer.";
?>
