<?php
require_once('mysql_helper.php');

function format_price($price) {
    $integer_price = ceil($price);
    return number_format($integer_price, 0, '.', ' ') . '<b class="rub">р</b>';
}

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function get_time_till_closing_time($closing_time) {
    $diff_time = strtotime($closing_time) - time();
    $sec_in_day = 86400;
    $num_days = $diff_time / $sec_in_day;
    if ($num_days > 3) {
        $format_time = gmdate("d.m.Y", strtotime($closing_time));
    } else if ($num_days <= 3 && $num_days > 1) {
        $format_time = floor($num_days) . (floor($num_days) > 1 ? ' дня' : ' день');
    } else {
        $format_time = gmdate("H:i", $diff_time);
    }
    return $format_time;
}

function remove_image($path, $tmp_name) {
    $path = pathinfo($path);
    $new_path = 'img/' . uniqid() . '.' . $path['extension'];
    move_uploaded_file($tmp_name, $new_path);
    return $new_path;
}

function set_difference_in_days($date) {
    $timestamp_lot = strtotime($date);
    $passed_secs = $timestamp_lot - strtotime('now');
    $days = floor($passed_secs / 86400);
    if ($days > 0) {
        return true;
    }
    return false;
}
?>
