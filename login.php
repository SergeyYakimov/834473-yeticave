<?php
require_once('init.php');

if(!empty($user)) {
    header("Location: /");
    die();
}

$information = [];
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
            $user = $user_information;
        }
        else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    }

    if(empty($errors)) {
        $_SESSION['user'] = $user;
        header("Location: /");
        die();
    }
}

$page_content = include_template('login.php', ['errors' => $errors, 'information' => $information]);
$layout_content = include_template('layout.php', ['name_page' => 'Вход', 'content' => $page_content, 'user' => $user,'categories' => $categories,'is_main_page' => $is_main_page]);
print($layout_content);
?>
