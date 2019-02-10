-- Добавление в базу данных списка существующих категорий
INSERT INTO categories (name, alias) VALUES
  ('Доски и лыжи', 'boards'),
  ('Крепления', 'attachment'),
  ('Ботинки', 'boots'),
  ('Одежда', 'clothing'),
  ('Инструменты', 'tools'),
  ('Разное', 'other');

-- Добавление в базу данных пользователей
INSERT INTO users (email, name, password, date_reg, avatar, contact_information) VALUES
  ('seemann@yandex.ru',
  'Михаил Артамонов',
  'qwerty12345',
  '2018-01-26 20:14:37',
  NULL,
  'г.Москва, ул. Петровка, д. 24, кв. 55. тел. 8(495)50-99999'),
  ('buildthewall@mail.ru',
  'Donald Trump',
  'shameofthedemocrates',
  '2017-01-20 17:00:00',
  NULL,
  'Donald J. Trump President, Inc, C/O Trump Tower, 725, 5 Avenue, New York, NY 10022. тел. (212) 935-0141');

-- Добавление в базу данных существующего списка объявлений
INSERT INTO lots (creation_date, title, description, image, start_price, completion_date, step_rate, author_id, category_id) VALUES
  ('2019-01-19 14:57:36',
  '2014 Rossignol District Snowboard',
  'Отличная новенькая доска! Эта доска отлично подойдёт как для обычного склона, так и для парка, а также для обучения.',
  'img/lot-1.jpg',
  10999,
  '2019-03-19 00:00:00',
  300,
  1,
  1),
  ('2019-02-07 18:10:10',
  'DC Ply Mens 2016/2017 Snowboard',
  'The board has traditional camber throughout the midsection with longer, traditional contact points, creating a flat, stable platform.',
  'img/lot-2.jpg',
  159999,
  '2019-04-01 00:00:00',
  2000,
  2,
  1),
  ('2019-02-02 10:00:23',
  'Крепления Union Contact Pro 2015 года размер L/XL',
  'These mounts are annually tested for strength by one of the most titled backcountry riders, my friend-Austrian Gigi Rüf.',
  'img/lot-3.jpg',
  8000,
  '2019-03-10 00:00:00',
  200,
  2,
  2),
  ('2019-01-01 04:20:12',
  'Ботинки для сноуборда DC Mutiny Charocal',
  'Отличные ботинки. Проверено в Cанкт-Моритце',
  'img/lot-4.jpg',
  10999,
  '2019-03-05 00:00:00',
  300,
  1,
  3),
  ('2019-01-15 15:50:24',
  'Куртка для сноуборда DC Mutiny Charocal',
  'Топовая куртка для активного отдыха. Выбор профессионалов.',
  'img/lot-5.jpg',
  7500,
  '2019-02-25 00:00:00',
  100,
  1,
  4),
  ('2019-01-30 16:24:55',
  'Маска Oakley Canopy',
  'The mask with polarizing filter. Barak and I think it is one of the best of its kind.',
  'img/lot-6.jpg',
  5400,
  '2019-03-31 00:00:00',
  300,
  2,
  6);

-- Добавление в базу данных ставок
INSERT INTO rates (date, rate, user_id, lot_id) VALUES
  ('2019-02-10 16:24:55', 165000, 1, 2),
  ('2019-02-09 23:48:23', 6600, 1, 6);

-- Получение всех категорий
SELECT * FROM categories;

-- Получение самых новых, открытых лотов
SELECT title, start_price, image, r.rate price, c.name FROM lots
JOIN categories c USING (category_id)
LEFT JOIN rates r USING (lot_id)
WHERE winner_id IS NULL
ORDER BY creation_date DESC
LIMIT 3;

-- Получение лота по его ID
SELECT *, c.name FROM lots
JOIN categories c USING (category_id)
WHERE lot_id = 1;

-- Обновление названия лота по его идентификатору
UPDATE lots SET title = 'Just a super mask' WHERE lot_id = 6;

-- Получение списка самых свежих ставок для лота по его идентификатору
SELECT * FROM rates
WHERE lot_id = 2
ORDER BY date DESC;
