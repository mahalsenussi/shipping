<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id']);
    $agent_id = intval($_POST['agent_id']);
    $naval_line_id = $_POST['naval_line_id'] ? intval($_POST['naval_line_id']) : null;
    $price = floatval($_POST['price']);

    // Save selection (create a new table if needed)
    $conn->query("CREATE TABLE IF NOT EXISTS agent_selections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT,
        agent_id INT,
        naval_line_id INT,
        price DECIMAL(10,2),
        approved TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("INSERT INTO agent_selections (request_id, agent_id, naval_line_id, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $request_id, $agent_id, $naval_line_id, $price);
    $stmt->execute();
    $selection_id = $stmt->insert_id;
    $stmt->close();

    // Show waiting for approval page
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Waiting for Approval</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <div class="container mt-5">
        <h2>Waiting for Approval</h2>
        <p>Your selection has been saved. Please wait for approval.</p>
        <form method="post" action="approve_agent_selection.php">
            <input type="hidden" name="selection_id" value="' . $selection_id . '">
            <button type="submit" class="btn btn-success">Approve</button>
        </form>
    </div>
    </body>
    </html>';
    exit;
}
?>
