<?php

require_once('init.php');
require_once('lots.php');

$page_content = include_template('index.php', ['categories' => $categories, 'lots_list' => $lots_list]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => 'Главная', 'user' => $user]);
print($layout_content);
?>
