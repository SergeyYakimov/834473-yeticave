<?php
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
?>
