<?php

require_once('init.php');
require_once('functions.php');

$mime_types = ['image/pjpeg', 'image/jpeg', 'image/png'];

$add = "INSERT INTO lots (title, description, image, start_price, completion_date, step_rate, category_id, author_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

$dict = [
    'lot-name' => 'Название',
    'category' => 'Категория',
    'message' => 'Описание',
    'image' => 'Изображение',
    'lot-rate' => 'Начальная цена',
    'lot-step' => 'Шаг ставки',
    'lot-date' => 'Дата окончания'
];

$errors = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    $errors = [];

    foreach($lot as $key => $value) {
        if(empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
        if($key === 'category' && $value === 'Выберите категорию') {
            $errors[$key] = 'Выберите категорию';
        }
    }

    foreach($lot as $key => $value) {
        if($key === 'lot-rate' || $key === 'lot-step') {
            if(!filter_var($value, FILTER_VALIDATE_INT)) {
                $errors[$key] = 'Введенное значение не является целым числом.';
            } else {
                if($value <= 0) {
                    $errors[$key] = 'Введенное значение должно быть положительным числом.';
                }
            }
        }
    }

    if( ($lot['lot-date']) !== date('Y-m-d', strtotime($lot['lot-date'])) || strtotime($lot['lot-date']) < strtotime('tomorrow')) {
        $errors['lot-date'] = 'Введите корректную дату завершения торгов, которая позже текущей даты хотя бы на один день';
    }



    if($_FILES['lot_img']['name']) {
        $tmp_name = $_FILES['lot_img']['tmp_name'];
        $path = $_FILES['lot_img']['name'];
        $file_type = mime_content_type($tmp_name);

        if(!array_search($file_type, $mime_types)) {
            $errors['image'] = 'Загрузите картинку лота в формате PNG или JPEG';
        }
    } else {
        $errors['image'] = 'Нет изображения лота';
    }
    if(!$errors) {
        $tmp_name = $_FILES['lot_img']['tmp_name'];
        $path = $_FILES['lot_img']['name'];
        $lot['image'] = remove_image($path, $tmp_name);

        $id = $lot['category'];
        $sql_id = "SELECT category_id FROM categories WHERE `name` = '$id'";
        $result_id = mysqli_query($link, $sql_id);

        if ($result_id) {
            $category_id = mysqli_fetch_assoc($result_id);
            $stmt = db_get_prepare_stmt($link, $add, [
                               $lot['lot-name'],
                               $lot['message'],
                               $lot['image'],
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
