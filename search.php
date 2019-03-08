<?php
require_once('init.php');

$limit = $search_page_limit_lots;

$search = ['text' => '', 'category' => ''];

if (!isset($_GET['search']) || empty(trim($_GET['search']))) {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'http://' . $_SERVER['SERVER_NAME']) === 0) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: /");
    }
    die();
}

$category_id = 0;
$search['text'] = trim($_GET['search']);
if (isset($_GET['category'])) {
    $category_id = intval($_GET['category']);
    $category = get_categories($link, ['category_id' => $category_id]);

    if (!empty($category)) {
        $search['category'] = $category['name'];
    } else {
        $category_id = 0;
    }
}

$lots_count = get_lots($link, false, mysqli_real_escape_string($link, $search['text']), $category_id, false, true);
$pages_count = (int) ceil($lots_count / $limit);

$page_id = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page_id <= 0  || $page_id > $pages_count) {
    $page_id = 1;
}

$search_information = ['search' => $search['text']];
if (!empty($category_id)) {
    $search_information = array_merge(['category' => $category_id], $search_information);
}

$pagination_information = get_pagination_information($pages_count, $page_id, $search_information, 9);

$lots = get_lots($link, $limit, mysqli_real_escape_string($link, $search['text']), $category_id, $page_id);
$page_content = include_template('search.php', ['search' => $search, 'lots' => $lots,'pagination_information' => $pagination_information, 'message' => 'Ничего не найдено по вашему запросу.']);
$layout_content = include_template('layout.php', ['present_category' => $category,'content' => $page_content, 'categories' => $categories, 'name_page' => 'Результаты поиска', 'user' => $user, 'is_main_page' => $is_main_page]);
print($layout_content);
