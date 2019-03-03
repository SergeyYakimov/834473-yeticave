<?php
require_once('functions.php');
require_once('config/db.php');

date_default_timezone_set("Asia/Irkutsk");

session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

if (!$link) {
    exit("Извините, ведутся технические работы");
}

$categories = [];
$sql_categories = "SELECT * FROM categories";

$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
}

?>
