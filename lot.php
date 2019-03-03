<?php

require_once('init.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    header("HTTP/1.0 404 Not Found");
    $error = [
        'name' => 'Ошибка 404. Страница не найдена',
        'message' => 'Данной страницы не существует на сайте'
    ];
    $page_content = include_template('error.php', ['categories' => $categories, 'error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => '404 - Страница не найдена', 'user' => $user]);
    print($layout_content);
    die();
}

$sql_lot = "SELECT lot_id, description, step_rate, completion_date, title, start_price, image, COALESCE(MAX(r.rate), start_price) AS price, c.name FROM lots
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
    $page_content = include_template('error.php', ['categories' => $categories, 'error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => '404 - Страница не найдена', 'user' => $user]);
    print($layout_content);
    die();
}

$lot = mysqli_fetch_assoc($result_lot);
$page_content = include_template('lot.php', ['categories' => $categories, 'lot' => $lot]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => $lot['title'], 'user' => $user]);
print($layout_content);
?>
