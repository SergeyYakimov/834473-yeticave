CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR (128) NOT NULL,
  class VARCHAR (128) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX name (name),
  UNIQUE INDEX class (class)
);

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR (128) NOT NULL,
  name VARCHAR (50) NOT NULL,
  password VARCHAR (64) NOT NULL,
  date_reg DATETIME NOT NULL,
  avatar VARCHAR (64),
  contact_information VARCHAR (255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX email (email)
);

CREATE TABLE lots (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR (128) NOT NULL,
  description VARCHAR (255) NOT NULL,
  image VARCHAR (64) NOT NULL,
  start_price INT UNSIGNED NOT NULL,
  completion_date DATETIME NOT NULL,
  step_rate INT UNSIGNED NOT NULL,
  id_author INT UNSIGNED NOT NULL,
  id_winner INT UNSIGNED,
  id_category TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_category) REFERENCES categories (id),
  FOREIGN KEY (id_author) REFERENCES users (id),
  FOREIGN KEY (id_winner) REFERENCES users (id),
  INDEX name_lot (name),
  INDEX description_lot (description)
);

CREATE TABLE rates (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  rate INT UNSIGNED NOT NULL,
  id_user INT UNSIGNED NOT NULL,
  id_lot INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_user) REFERENCES users (id),
  FOREIGN KEY (id_lot) REFERENCES lots (id)
);
