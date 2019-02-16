<?php

require_once('init.php');

$id = intval($_GET['id']);
if (!isset($id)) {
    $page_content = include_template('error.php', ['categories' => $categories]);
}

$sql_lot = "SELECT lot_id, description, step_rate, completion_date, title, start_price, image, COALESCE(MAX(r.rate), start_price) AS price, c.name FROM lots
            JOIN categories c USING (category_id)
            LEFT JOIN rates r USING (lot_id)
            WHERE lot_id = $id
            GROUP BY lot_id";
$result_lot = mysqli_query($link, $sql_lot);

if (mysqli_num_rows($result_lot)) {
    $lot = mysqli_fetch_assoc($result_lot);
    $page_content = include_template('lot.php', ['categories' => $categories, 'lot' => $lot]);
} else {
    $page_content = include_template('error.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => $lot['title'], 'user' => $user]);
print($layout_content);
?>
