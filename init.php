<?php
require_once('functions.php');
require_once('config/db.php');

date_default_timezone_set("Asia/Irkutsk");

$user = [
    'name' => 'Sergey Yakimov',
    'image' => 'img/user.jpg'
];

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

if (!$link) {
    exit("Извините, ведутся технические работы");
}

$categories = [];
$lots_list = [];
$sql_categories = "SELECT * FROM categories";
$sql_lots = "SELECT title, start_price, image, completion_date, c.name FROM lots
            JOIN categories c USING (category_id)
            WHERE completion_date > NOW()
            ORDER BY creation_date DESC";
$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
}
$result_lots = mysqli_query($link, $sql_lots);
if ($result_lots) {
    $lots_list = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
}
?>
