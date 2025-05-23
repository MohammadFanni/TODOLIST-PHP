<?php
require_once "../database/database.php";
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

try {
    $userId = $_SESSION['user_id'];

    // Dapatkan Senin terakhir (atau hari ini jika Senin)
    $today = new DateTime();
    $monday = clone $today;
    $monday->modify('last Monday');
    if ($today->format('D') === 'Mon') {
        $monday = clone $today;
    }

    // Buat label dan list tanggal untuk query
    $labels = [];
    $datesForQuery = [];
    for ($i = 0; $i < 7; $i++) {
        $current = clone $monday;
        $current->modify("+$i days");
        $labels[] = $current->format("D"); // ["Mon", "Tue", ..., "Sun"]
        $datesForQuery[] = $current->format("Y-m-d");
    }

    // Ambil data per hari
    $completedData = [];
    $pendingData = [];
    $overdueData = [];

    foreach ($datesForQuery as $date) {
        // Completed tasks
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM tasks 
            WHERE user_id = ? 
              AND status = 'completed' 
              AND DATE(due_date) = ?
        ");
        $stmt->execute([$userId, $date]);
        $completedData[] = $stmt->fetchColumn();

        // Pending tasks
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM tasks 
            WHERE user_id = ? 
              AND status = 'pending' 
              AND DATE(due_date) = ?
        ");
        $stmt->execute([$userId, $date]);
        $pendingData[] = $stmt->fetchColumn();

        // Overdue tasks (tasks yang due_date-nya < hari ini DAN jatuh pada tanggal $date)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM tasks 
            WHERE user_id = ? 
              AND status = 'pending' 
              AND due_date < ?
              AND DATE(due_date) = ?
        ");
        $stmt->execute([$userId, "$date 23:59:59", $date]);
        $overdueData[] = $stmt->fetchColumn();
    }

    // Kirim hasil dalam format JSON
    echo json_encode([
        "success" => true,
        "data" => [
            "labels" => $labels,
            "completed" => $completedData,
            "pending" => $pendingData,
            "overdue" => $overdueData
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>