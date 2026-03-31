<?php 
    session_start();
    include_once '../config.php';
    $login = $_POST['username'];
    $parol = md5($_POST['password']);
    $db = new Database();
    $fetch = $db->get_data_by_table('users', ['username'=>$login, 'password'=>$parol]);
    $ret = [];
    if($fetch){
        $_SESSION['username'] = $fetch['username'];
        $_SESSION['id'] = $fetch['id'];
        $_SESSION['fullname'] = $fetch['fullname'];
        $_SESSION['role'] = $fetch['role'];
        $ret += ['error'=>0, 'message'=>"You have successfully logged in!", 'rederict'=> $_SESSION['role']];
    }else{
        $ret += ['error'=>1, 'message'=>"Error! Invalid login or password"];

    }
    echo json_encode($ret);
?>