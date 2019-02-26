<?php

require_once('init.php');
require_once('functions.php');

$mime_types = ['image/pjpeg', 'image/jpeg', 'image/png'];

$author_id = $user['user_id'];

$add = "INSERT INTO lots (title, description, image, start_price, completion_date, step_rate, category_id, author_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, '$author_id')";

$dict = [
    'lot-name' => 'Название',
    'category' => 'Категория',
    'message' => 'Описание',
    'lot_img' => 'Изображение',
    'lot-rate' => 'Начальная цена',
    'lot-step' => 'Шаг ставки',
    'lot-date' => 'Дата окончания'
];

$max_length_title = 128;
$max_length_description = 255;
$max_rate = 50000000;
$max_step = 50000000;

$errors = false;

$lot = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    $keys = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

    foreach ($keys as $key) {
        if (isset($_POST[$key]) && !empty(trim($_POST[$key]))) {
            $lot[$key] = trim($_POST[$key]);
        }
        else {
            $errors[$key] = 'Это поле необходимо заполнить';
        }
    }

    if (empty($errors['lot-name']) && strlen($lot['lot-name']) > $max_length_title) {
        $errors['lot-name'] = 'Слишком длинное наименование. Максимальное количество символов - ' . $max_length_title;
    }

    if (empty($errors['message']) && strlen($lot['message']) > $max_length_description) {
        $errors['message'] = 'Описание слишком длинное. Максимальное количество символов - ' . $max_length_description;
    }

    if (empty($errors['lot-rate'])) {
        $lot['lot-rate'] = intval($lot['lot-rate']);
        if ($lot['lot-rate'] <= 0) {
            $errors['lot-rate'] = 'Введенное значение должно быть положительным числом.';
        } else if ($lot['lot-rate'] > $max_rate) {
            $errors['lot-rate'] = 'Превышено значение шага ставки. Максимальный шаг - ' . $max_rate . ' р';
        }
    }

    if (empty($errors['lot-step'])) {
        $lot['lot-step'] = intval($lot['lot-step']);
        if ($lot['lot-step'] <= 0) {
            $errors['lot-step'] = 'Введенное значение должно быть положительным числом.';
        } else if ($lot['lot-step'] > $max_step) {
            $errors['lot-step'] = 'Превышено значение шага ставки. Максимальный шаг - ' . $max_step . ' р';
        }
    }

    if (empty($errors['lot-date'])) {
        if( ($lot['lot-date']) !== date('Y-m-d', strtotime($lot['lot-date'])) || strtotime($lot['lot-date']) < strtotime('tomorrow')) {
            $errors['lot-date'] = 'Введите корректную дату завершения торгов, которая позже текущей даты хотя бы на один день';
        }
    }

    if (isset($_FILES['lot_img']) && is_uploaded_file($_FILES['lot_img']['tmp_name'])) {
        $tmp_name = $_FILES['lot_img']['tmp_name'];
        $file_type = mime_content_type($tmp_name);

        if(!array_search($file_type, $mime_types)) {
            $errors['lot_img'] = 'Загрузите картинку лота в формате PNG или JPEG';
        }
    } else {
        $errors['lot_img'] = 'Нет изображения лота';
    }

    if(!$errors) {
        $file_extension = $file_type === 'image/jpeg' ? '.jpg' : '.png';
        $file_name = uniqid('lot-' . $user['user_id'] . '-') . $file_extension;
        move_uploaded_file($_FILES['lot_img']['tmp_name'], 'img/' . $file_name);
        $lot['lot_img'] = 'img/' . $file_name;

        $id = $lot['category'];
        $sql_id = "SELECT category_id FROM categories WHERE `name` = '$id'";
        $result_id = mysqli_query($link, $sql_id);


        if ($result_id) {
            $category_id = mysqli_fetch_assoc($result_id);
            $stmt = db_get_prepare_stmt($link, $add, [
                               $lot['lot-name'],
                               $lot['message'],
                               $lot['lot_img'],
                               intval($lot['lot-rate']),
                               $lot['lot-date'],
                               intval($lot['lot-step']),
                               intval($category_id)]);
            $is_add = mysqli_stmt_execute($stmt);
        }

        if($is_add) {
            $lot_id = mysqli_insert_id($link);
            header('Location: lot.php?id=' . $lot_id);
            die();
        } else {
            header("HTTP/1.0 404 Not Found");
            $fail_content = include_template('error.php', ['categories' => $categories]);
            $page = include_template('layout.php', ['content' => $fail_content, 'categories' => $categories, 'name_page' => 'Ошибка', 'user' => $user]);
            die();
        }
    }
}

$add_lot = include_template('add-lot.php', ['categories' => $categories, 'errors' => $errors, 'dict' => $dict]);
$page = include_template('layout.php', ['content' => $add_lot, 'categories' => $categories, 'name_page' => 'Добавление лота', 'user' => $user,]);
print($page);
?>
