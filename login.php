<?php
require_once('init.php');
require_once('functions.php');

if(!empty($user)) {
    header("Location: /");
    die();
}

$information = [];
$is_error_authentication = false;
$errors = [];

$user_information = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keys = ['email', 'password'];
    foreach ($keys as $key) {
        if (isset($_POST[$key]) && !empty(trim($_POST[$key]))) {
            $information[$key] = trim($_POST[$key]);
        }
        else {
            $errors[$key] = 'Это поле необходимо заполнить';
        }
    }

    if (empty($errors['email'])) {
        if (!filter_var($information['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный формат адреса электронной почты';
        }
        else {
            $user_information = identify_user($link, ['email' => mysqli_real_escape_string($link, $information['email'])]);
        }
    }

    if (empty($errors['password'])) {
        if (!empty($user_information) && password_verify($information['password'], $user_information['password'])) {
            $user['user_id'] = $user_information['user_id'];
            $user['name'] = $user_information['name'];
            $user['avatar'] = $user_information['avatar'];
        }
        else {
            $is_error_authentication = true;
        }
    }

    if(empty($errors) && !$is_error_authentication) {
        $_SESSION['user'] = $user;
        header("Location: /");
        die();
    }
}

$page_content = include_template('login.php', ['errors' => $errors, 'information' => $information, 'categories' => $categories, 'is_error_authentication' => $is_error_authentication]);
$layout_content = include_template('layout.php', ['name_page' => 'Вход', 'content' => $page_content, 'user' => $user,'categories' => $categories]);
print($layout_content);
?>
