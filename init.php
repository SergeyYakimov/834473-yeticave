<?php
require_once('functions.php');
require_once('config/db.php');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set("Asia/Irkutsk");
$search_page_limit_lots = 9;
$all_lots_page_limit_lots = 9;

session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if (!$link) {
    exit("Извините, ведутся технические работы");
}

mysqli_set_charset($link, "utf8");

$is_main_page = false;

$categories = [];
$sql_categories = "SELECT * FROM categories";

$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
}

?>
