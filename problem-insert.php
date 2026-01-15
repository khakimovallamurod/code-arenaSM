<?php
    include_once 'config.php';
    $db = new Database();
    $user_id  = $_POST['user_id'];
    $problem_id = $_POST['problem_id'];

    $language = $_POST['language'];
    $code = $_POST['code'];
    $data = [
        "user_id" => $user_id,
        "problem_id" => $problem_id,
        "language" => $language,
        "code" => $code
    ];
    $insert = $db->insert('code_attempts', $data);
    if($insert){
        echo json_encode(["success"=>true, "message"=>"Code muvaffaqiyatli saqlandi.", "attempt_id"=>$insert]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Code saqlashda xatolik yuz berdi."]);
    }
?>



