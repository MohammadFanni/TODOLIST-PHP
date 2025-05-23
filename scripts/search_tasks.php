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
    if (!isset($data['query'])) {
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

    $query = "%" . $data['query'] . "%";
    $userId = $_SESSION['user_id'];

    // Query to fetch tasks based on search query
    $stmt = $pdo->prepare("
        SELECT id, title, description, due_date, priority, category, status 
        FROM tasks 
        WHERE user_id = :user_id 
        AND (title LIKE :query OR description LIKE :query OR category LIKE :query)
        ORDER BY due_date ASC
    ");
    $stmt->execute([
        'user_id' => $userId,
        'query' => $query,
    ]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format tasks for JSON response
    $formattedTasks = array_map(function ($task) {
        return [
            'id' => $task['id'],
            'title' => $task['title'],
            'description' => $task['description'],
            'due_date' => $task['due_date'],
            'priority' => $task['priority'],
            'category' => $task['category'],
            'status' => $task['status'],
        ];
    }, $tasks);

    // Prepare final response
    $response = [
        "success" => true,
        "data" => $formattedTasks,
    ];

    // Output sesuai jenis akses
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($response);
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