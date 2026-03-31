<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
include_once 'config.php';
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Faqat POST so\'rovga ruxsat berilgan.']);
    exit;
}

$contest_id = isset($_POST['contestid']) ? intval($_POST['contestid']) : 0;
$user_id = isset($_POST['userid']) ? intval($_POST['userid']) : 0;

if ($contest_id <= 0 || $user_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri ma\'lumot yuborildi.']);
    exit;
}

$existsSql = "SELECT status FROM contest_register WHERE contest_id = {$contest_id} AND user_id = {$user_id} LIMIT 1";
$existsResult = $db->query($existsSql);

if ($existsResult === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ro\'yxatdan o\'tishni tekshirishda xatolik.']);
    exit;
}

if (mysqli_num_rows($existsResult) > 0) {
    $row = mysqli_fetch_assoc($existsResult);
    $status = isset($row['status']) ? intval($row['status']) : 0;

    if ($status === 1) {
        echo json_encode(['success' => false, 'message' => "Siz ro'yxatdan o'tgansiz."]);
        exit;
    }

    $updateSql = "UPDATE contest_register SET status = 1 WHERE contest_id = {$contest_id} AND user_id = {$user_id} LIMIT 1";
    $updateResult = $db->query($updateSql);

    if ($updateResult) {
        echo json_encode(['success' => true, 'message' => "Siz muvaffaqiyatli ro'yxatdan o'tdingiz!"]);
        exit;
    }

    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Ro'yxatdan o'tishda xatolik yuz berdi."]);
    exit;
}

$insertSql = "INSERT INTO contest_register (contest_id, user_id, status) VALUES ({$contest_id}, {$user_id}, 1)";
$insertResult = $db->query($insertSql);

if ($insertResult) {
    echo json_encode(['success' => true, 'message' => "Siz muvaffaqiyatli ro'yxatdan o'tdingiz!"]);
    exit;
}

http_response_code(500);
echo json_encode(['success' => false, 'message' => "Ro'yxatdan o'tishda xatolik yuz berdi."]);
exit;
