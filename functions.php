<?php
require_once('mysql_helper.php');

/**
* Функция для форматирования суммы
* @param integer|float $price Цена в числовом формате
* @return string Отформатированная цена в денежном формате
*/

function format_price($price) {
    $integer_price = ceil($price);
    return number_format($integer_price, 0, '.', ' ') . '<b class="rub">р</b>';
}

/**
* Функция-шаблонизатор
* @param string $name Имя файла шаблона
* @param array $data Данные, передаваемые в шаблон
* @return string Готовая часть разметки страницы
*/

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

/**
* Отображение окончания лота в соответствующем формате
* @param string $closing_time Дата и время полученные из БД
* @return string Отображение времени завершения лота в подобранном формате
*/

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

/**
* Функция, определяющая имеется ли в базе данных пользователь с таким же email
* @param mysqli $link Ресурс подключения к базе данных
* @param string $email E-mail адрес
* @return bool true - пользователь с указанным e-mail найден, false - не найден
*/

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

/**
 * Получает данные конкретного пользователя из базы данных
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param array $object Ассоциативный массив, задающий значения для поиска
 * @return array Массив данных пользователя
 */

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

/**
 * Получает из базы данных массив ставок для конкретного лота
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param integer $lot_id id лота
 * @return array Массив ставок для конкретного лота
 */

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

/**
 * Выполняет запись новой строки в таблицу ставок базы данных на основе переданных данных и возвращает id этой строки
 *
 * @param mysqli $link Ресурс соединения с базой данных
 * @param array $information Массив данных для записи новой ставки в базу данных
 * @return integer id записанной строки
 */

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

/**
 * Добавляет к числу соответствующее наименование в правильном падеже
 *
 * @param integer $value Числовое значение
 * @param string $indicator Индикатор, указывающий на конкретные варианты склонения существительного
 * @return string Наименование в правильном падеже
 */

function set_format_phrase($value, $indicator) {
    $result = '';

    $indicators = [
        'minute' => ['минута', 'минуты', 'минут'],
        'hour' => ['час', 'часа', 'часов'],
        'rate' => ['ставка', 'ставки', 'ставок'],
        'day' => ['день', 'дня', 'дней']
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

/**
 * Представляет период времени, прошедший с момента добавления ставки в необходимом формате
 *
 * @param string $time Время добавления ставки
 * @return string Отформатированное время добавления ставки
 */

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

/**
 * Получает из базы данных конкретную категорию из таблицы категорий
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param array $category Ассоциативный массив, задающий значения для поиска
 * @return array Массив категорий
 */

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

/**
 * Возвращает либо массив лотов либо количество лотов
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param integer $limit Количество лотов, отображаемое на странице
 * @param string|bool $search Текст запроса для поиска
 * @param integer|bool $category_id id категории лота
 * @param integer|bool $page_id id страницы при осуществлении навигации
 * @param bool $records Параметр, определяющий тип возвращаемого функцией результата (false - массив, true - количество)
 * @return array|integer Массив лотов|количество лотов
 */

function get_lots($link, $limit, $search = false, $category_id = false, $page_id = false, $records = false) {
    $result = [];
    $count = 0;

    $filter_by_category = empty($category_id) ? '' : 'AND c.category_id = ' . $category_id;
    $search_filter = empty($search) ? '' : "AND MATCH (title, description) AGAINST ('" . $search . "')";
    $limit_filter = !empty($limit) ? 'LIMIT ' . $limit : '';
    $offset_filter = !empty($page_id) && !empty($limit) ? 'OFFSET ' . ($page_id - 1) * $limit : '';

    $sql =
        "SELECT lot_id, title, start_price, image, COUNT(r.rate_id) AS rates_count, COALESCE(MAX(r.rate),start_price) AS price, completion_date, c.name AS category
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

/**
 * Возвращает массив данных для блока пагинации
 *
 * @param integer $pages_count Общее количество страниц
 * @param integer $present_page Номер текущей страницы
 * @param array $search_information Массив исходных get-параметров страницы
 * @param integer $max_pages Максимальное возможное количество страниц
 * @return array Двумерный массив данных, каждый элемент которого содержит номер страницы, css-класс каждого элемента списка и текст атрибута href
 */

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

    for ($i = 1; $i <= $max_pages; $i++) {
        $page_number = $i;
        if ($left > $left_min && $right > $right_min) {
            $page_number = $i + $present_page - $middle;
        } else if ($right <= $right_min) {
            $page_number = $i + $pages_count - $max_pages;
        }

        $page_href = '';
        $class = ' pagination-item-active';
        if ($page_number !== $present_page) {
            $search_information['page'] = $page_number;
            $page_href = ' href="?' . http_build_query($search_information) . '"';
            $class = '';
        }
        $pagination_information[$i] = ['page_number' => $page_number, 'class' => $class, 'href' => $page_href];
    }
    $pagination_information[$max_pages + 1] = ['page_number' => 'Вперед', 'class' => ' pagination-item-next', 'href' => $next];
    return $pagination_information;
}

/**
 * Функция получает массив ставок, которые сделал конкретный пользователь
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param int $user_id Идентификатор пользователя
 * @return array Массив ставок конкретного пользователя
 */

function get_user_rates($link, $user_id) {
    $result = [];
    $sql_user_rates =
        "SELECT l.lot_id, l.title AS lot_title, l.completion_date AS lot_completion_date, MAX(rate) AS rate, MAX(r.date) AS date_add, l.winner_id, l.image, l.author_id AS author_id, c.name AS category, u.contact_information AS contacts
            FROM rates r
            JOIN lots l USING (lot_id)
            JOIN categories c USING (category_id)
            JOIN users u ON u.user_id = l.author_id
            WHERE r.user_id = $user_id
            GROUP BY l.lot_id, l.title, l.completion_date, l.image, l.author_id, l.winner_id, c.name, u.contact_information
            ORDER BY date_add DESC";
    if ($query = mysqli_query($link, $sql_user_rates)) {
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    } else {
        die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
    }
    return $result;
}

/**
 * Представляет время до окончания торгов по лоту в удобном формате либо указывает, что торги закончены
 *
 * @param string $time Дата окончания торгов по лоту
 * @return string Время до окончания торгов либо указание на то, что торги закончены
 */

function get_time_of_end_lot ($time) {
    $completion_time = strtotime($time);

    $seconds = $completion_time - time();
    $days = (int) floor($seconds / 86400);
    $hours = (int) floor(($seconds % 86400) / 3600);
    $minutes = (int) floor(($seconds % 3600) / 60);

    $result = date('d.m.Y', $completion_time);

    if ($seconds <= 0) {
        $result = 'Торги окончены';
    } else if ($days === 0) {
        $result = sprintf('%02d:%02d', $hours, $minutes);
    } else if ($days <= 7) {
        $result = sprintf('%d %s', $days, set_format_phrase($days, 'day'));
    }
    return $result;
}

/**
 * Получает из базы данных записи для пользователя, являющегося победителем по конкретным торгам
 *
 * @param mysqli $link Ресурс подключения к базе данных
 * @param array $user Ассоциативный массив, задающий значения для поиска
 * @return array Массив записей из базы данных для пользователя, являющегося победителем торгов
 */

function get_winner($link, $user) {
    $result = [];
    $sql = '';

    if (!empty($user)) {
        $sql = "WHERE " . key($user) . "='" . current($user) . "'";
        $sql_winner = "SELECT * FROM users $sql";
        if ($query = mysqli_query($link, $sql_winner)) {
            $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
        } else {
            die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
        }
    }
    return $result;
}

?>
