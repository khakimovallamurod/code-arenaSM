<?php
    include_once '../config.php';
    $login = $_POST['username'];
    $arr = ['username'=>$login];
    $db = new Database();
    $sql = $db->get_data_by_table('users', $arr);
    $ret = [];
    if (!empty($sql)) {
        $ret = ['error' => 0, 'message' => 'This username is busy'];
    } else {
        $ret = ['error' => 1, 'message' => 'Success'];
    }
    echo json_encode($ret);

?>