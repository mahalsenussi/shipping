<?php

include_once __DIR__ . "/../../config/database.php"; 

$mysqli = getDbConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_number = $_POST['reference_number'];
    $customer_id = $_POST['customer_id'];
    $shipping_line_id = !empty($_POST['shipping_line_id']) ? $_POST['shipping_line_id'] : null;
    $local_agent_id = !empty($_POST['local_agent_id']) ? $_POST['local_agent_id'] : null;
    $origin_port_id = !empty($_POST['origin_port_id']) ? $_POST['origin_port_id'] : null;
    $destination_port_id = !empty($_POST['destination_port_id']) ? $_POST['destination_port_id'] : null;
    $vessel_id = !empty($_POST['vessel_id']) ? $_POST['vessel_id'] : null;
    $voyage_number = !empty($_POST['voyage_number']) ? $_POST['voyage_number'] : null;
    $estimated_departure_date = !empty($_POST['estimated_departure_date']) ? $_POST['estimated_departure_date'] : null;
    $estimated_arrival_date = !empty($_POST['estimated_arrival_date']) ? $_POST['estimated_arrival_date'] : null;
    $created_by = $_SESSION['user_id'] ?? 1;

    $stmt = $mysqli->prepare("
        INSERT INTO shipments
            (reference_number, customer_id, shipping_line_id, local_agent_id, origin_port_id, destination_port_id, vessel_id, voyage_number, estimated_departure_date, estimated_arrival_date, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "siiiiiiissi",
        $reference_number,
        $customer_id,
        $shipping_line_id,
        $local_agent_id,
        $origin_port_id,
        $destination_port_id,
        $vessel_id,
        $voyage_number,
        $estimated_departure_date,
        $estimated_arrival_date,
        $created_by
    );

    if ($stmt->execute()) {
        header("Location: ?page=shipments&action=index&success=1");
        exit;
    } else {
        $_SESSION['error'] = "خطأ أثناء الحفظ: " . $stmt->error;
        header("Location: ?page=shipments&action=create");
        exit;
    }
}
