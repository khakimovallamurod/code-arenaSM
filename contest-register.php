<?php
    include_once 'config.php';
    $db = new Database();
    $contest_id = $_POST['contestid'];
    $user_id = $_POST['userid'];
    $data = [
        'contest_id' => $contest_id,
        'user_id' => $user_id
    ];
    $get_data = $db->get_data_by_table('contest_register', $data);
    if ($get_data) {
        $arr = ['success'=>false, 'message'=>"Siz ro'yxatdan o'tgansiz."];
        echo json_encode($arr);
        exit;
    }else{
        $sql = $db->insert('contest_register', $data);
        if ($sql) {
            $arr = ['success'=>true, 'message'=>"Siz muvaffaqiyatli ro'yxatdan o'tdingiz!"];
        } else {
            $arr = ['success'=>false, 'message'=>"Ro'yxatdan o'tishda xatolik yuz berdi."];
        }
        echo json_encode($arr);
        exit; 
    }
?>