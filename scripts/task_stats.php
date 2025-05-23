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
    // Get user ID from session
    $userId = $_SESSION['user_id'];

    // Query to get total tasks
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id");
    $stmtTotal->execute(['user_id' => $userId]);
    $totalTasks = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    // Query to get completed tasks
    $stmtCompleted = $pdo->prepare("SELECT COUNT(*) AS completed FROM tasks WHERE user_id = :user_id AND status = 'completed'");
    $stmtCompleted->execute(['user_id' => $userId]);
    $completedTasks = $stmtCompleted->fetch(PDO::FETCH_ASSOC)['completed'];

    // Query to get pending tasks
    $stmtPending = $pdo->prepare("SELECT COUNT(*) AS pending FROM tasks WHERE user_id = :user_id AND status = 'pending'");
    $stmtPending->execute(['user_id' => $userId]);
    $pendingTasks = $stmtPending->fetch(PDO::FETCH_ASSOC)['pending'];

    // Query to get overdue tasks
    $stmtOverdue = $pdo->prepare("
        SELECT COUNT(*) AS overdue 
        FROM tasks 
        WHERE user_id = :user_id 
        AND status = 'pending' 
        AND due_date < NOW()
    ");
    $stmtOverdue->execute(['user_id' => $userId]);
    $overdueTasks = $stmtOverdue->fetch(PDO::FETCH_ASSOC)['overdue'];

    // Prepare response data
    $responseData = [
        "success" => true,
        "data" => [
            "total" => $totalTasks,
            "completed" => $completedTasks,
            "pending" => $pendingTasks,
            "overdue" => $overdueTasks,
        ],
    ];

    // Output sesuai jenis akses
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($responseData);
    }

} catch (Exception $e) {
    $errorResponse = ["success" => false, "message" => $e->getMessage()];
    
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($errorResponse, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($errorResponse);
    }
}
?>