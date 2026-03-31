<?php
    include_once 'config.php';
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Noto'g'ri so'rov turi."]);
        exit;
    }

    $user_id  = intval($_POST['user_id'] ?? 0);
    $cn_problem_id = intval($_POST['problem_id'] ?? 0);
    $cn_contest_id = intval($_POST['contest_id'] ?? 0);
    $language = trim($_POST['language'] ?? '');
    $code = $_POST['code'] ?? '';

    if ($user_id <= 0 || $cn_problem_id <= 0 || $cn_contest_id <= 0 || $language === '' || $code === '') {
        http_response_code(422);
        echo json_encode(["success" => false, "message" => "Yuborilgan ma'lumotlar to'liq emas."]);
        exit;
    }

    $db = new Database();
    $data = [
        "user_id" => $user_id,
        "problem_id" => $cn_problem_id,
        "contest_id" => $cn_contest_id,
        "language" => $language,
        "code" => $code
    ];
    $insert = $db->insert('cncode_attempts', $data);
    if($insert){
        echo json_encode(["success"=>true, "message"=>"Code muvaffaqiyatli saqlandi.", "attempt_id"=>$insert]);
    } else {
        http_response_code(500);
        echo json_encode(["success"=>false, "message"=>"Code saqlashda xatolik yuz berdi."]);
    }
?>


