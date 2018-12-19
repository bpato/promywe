# Borramos la base de datos si existe para un inicio limpio
DROP SCHEMA IF EXISTS mydatabase;
CREATE SCHEMA mydatabase;

USE mydatabase;
SET AUTOCOMMIT=0;

DROP TABLE IF EXISTS mydatabase.mw_users;
CREATE TABLE mydatabase.mw_users (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    accountType ENUM("0", "1", "2") NOT NULL DEFAULT "2",
	username VARCHAR(25) NOT NULL,
	firstName VARCHAR(50),
	lastName VARCHAR(50),
	email VARCHAR(200) NOT NULL UNIQUE KEY,
	# Al encriptar la contraseña toma 32 caracteres
	password VARCHAR(32) NOT NULL,
	singUpDate DATETIME,
	lastLogin DATETIME,
	profilePic VARCHAR(25)
) ENGINE = INNODB DEFAULT CHARSET=UTF8;

DROP TABLE IF EXISTS mydatabase.mw_pages;
CREATE TABLE mydatabase.mw_pages (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    autor INT,
    date DATETIME,
    tipo ENUM("post","image") NOT NULL,
    titulo VARCHAR(128) NOT NULL DEFAULT "",
    contenido VARCHAR(255)
) ENGINE = INNODB DEFAULT CHARSET=UTF8;

ALTER TABLE mw_pages ADD CONSTRAINT pag_use_FK
    FOREIGN KEY (autor) REFERENCES mw_users(id) ON UPDATE CASCADE ON DELETE CASCADE;

--
-- Volcado de datos para la tabla `mw_users`
--

INSERT INTO `mw_users` (`id`, `accountType`, `username`, `firstName`, `lastName`, `email`, `password`, `singUpDate`, `lastLogin`, `profilePic`) VALUES
(1, '0', 'admin', 'Brais', 'Pato', 'admin@myweb.com', '21232f297a57a5a743894a0e4a801fc3', '2018-12-13 15:32:55', '2018-12-17 17:31:59', NULL),
(2, '1', 'editor', 'Manolo', 'Rodriguez', 'editor@myweb.com', '5aee9dbd2a188839105073571bee1b1f', '2018-12-13 16:15:25', '2018-12-17 16:13:56', NULL),
(3, '2', 'user', 'Maria', 'García', 'user@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(5, '2', 'user', 'Maria', 'García', 'user1@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(6, '2', 'user', 'Maria', 'García', 'user2@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(7, '2', 'user', 'Maria', 'García', 'user3@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(8, '2', 'user', 'Maria', 'García', 'user4@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(9, '2', 'user', 'Maria', 'García', 'user5@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(10, '2', 'user', 'Maria', 'García', 'user6@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(11, '2', 'user', 'Maria', 'García', 'user7@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(12, '2', 'user', 'Maria', 'García', 'user8@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(13, '2', 'user', 'Maria', 'García', 'user9@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(14, '2', 'user', 'Maria', 'García', 'user10@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(15, '2', 'user', 'Maria', 'García', 'user11@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(16, '2', 'user', 'Maria', 'García', 'user12@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(17, '2', 'user', 'Maria', 'García', 'user13@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(18, '2', 'user', 'Maria', 'García', 'user14@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL),
(19, '2', 'user', 'Maria', 'García', 'user15@myweb.com', 'a7da50a6b62f1fd6a5661e17be20d008', '2018-12-14 13:04:47', NULL, NULL);

ALTER TABLE `mw_users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;