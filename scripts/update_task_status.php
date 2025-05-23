<?php
// Include database configuration
require_once "../database/database.php";

session_start();

// Helper function to detect browser access
function isBrowserAccess() {
    return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'html') !== false;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $response = ["success" => false, "message" => "Unauthorized"];
    
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    exit();
}

try {
    // Get raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Validate input
    if (!isset($data['taskId']) || !isset($data['status'])) {
        $response = ["success" => false, "message" => "Invalid input"];

        if (isBrowserAccess()) {
            header('Content-Type: text/html');
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        exit();
    }

    $taskId = $data['taskId'];
    $status = $data['status'];

    // Update task status
    $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $taskId,
        'status' => $status,
        'user_id' => $_SESSION['user_id'],
    ]);

    if ($stmt->rowCount() > 0) {
        $response = ["success" => true];
    } else {
        $response = ["success" => false, "message" => "Task not found or update failed"];
    }

    // Output sesuai jenis akses
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

} catch (Exception $e) {
    $response = ["success" => false, "message" => $e->getMessage()];
    
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>