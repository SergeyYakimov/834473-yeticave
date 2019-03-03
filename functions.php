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

function is_registered_email($link, $email) {
    $result = 0;
    $sql = "SELECT user_id FROM users WHERE email = '$email'";
    if ($sql_email = mysqli_query($link, $sql)) {
        $result = mysqli_num_rows($sql_email);
    }
    else {
        die('Возникла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return !($result === 0);
}

function identify_user($link, $object) {
    $result = [];
    $sql_object = '';
    if (!empty($object)) {
        $sql_object = "WHERE " . key($object) . "='" . current($object) . "'";
    }
    $sql_user = "SELECT * FROM users $sql_object";
    if ($query_user = mysqli_query($link, $sql_user)) {
        $result = empty($object) ? mysqli_fetch_all($query_user, MYSQLI_ASSOC) : mysqli_fetch_array($query_user, MYSQLI_ASSOC);
    }
    else {
        die('Возникла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $result;
}
?>
