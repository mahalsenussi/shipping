<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selection_id'])) {
    $selection_id = intval($_POST['selection_id']);
    $conn->query("UPDATE agent_selections SET approved=1 WHERE id=$selection_id");

    // Get request id for manifest input
    $sel = $conn->query("SELECT request_id FROM agent_selections WHERE id=$selection_id")->fetch_assoc();
    $request_id = $sel ? $sel['request_id'] : 0;

    // Set request status to approved
    if ($request_id) {
        $conn->query("UPDATE requests SET status='approved' WHERE id=$request_id");
    }

    header("Location: manifest_input.php?request_id=$request_id");
    exit;
}
?>
