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

function set_format_phrase($value, $indicator) {
    $result = '';

    $indicators = [
        'minute' => ['минута', 'минуты', 'минут'],
        'hour' => ['час', 'часа', 'часов'],
        'rate' => ['ставка', 'ставки', 'ставок'],
    ];

    if (!isset($indicators[$indicator])) {
        return $result;
    }

    $remainder = $value % 10;

    if ($remainder === 1) {
        $result = $indicators[$indicator][0];
    } else if ($remainder >= 2 && $remainder <= 4) {
        $result = $indicators[$indicator][1];
    } else {
        $result = $indicators[$indicator][2];
    }
    return $result;
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
            $result = $passed_seconds <= 30 ? 'Меньше минуты назад' : 'Минуту назад';
        } else if ($passed_hours === 0) {
            $result = $passed_minutes === 1 ? 'Минуту назад' : sprintf('%d %s назад', $passed_minutes, set_format_phrase($passed_minutes, 'minute'));
        } else if ($passed_hours > 0 && $passed_hours <= 10) {
            $result = $passed_hours === 1 ? 'Час назад' : sprintf('%d %s назад', $passed_hours, set_format_phrase($passed_hours, 'hour'));
        }
    }
    return $result;
}

function get_categories($link, $category) {
    $result = [];
    $sql_category = '';
    if (!empty($category)) {
        $sql_category = "WHERE " . key($category) . "='" . current($category) . "'";
    }
    $sql = "SELECT * FROM categories $sql_category";

    if ($query = mysqli_query($link, $sql)) {
        $result = empty($category) ? mysqli_fetch_all($query, MYSQLI_ASSOC) : mysqli_fetch_array($query, MYSQLI_ASSOC);
    } else {
        die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $result;
}

function get_lots($link, $limit, $search = false, $category_id = false, $page_id = false, $records = false) {
    $result = [];
    $count = 0;

    $filter_by_category = empty($category_id) ? '' : 'AND c.category_id = ' . $category_id;
    $search_filter = empty($search) ? '' : "AND MATCH (title, description) AGAINST ('" . $search . "')";
    $limit_filter = !empty($limit) ? 'LIMIT ' . $limit : '';
    $offset_filter = !empty($page_id) && !empty($limit) ? 'OFFSET ' . ($page_id - 1) * $limit : '';

    $sql =
        "SELECT lot_id, title, start_price, image, COUNT(r.rate_id) AS rates_count, COALESCE(MAX(r.rate),start_price) AS price, completion_date
            FROM lots l
            LEFT JOIN rates r USING (lot_id)
            JOIN categories c USING (category_id)
            WHERE l.completion_date > NOW() $filter_by_category $search_filter
            GROUP BY l.lot_id
            ORDER BY l.creation_date DESC
            $limit_filter
            $offset_filter";


    if ($query = mysqli_query($link, $sql)) {
        if ($records) {
            $count = mysqli_num_rows($query);
        } else {
            $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        }
    } else {
        die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $records ? $count : $result;
}

function get_pagination_information($pages_count, $present_page, $search_information, $max_pages) {
    $previous ='';
    $next ='';

    if ($pages_count <= 1) {
        return [];
    }

    $max_pages = $pages_count < $max_pages ? $pages_count : $max_pages;
    $pagination_information = [];

    $left = $current_page - 1;
    $right = $pages_count - $present_page;
    $middle = (int) ceil($max_pages / 2);
    $left_min = $middle - 1;
    $right_min = $max_pages - $middle;

    if ($present_page > 1) {
        $search_information['page'] = $present_page - 1;
        $previous = ' href="?' . http_build_query($search_information) . '"';
    }
    if ($present_page < $pages_count) {
        $search_information['page'] = $present_page + 1;
        $next = ' href="?' . http_build_query($search_information) . '"';
    }

    $pagination_information[0] = ['page_number' => 'Назад', 'class' => ' pagination-item-prev', 'href' => $previous];
    $j = 1;

    while ($j <= $max_pages) {
        $page_number = $j;

        if ($left > $left_min && $right > $right_min) {
            $page_number = $j + $present_page - $middle;
        } else if ($right <= $right_min) {
            $page_number = $j + $pages_count - $max_pages;
        }

        $page_href = '';
        $class = ' pagination-item-active';
        if ($page_number !== $present_page) {
            $search_information['page'] = $page_number;
            $page_href = ' href="?' . http_build_query($search_information) . '"';
            $class = '';
        }
        $pagination_information[$j] = [
            'page_number' => $page_number,
            'class' => $class,
            'href' => $page_href
        ];

        $j++;
    }
    $pagination_information[$max_pages + 1] = ['page_number' => 'Вперед', 'class' => ' pagination-item-next', 'href' => $next];
    return $pagination_information;
}
?>
