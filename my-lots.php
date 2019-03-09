<?php
require_once('init.php');

if (empty($user)) {
    header("Location: login.php");
    die();
}

$user_rates = get_user_rates($link, $user['user_id']);

foreach($user_rates as $key => $rate) {
    $is_end = false;
    $is_finishing = false;
    $is_win = $user['user_id'] === $rate['winner_id'] ? true : false;
    $time_end = strtotime($rate['lot_completion_date']) - time();
    if ($time_end > 0 && $time_end <= 7200) {
        $is_finishing = true;
    } else if ($time_end <= 0) {
        $is_end = true;
    }
    $user_rates[$key]['is_finishing'] = $is_finishing;
    $user_rates[$key]['is_end'] = $is_end;
    $user_rates[$key]['is_win'] = $is_win;
}

$page_content = include_template('my-lots.php', ['rates' => $user_rates, 'user' => $user]);
$layout_content = include_template('layout.php', ['name_page' => 'Мои ставки', 'is_main_page' => $is_main_page, 'content' => $page_content, 'user' => $user, 'categories' => $categories]);
print($layout_content);
