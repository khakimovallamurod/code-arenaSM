<?php 
    include_once '../config.php';
    session_start();
    header('Content-Type: application/json; charset=utf-8');

    $fullname = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $otm = $_POST['otm'] ?? '';
    $course = $_POST['course'] ?? '';
    $phone    = $_POST['phone'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = !empty($_POST['password']) ? md5($_POST['password']) : '';

    $ret = [];

    $db = new Database();

    $checkUser = $db->get_data_by_table('users', ['username' => $username]);
    if (!empty($checkUser)) {
        echo json_encode(['error' => 1, 'message' => 'This username is already taken']);
        exit;
    }
    $emailcheck = $db->get_data_by_table('users', ['email' => $email]);
    if (!empty($emailcheck)) {
        echo json_encode(['error' => 1, 'message' => 'This email is already taken']);
        exit;
    }
    $sql = $db->insert('users', [
        'fullname' => $fullname,
        'username' => $username,
        'phone'    => $phone,
        'email'    => $email,
        'otm'  => $otm,
        'course'     => $course,
        'password' => $password
    ]);
    if ($sql != 0) {
        $user = $db->get_data_by_table('users', ['username' => $username]);
        if ($user && isset($user['id'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
        }
        $ret = ['error' => 0, 'message' => 'Successfully registered!'];
    } else {
        $ret = ['error' => 1, 'message' => 'Error! Please try again.'];
    }

    echo json_encode($ret);
?>