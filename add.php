<?php
    require_once('init.php');

    $page_content = include_template('add-lot.php', ['categories' => $categories]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => 'Добавление лота', 'user' => $user]);
    print($layout_content);
?>
