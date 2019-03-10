<?php
require_once('vendor/autoload.php');

$lots = [];
$sql_lots = "SELECT lot_id, title, author_id FROM lots
WHERE completion_date <= NOW() AND winner_id IS NULL";

if ($query = mysqli_query($link, $sql_lots)) {
    $lots = mysqli_fetch_all($query, MYSQLI_ASSOC);
} else {
    die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
};

if (!empty($lots)) {
    $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
    $transport->setUsername('keks@phpdemo.ru');
    $transport->setPassword('htmlacademy');

    $mailer = new Swift_Mailer($transport);

    foreach ($lots as $lot) {
        $lot_id = $lot['lot_id'];
        $rates = get_rates($link, $lot_id);
        $winner_id = 0;

        if (!empty($rates)) {
            $winner_id = $rates[0]['user_id'];
        }

        if ($winner_id !== 0) {
            $sql_winner_add = "UPDATE lots SET winner_id = $winner_id WHERE lot_id = $lot_id";
            if (!$query = mysqli_query($link, $sql_winner_add)) {
                die('Произошла ошибка. Пожалуйста, попробуйте еще раз.');
            }

            $winner = get_winner($link, ['user_id' => $winner_id]);

            $message = new Swift_Message('Ваша ставка победила');
            $message->setFrom('keks@phpdemo.ru', 'YetiCave');
            $message->setTo([$winner['email'] => htmlspecialchars($winner['name'])]);

            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
            $name_server = $_SERVER['SERVER_NAME'];

            $email_content = include_template('email.php', ['lot' => $lot, 'winner' => $winner, 'protocol' => $protocol, 'name_server' => $name_server]);

            $message->setBody($email_content, 'text/html');

            $mailer->send($message);
        }
    }
}
