<?php
$lots_list = [];

$sql_lots = "SELECT lot_id, title, start_price, image, completion_date, c.name FROM lots
            JOIN categories c USING (category_id)
            WHERE completion_date > NOW()
            ORDER BY creation_date DESC";

$result_lots = mysqli_query($link, $sql_lots);
if ($result_lots) {
    $lots_list = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
}
?>
