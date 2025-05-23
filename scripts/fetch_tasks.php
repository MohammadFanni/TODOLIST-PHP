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

    // Get filter, sort, and category parameters
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'All Tasks';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'Due Date';
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    // Determine filter condition
    $filterCondition = '';
    switch ($filter) {
        case 'Completed':
            $filterCondition = "AND status = 'completed'";
            break;
        case 'Pending':
            $filterCondition = "AND status = 'pending'";
            break;
        case 'Overdue':
            $filterCondition = "AND status = 'pending' AND due_date < NOW()";
            break;
        default:
            $filterCondition = '';
            break;
    }

    // Determine sort order
    $sortOrder = '';
    switch ($sort) {
        case 'Priority':
            $sortOrder = "ORDER BY priority DESC";
            break;
        case 'Recently Added':
            $sortOrder = "ORDER BY created_at DESC";
            break;
        default:
            $sortOrder = "ORDER BY due_date ASC";
            break;
    }

    // Determine category condition
    $categoryCondition = '';
    if ($category) {
        $categoryCondition = "AND category = :category";
    }

    // Query to fetch tasks
    $sql = "
        SELECT id, title, description, due_date, priority, category, status, created_at 
        FROM tasks 
        WHERE user_id = :user_id 
        $filterCondition
        $categoryCondition
        $sortOrder
    ";
    $stmt = $pdo->prepare($sql);
    $params = ['user_id' => $userId];
    if ($category) {
        $params['category'] = $category;
    }
    $stmt->execute($params);
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
            'created_at' => $task['created_at'],
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