<?php
require_once('init.php');

if (!empty($user)) {
    header("Location: /");
    die();
}

$min_length_password = 6;
$max_length_password = 64;
$max_length_name = 50;
$max_length_contacts = 255;


$errors = [];

$information = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keys = ['email', 'password', 'name', 'contacts'];
    $file_user_name = '';
    foreach ($keys as $key) {
        if (isset($_POST[$key]) && !empty(trim($_POST[$key]))) {
            $information[$key] = trim($_POST[$key]);
        } else {
            $errors[$key] = 'Это поле необходимо заполнить';
        }
    }

    if (empty($errors['email'])) {
        if (!filter_var($information['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный формат адреса электронной почты';
        } elseif (is_registered_email($link, mysqli_real_escape_string($link, $information['email']))) {
            $errors['email'] = 'Пользователь с указанным e-mail уже зарегистрирован';
        }
    }

    if (empty($errors['password'])) {
        if (strlen($information['password']) < $min_length_password) {
            $errors['password'] = 'Минимальная длина пароля - ' . $min_length_password . ' символов';
        } elseif (strlen($information['password']) > $max_length_password) {
            $errors['password'] = 'Максимальная длина пароля - ' . $max_length_password . ' символов';
        }
    }

    if (empty($errors['name']) && strlen($information['name']) > $max_length_name) {
        $errors['name'] = 'Cлишком длинное имя. Максимальное количество символов - ' . $max_length_name;
    }

    if (empty($errors['contacts']) && strlen($information['contacts']) > $max_length_contacts) {
        $errors['contacts'] = 'Слишком длинное сообщение. Максимальное количество символов - ' . $max_length_contacts;
    }

    if (isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        $tmp_name = $_FILES['avatar']['tmp_name'];
        $file_type = mime_content_type($tmp_name);
        if ($file_type !== 'image/png' && $file_type !== 'image/jpeg') {
            $errors['avatar'] = 'Неправильный тип файла. Загрузите файл в правильном формате (jpeg, jpg или png)';
        } else {
            $file_extension = $file_type === 'image/jpeg' ? '.jpg' : '.png';
            $file_user_name = uniqid('user-') . $file_extension;
        }
    }

    if (empty($errors)) {
        $information['password'] = password_hash($information['password'], PASSWORD_DEFAULT);

        if (!empty($file_user_name)) {
            move_uploaded_file($_FILES['avatar']['tmp_name'], 'avatars/' . $file_user_name);
            $information['avatar'] = 'avatars/' . $file_user_name;
            $add_user = "INSERT INTO users (email, name, password, avatar, contact_information) VALUES (?, ?, ?, ?, ?)";
            $stmt_user = db_get_prepare_stmt($link, $add_user, [
                $information['email'],
                $information['name'],
                $information['password'],
                $information['avatar'],
                $information['contacts']]);
        } else {
            $add_user = "INSERT INTO users (email, name, password, contact_information) VALUES (?, ?, ?, ?)";
            $stmt_user = db_get_prepare_stmt($link, $add_user, [
                $information['email'],
                $information['name'],
                $information['password'],
                $information['contacts']]);
        }

        $is_add_user = mysqli_stmt_execute($stmt_user);
        if (!$is_add_user) {
            die('Произошла ошибка. Пожалуйста, попробуйте снова.');
        }
        header("Location: login.php");
        die();
    }
}
$page_content = include_template('sign-up.php', [
    'errors' => $errors,
    'information' => $information
]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => 'Регистрация', 'user' => $user, 'is_main_page' => $is_main_page]);
print($layout_content);
