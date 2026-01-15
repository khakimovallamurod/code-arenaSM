<?php
    include_once 'config.php';
    $db = new Database();
    $user_id  = $_POST['user_id'];
    $cn_problem_id = intval($_POST['problem_id']);
    $cn_contest_id = intval($_POST['contest_id']);
    $language = $_POST['language'];
    $code = $_POST['code'];
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
        echo json_encode(["success"=>false, "message"=>"Code saqlashda xatolik yuz berdi."]);
    }
?>



