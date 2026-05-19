<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
include 'db_config.php';

$billNumber = $_GET['bill_number'] ?? '';

$sql = "SELECT * FROM bill_of_lading WHERE bill_number = '$billNumber'";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("<h2>No bill of lading found for this number.</h2>");
}

$bill = $result->fetch_assoc();
$shipmentId = $bill['shipment_id'];

$shipment = $conn->query("SELECT * FROM shipments WHERE id = '$shipmentId'")->fetch_assoc();
$terms = ($conn->query("SELECT terms FROM terms_conditions ORDER BY id DESC LIMIT 1")->fetch_assoc())['terms'] ?? '';
$freight = $conn->query("SELECT * FROM freight_charges WHERE bill_number = '$billNumber'")->fetch_assoc();
$details = $conn->query("SELECT * FROM shipment_details WHERE shipment_id = '$shipmentId'")->fetch_assoc();
$company = $conn->query("SELECT * FROM company WHERE id = '{$shipment['company_id']}'");
if (!$company || $company->num_rows === 0) {
    $company = ['company_name' => 'N/A', 'company_address' => 'N/A', 'company_phone' => 'N/A'];
} else {
    $company = $company->fetch_assoc();
}

$broker = $conn->query("SELECT * FROM broker WHERE id = '{$shipment['broker_id']}'");
if (!$broker || $broker->num_rows === 0) {
    $broker = ['broker_name' => 'N/A', 'broker_phone' => 'N/A'];
} else {
    $broker = $broker->fetch_assoc();
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetMargins(10, 10, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 9);

// Draw main boxes and layout
$pdf->SetXY(150, 10);
$pdf->Cell(45, 20, $pdf->Image(__DIR__.'/img/logo.jpg', $pdf->GetX() + 2, $pdf->GetY() + 2, 41), 1, 0, 'C');
$pdf->SetXY(10, 10);
$pdf->Cell(100, 20, "Shipper", 1);
$pdf->SetXY(110, 10);
$pdf->Cell(90, 10, "BILL OF LADING", 1, 2, 'L');
$pdf->Cell(90, 10, "B/L No: {$bill['bill_number']}", 1, 2, 'L');

// Adjust table position to start under the logo
$pdf->SetXY(10, 40);
$pdf->MultiCell(190, 20, "Consignee (if 'order' state notify party):\n{$company['company_name']}\n{$company['company_address']}\n{$company['company_phone']}", 1);

$pdf->SetX(10);
$notifyContact = isset($shipment['contact_person']) ? $shipment['contact_person'] : 'N/A';
$pdf->MultiCell(190, 20, "Notify Party:\n{$notifyContact}\n{$broker['broker_name']}\n{$broker['broker_phone']}", 1);

$pdf->Cell(95, 10, 'Notify Party', 1);
$pdf->Cell(95, 10, 'Port of loading', 1, 1);
$pdf->Cell(95, 10, isset($shipment['contact_person']) ? $shipment['contact_person'] : '', 1);
$pdf->Cell(95, 10, isset($shipment['shipping_port']) ? $shipment['shipping_port'] : '', 1, 1);

$pdf->Cell(95, 10, 'Place of delivery', 1);
$pdf->Cell(47.5, 10, 'Freight Payable at', 1);
$pdf->Cell(47.5, 10, 'Number of Original B/L required', 1, 1);
$pdf->Cell(95, 10, isset($shipment['arrival_port']) ? $shipment['arrival_port'] : '', 1);
$pdf->Cell(47.5, 10, isset($freight['place_of_issue']) ? $freight['place_of_issue'] : "[x] PREPAID   [ ] COLLECT", 1);
$pdf->Cell(47.5, 10, isset($freight['number_sequence']) ? $freight['number_sequence'] : '3', 1, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 8, 'BARTICULARS FURNISHED BY SHIPPER', 1, 1, 'C');
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(30, 10, 'Marks& Number', 1);
$pdf->Cell(30, 10, 'No. of cntr/ pkg', 1);
$pdf->Cell(60, 10, 'Description of Goods', 1);
$pdf->Cell(35, 10, 'Gross Weight', 1);
$pdf->Cell(35, 10, 'Measurement', 1, 1);

$pdf->Cell(30, 20, isset($details['marks_numbers']) ? $details['marks_numbers'] : '', 1);
$pdf->Cell(30, 20, isset($details['quantity']) ? $details['quantity'] : '', 1);
$pdf->MultiCell(60, 20, isset($details['description']) ? $details['description'] : 'Containers:', 1, 'L', false, 0);
$pdf->Cell(35, 20, isset($details['weight']) ? $details['weight'] : 'Kgs', 1, 0);
$pdf->Cell(35, 20, isset($details['container_seal_number']) ? $details['container_seal_number'] : 'CBM', 1, 1);

$pdf->Cell(190, 10, '[ ] CY   [ ] CFS   [x] CF/S   [ ] OTHER', 1, 1);

$pdf->SetFont('dejavusans', '', 8);
$pdf->MultiCell(120, 15, "SHIPPER'S DECLARATION\nWe warrant that the details of cargo declared above are correct to our knowledge.\nThe goods detailed herein were, at the time of", 1);
$pdf->MultiCell(70, 15, "For World Sea's MARITIME TRANSPORT\n\n--------------------------------------------\nAs Carrier / Agent", 1, 'C');

$issueDate = isset($bill['issue_date']) ? $bill['issue_date'] : 'N/A'; // Fetch issue_date from the bill_of_lading table
$pdf->Cell(120, 10, "PLACE AND DATE OF ISSUE:\nDATE: {$issueDate}", 1);
$pdf->Cell(70, 10, "NO BOARD", 1, 1, 'C');

$pdf->Cell(190, 10, 'FROM-OE-002V .1.1', 0, 1, 'R');

// Ensure no prior output before sending the PDF
if (headers_sent()) {
    die("Error: Headers already sent. Cannot generate PDF.");
}

$pdf->Output('bill_of_lading.pdf', 'D');
?>