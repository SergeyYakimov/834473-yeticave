<?php

require_once('init.php');

$max_step_rate = 500000;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    header("HTTP/1.0 404 Not Found");
    $error = [
        'name' => 'Ошибка 404. Страница не найдена',
        'message' => 'Данной страницы не существует на сайте'
    ];
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => '404 - Страница не найдена', 'user' => $user,'is_main_page' => $is_main_page]);
    print($layout_content);
    die();
}

$sql_lot = "SELECT lot_id, description, step_rate, completion_date, title, author_id, start_price, image, COALESCE(MAX(r.rate), start_price) AS price, c.name FROM lots
            JOIN categories c USING (category_id)
            LEFT JOIN rates r USING (lot_id)
            WHERE lot_id = $id
            GROUP BY lot_id";
$result_lot = mysqli_query($link, $sql_lot);

if (!mysqli_num_rows($result_lot)) {
    header("HTTP/1.0 404 Not Found");
    $error = [
        'name' => 'Ошибка 404. Страница не найдена',
        'message' => 'Данной страницы не существует на сайте'
    ];
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => '404 - Страница не найдена', 'user' => $user, 'is_main_page' => $is_main_page]);
    print($layout_content);
    die();
}

$lot = mysqli_fetch_assoc($result_lot);

$rates = get_rates($link, $lot['lot_id']);

$show_add_rate_form = false;

if ((time() <= strtotime($lot['completion_date'])) && !empty($user) && $user['user_id'] !== $lot['author_id']
    && (empty($rates) || $rates[0]['user_id'] !== $user['user_id'])) {
    $show_add_rate_form = true;
}

$information = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$show_add_rate_form) {
        header("Location: lot.php?id=" . $lot['lot_id']);
        die();
    }

    if (isset($_POST['cost']) && !empty(trim($_POST['cost']))) {
        $information['cost'] = trim($_POST['cost']);
    } else {
        $errors['cost'] = 'Это поле необходимо заполнить';
    }

    if (empty($errors['cost'])) {
        if (!ctype_digit($information['cost']) || $information['cost'] <= 0) {
            $errors['cost'] = 'Ставка должна быть целым положительным числом';
        } else if ($information['cost'] < $lot['price'] + $lot['step_rate']) {
            $errors['cost'] = 'Ставка должна быть не меньше минимально возможной';
        } else if ($information['cost'] - $lot['price'] > $max_step_rate) {
            $errors['cost'] = 'Ставка слишком большая. Максимальный шаг ставки - ' . $max_step_rate . ' р';
        }
    }

    if (empty($errors)) {
        $information['user_id'] = $user['user_id'];
        $information['lot_id'] = $lot['lot_id'];
        if ((time() >= strtotime($lot['completion_date']))) {
            header("HTTP/1.0 403 Forbidden");
            $error = [
                'name' => 'Ошибка 403. Действие запрещено',
                'message' => 'Аукцион по данному лоту завершен'
            ];
            $page_content = include_template('error.php', ['error' => $error]);
            $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => '403 - Действие запрещено', 'user' => $user,'is_main_page' => $is_main_page]);
            print($layout_content);
            die();
        }

        $rate_id = add_rate($link, $information);
        $show_add_rate_form = false;

        $lot['price'] = $information['cost'];
        array_unshift($rates, [
            'date' => date('Y-m-d H:i:s'),
            'rate' => $information['cost'],
            'user_id' => $user['user_id'],
            'user' => $user['name']
        ]);
    }
}

$page_content = include_template('lot.php', ['lot' => $lot, 'user' => $user, 'errors' => $errors, 'information' => $information, 'rates' => $rates, 'show_add_rate_form' => $show_add_rate_form]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => $lot['title'], 'user' => $user, 'is_main_page' => $is_main_page]);
print($layout_content);
?>
