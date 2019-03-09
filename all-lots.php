<?php
require_once('init.php');

$limit = $all_lots_page_limit_lots;

$present_category = [];
$category_id = 1;

if (isset($_GET['category'])) {
    $category_id = intval($_GET['category']);
}

$present_category = get_categories($link, ['category_id' => $category_id]);

if (empty($present_category)) {
    $category_id = 1;
    $present_category = get_categories($link, ['category_id' => $category_id]);
}

$lots_count = get_lots($link, false, false, $category_id, false, true);

$pages_count = (int) ceil($lots_count / $limit);

$page_id = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page_id <= 0  || $page_id > $pages_count) {
    $page_id = 1;
}

$pagination_information = get_pagination_information($pages_count, $page_id, ['category' =>  $category_id], 9);

$lots = get_lots($link, $limit, false, $category_id, $page_id);
$title = 'Все лоты в категории «' . $present_category['name'] . '»';

$page_content = include_template('all-lots.php', ['present_category' => $present_category, 'lots' => $lots, 'pagination_information' => $pagination_information, 'message' => 'Лоты в категории отсутствуют.']);
$layout_content = include_template('layout.php', ['present_category' => $present_category, 'content' => $page_content, 'categories' => $categories, 'name_page' => $title, 'user' => $user, 'is_main_page' => $is_main_page]);
print($layout_content);
