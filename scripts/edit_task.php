<?php
session_start();
require_once "../database/database.php";

// Fungsi deteksi akses dari browser
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
    // Ambil data POST dalam format JSON
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Validasi input
    $requiredFields = ['id', 'title', 'description', 'due_date', 'priority', 'category'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            $response = ["success" => false, "message" => "Invalid input"];
            break;
        }
    }

    if (isset($response)) {
        if (isBrowserAccess()) {
            header('Content-Type: text/html');
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        exit();
    }

    // Validasi format tanggal
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data['due_date'])) {
        $response = ["success" => false, "message" => "Format tanggal tidak valid"];

        if (isBrowserAccess()) {
            header('Content-Type: text/html');
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        exit();
    }

    $userId = $_SESSION['user_id'];
    $taskId = $data['id'];

    // Pastikan task milik user yang bersangkutan
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $taskId, 'user_id' => $userId]);

    if ($stmt->rowCount() === 0) {
        $response = ["success" => false, "message" => "Akses ditolak"];
        
        if (isBrowserAccess()) {
            header('Content-Type: text/html');
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        exit();
    }

    // Update task
    $pdo->prepare("
        UPDATE tasks 
        SET title = :title, description = :description, due_date = :due_date, 
            priority = :priority, category = :category 
        WHERE id = :id AND user_id = :user_id
    ")->execute([
        'id' => $taskId,
        'user_id' => $userId,
        'title' => $data['title'],
        'description' => $data['description'],
        'due_date' => $data['due_date'],
        'priority' => $data['priority'],
        'category' => $data['category']
    ]);

    $response = ["success" => true];

} catch (Exception $e) {
    $response = ["success" => false, "message" => "Server error: " . $e->getMessage()];
}

// Output respons sesuai jenis akses
if (isBrowserAccess()) {
    header('Content-Type: text/html');
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
} else {
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>