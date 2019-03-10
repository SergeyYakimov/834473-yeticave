<?php

require_once('init.php');
require_once('getwinner.php');

$is_main_page = true;

$lots_list = [];

$sql_lots = "SELECT lot_id, title, start_price, image, completion_date, c.name FROM lots
            JOIN categories c USING (category_id)
            WHERE completion_date > NOW()
            ORDER BY creation_date DESC";

$result_lots = mysqli_query($link, $sql_lots);
if ($result_lots) {
    $lots_list = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
}

$page_content = include_template('index.php', ['categories' => $categories, 'lots_list' => $lots_list]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => 'Главная', 'user' => $user,'is_main_page' => $is_main_page]);
print($layout_content);
?>
