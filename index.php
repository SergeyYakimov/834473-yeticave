<?php

$user = [
    'name' => 'Sergey Yakimov',
    'image' => 'img/user.jpg'
];

$categories = [
    [
        'name' => 'Доски и лыжи',
        'alias' => 'boards'
    ],
    [
        'name' => 'Крепления',
        'alias' => 'attachment'
    ],
    [
        'name' => 'Ботинки',
        'alias' => 'boots'
    ],
    [
        'name' => 'Одежда',
        'alias' => 'clothing'
    ],
    [
        'name' => 'Инструменты',
        'alias' => 'tools'
    ],
    [
        'name' => 'Разное',
        'alias' => 'other'
    ]
];

$ads_list = [
	[
		'name' => '2014 Rossignol District Snowboard',
		'category' => 'Доски и лыжи',
        'price' => 10999,
        'url' => 'img/lot-1.jpg'
	],
	[
		'name' => 'DC Ply Mens 2016/2017 Snowboard',
		'category' => 'Доски и лыжи',
        'price' => 159999,
        'url' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'url' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'url' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charosal',
        'category' => 'Одежда',
        'price' => 7500,
        'url' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'url' => 'img/lot-6.jpg'
    ]
];

require_once('functions.php');
$page_content = include_template('index.php', ['categories' => $categories, 'ads_list' => $ads_list, 'timer' => get_time_till_tomorrow_midnight()]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'name_page' => 'Главная', 'user' => $user]);
print($layout_content);
?>
