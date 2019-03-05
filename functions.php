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

function get_rates($link, $lot_id) {
    $result = [];
    $sql_rates =
        "SELECT date, rate, r.user_id, u.name AS user
            FROM rates r
            JOIN users u USING (user_id)
            WHERE lot_id = $lot_id
            ORDER BY date DESC";
    if ($query_rates = mysqli_query($link, $sql_rates)) {
        $result = mysqli_fetch_all($query_rates, MYSQLI_ASSOC);
    } else {
        die('Возникла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $result;
}

function add_rate($link, $information) {
    $rate_id = '';
    $sql_rate = "INSERT INTO rates (rate, user_id, lot_id) VALUES (?, ?, ?)";
    $stmt = db_get_prepare_stmt($link, $sql_rate, [
        $information['cost'],
        $information['user_id'],
        $information['lot_id']
    ]);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $rate_id = mysqli_insert_id($link);
    } else {
        die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $rate_id;
}

function get_rate_add_time($time) {
    $add_time = strtotime($time);
    $result = date('d.m.y в H:i', $add_time);
    if ($add_time > time()) {
        return 'Ошибка! Время превышает текущее значение времени';
    }
    $passed_seconds = time() - $add_time;
    $passed_minutes = (int) floor(($passed_seconds % 3600) / 60);
    $passed_hours = (int) floor(($passed_seconds % 86400) / 3600);
    $passed_days = (int) floor($passed_seconds / 86400);

    if ($add_time >= strtotime('yesterday')) {
        $result = sprintf('Вчера в %s', date('H:i', $add_time));
    }
    if ($add_time >= strtotime('today')) {
        $result = sprintf('Сегодня в %s', date('H:i', $add_time));
    }
    if ($passed_days === 0) {
        if ($passed_hours === 0 && $passed_minutes === 0) {
            $result = $passed_seconds <= 30 ? 'Только что' : 'Минута назад';
        } else if ($passed_hours === 0) {
            $result = $passed_minutes === 1 ? 'Минута назад' : sprintf('%d %s назад', $passed_minutes);
        } else if ($passed_hours > 0 && $passed_hours <= 10) {
            $result = $passed_hours === 1 ? 'Час назад' : sprintf('%d %s назад', $passed_hours);
        }
    }
    return $result;
}
?>
