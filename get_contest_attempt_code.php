<?php
    include_once 'config.php';
    $db = new Database();
    $code_attempt_id = $_POST['attempt_id'];

    $attempt_data = $db->get_data_by_table('contest_attempts', ['id' => $code_attempt_id]);

    if ($attempt_data) {
        // DB dagi textni xuddi boricha chiqaramiz
        header('Content-Type: text/plain; charset=utf-8');
        echo $attempt_data['code'];
    } else {
        header('Content-Type: text/plain');
        echo "Kod topilmadi yoki ruxsat yo'q";
    }
?>
