<?php

require_once "../database/database.php";

session_start();


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Cek apakah user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Folder tujuan upload
$uploadDir = "../public/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Buat folder jika belum ada
}

// Validasi file
if (!isset($_FILES["profile_picture"])) {
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
    exit;
}

$file = $_FILES["profile_picture"];
$ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
$allowedExtensions = ["jpg", "jpeg", "png", "gif"];

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(["success" => false, "message" => "Invalid file type. Allowed: JPG, JPEG, PNG, GIF"]);
    exit;
}

// Nama file unik berdasarkan ID user
$newFileName = "profile_" . $userId . "." . $ext;
$uploadPath = $uploadDir . $newFileName;
$publicUrl = "/public/uploads/profile_pictures/" . $newFileName;

// Hapus file lama jika sudah ada
$oldFilePath = $uploadDir . $newFileName;
if (file_exists($oldFilePath)) {
    unlink($oldFilePath);
}

// Upload file baru
if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
    // Update database
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->execute([$newFileName, $userId]);

    echo json_encode([
        "success" => true,
        "profile_picture" => $publicUrl . "?t=" . time() // cache-busting
    ]);
} else {
    echo json_encode(["success" => false, "message" => "File upload failed"]);
}
?>