DROP DATABASE IF EXISTS Whatsapp;

CREATE DATABASE IF NOT EXISTS Whatsapp;
CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

USE Whatsapp;

CREATE TABLE Usuarios(
  usuario VARCHAR(50) NOT NULL PRIMARY KEY,
  password VARCHAR(50) NOT NULL,
  admin BOOLEAN NOT NULL,
  maestro BOOLEAN NOT NULL
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE Agentes(
  id INT(50) PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  apellido VARCHAR(50) NOT NULL,
  documento VARCHAR(50) NOT NULL,
  telefono VARCHAR(50) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  cargo VARCHAR(50) NOT NULL,
  creador VARCHAR(50) NOT NULL,
  usuario VARCHAR(50) NOT NULL,
  INDEX(usuario),
  FOREIGN KEY Agentes(usuario) REFERENCES Usuarios(usuario)
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE TokenChatApi(
  idToken INT PRIMARY KEY AUTO_INCREMENT,
  Instance VARCHAR(100) NOT NULL,
  Token VARCHAR(100) NOT NULL,
  user VARCHAR(55) NOT NULL
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE dialogs(
  id VARCHAR(25) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  image TEXT NOT NULL,
  last_time VARCHAR(25) NOT NULL,
  abierto BOOLEAN DEFAULT FALSE,
  seguimiento BOOLEAN DEFAULT FALSE,
  Asignador VARCHAR(255) DEFAULT NULL,
  idAgentes INT(50) NULL,
  INDEX(idAgentes),
  FOREIGN KEY dialogs(idAgentes) REFERENCES Agentes(id)
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE messages(
  id VARCHAR(255) PRIMARY KEY,
  body TEXT NULL,
  fromMe BOOLEAN NULL,
  self INT NULL,
  isForwarded INT NULL,
  author VARCHAR(100) NULL,
  time INT NOT NULL,
  chatId VARCHAR(100) NULL,
  messageNumber INT NULL,
  type VARCHAR(55) NULL,
  senderName VARCHAR(255),
  quotedMsgBody TEXT NULL,
  quotedMsgId TEXT NULL,
  quotedMsgType TEXT NULL,
  metadata TEXT NULL,
  ack TEXT NULL,
  chatName VARCHAR(255) NULL,
  FechaHora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  sender VARCHAR(255) NULL
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ENGINE=InnoDB;

CREATE TABLE MensajeDespedida(
  id INT AUTO_INCREMENT PRIMARY KEY,
  cuerpo VARCHAR(255) NOT NULL,
  usuario VARCHAR(55) NOT NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;








use Whatsapp;
DROP PROCEDURE IF EXISTS SP_ReadDialogsByName;

DELIMITER $$
CREATE PROCEDURE SP_ReadDialogsByName(IN v_dato VARCHAR(255), IN v_user VARCHAR(255))
BEGIN
DECLARE v_id INT;
SET v_id = (SELECT id FROM Agentes WHERE usuario = v_user);

SELECT * FROM dialogs WHERE idAgentes = v_id AND (name LIKE CONCAT(v_dato,'%'));

END $$









DROP PROCEDURE IF EXISTS SP_CreateDialogsAdmin;
DELIMITER $$
CREATE PROCEDURE SP_CreateDialogsAdmin(IN v_id VARCHAR(255), IN v_name VARCHAR(255), IN v_image VARCHAR(255), IN v_last_time VARCHAR(255), IN v_user VARCHAR(255))
BEGIN
DECLARE v_idAgente INT;

SET v_idAgente = (SELECT id FROM Agentes WHERE usuario = v_user);

INSERT INTO dialogs(id, name, image, last_time, idAgentes) VALUES(v_id, v_name, v_image, v_last_time, v_idAgente);

END $$







DROP PROCEDURE IF EXISTS SP_FiltrarSalaAsignadosAdmin;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaAsignadosAdmin(IN v_dato VARCHAR(255), IN v_usuario VARCHAR(255))
BEGIN

DECLARE v_id INT;
SET v_id = (SELECT id FROM Agentes WHERE usuario = v_usuario);


    SELECT * FROM dialogs WHERE idAgentes = v_id AND (id LIKE CONCAT(v_dato, '%')  OR name LIKE CONCAT(v_dato, '%'));
END $$






DROP PROCEDURE IF EXISTS SP_FiltrarSalaCerradosAdmin;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaCerradosAdmin(IN v_datos VARCHAR(255), IN v_usuario VARCHAR(255))
BEGIN 
    DECLARE v_id INT;
    SET v_id = (SELECT id FROM Agentes WHERE usuario = v_usuario);

    SELECT * FROM dialogs WHERE abierto = 0 AND (idAgentes = v_id OR Asignador = v_id) AND (name LIKE CONCAT(v_datos, '%') OR id LIKE CONCAT(v_datos, '%'));
END$$








DROP PROCEDURE IF EXISTS SP_FiltrarSalaAdmin;

DELIMITER //
CREATE PROCEDURE SP_FiltrarSalaAdmin(IN v_datos VARCHAR(255), IN v_user VARCHAR(255))
BEGIN
DECLARE v_id INT;
SET v_id = (SELECT id FROM Agentes WHERE usuario = v_user);

SELECT * FROM dialogs WHERE (id LIKE concat(v_datos,'%') OR name LIKE concat(v_datos,'%')) AND idAgentes = v_id;

END //










DROP PROCEDURE IF EXISTS SP_FiltrarSalaAbiertosAdmin;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaAbiertosAdmin(IN v_datos VARCHAR(255), IN v_usuario VARCHAR(255))
BEGIN 

DECLARE v_id INT;
SET v_id = (SELECT id FROM Agentes WHERE usuario = v_usuario);

    SELECT * FROM dialogs WHERE abierto = 1 AND (idAgentes = v_id OR Asignador = v_id) AND (name LIKE concat(v_datos, '%')  OR id LIKE concat(v_datos, '%'));
END $$










DROP PROCEDURE IF EXISTS SP_ReadDialogs;
DELIMITER //

CREATE PROCEDURE SP_ReadDialogs(IN v_user VARCHAR(255))
BEGIN

DECLARE v_id INT;
SET v_id = (SELECT id FROM Agentes WHERE usuario = v_user);

SELECT * FROM dialogs  WHERE idAgentes = v_id OR Asignador = v_user;

END //













DROP PROCEDURE IF EXISTS SP_FiltrarSalaAsignados;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaAsignados(IN v_dato VARCHAR(255))
BEGIN
    SELECT * FROM dialogs WHERE idAgentes <> '' AND (id LIKE CONCAT(v_dato, '%')  OR name LIKE CONCAT(v_dato, '%'));
END $$







DROP PROCEDURE IF EXISTS SP_FiltrarSalaCerrados;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaCerrados(IN v_datos VARCHAR(255))
BEGIN 
    SELECT * FROM dialogs WHERE abierto = 0 AND (name LIKE CONCAT(v_datos, '%') OR id LIKE CONCAT(v_datos, '%'));
END$$




DROP PROCEDURE IF EXISTS SP_FiltrarSalaAbiertos;

DELIMITER $$
CREATE PROCEDURE SP_FiltrarSalaAbiertos(IN v_datos VARCHAR(255))
BEGIN 

    SELECT * FROM dialogs WHERE abierto = 1 AND (name LIKE concat(v_datos, '%')  || id LIKE concat(v_datos, '%'));
END $$





DROP PROCEDURE IF EXISTS SP_FiltrarSala;

DELIMITER // 
CREATE PROCEDURE SP_FiltrarSala(IN v_datos VARCHAR(255))

BEGIN

SELECT * FROM dialogs WHERE id LIKE concat(v_datos, '%') OR name LIKE concat(v_datos, '%');

END // 






DELIMITER //
CREATE PROCEDURE SP_MostrarDialogsTotales()
BEGIN
    SELECT * FROM dialogs;
END //





DROP Procedure IF EXISTS SP_ReadMensajeDespedidaCreador;

DELIMITER // 
CREATE Procedure SP_ReadMensajeDespedidaCreador(IN v_usuario VARCHAR(255)) BEGIN DECLARE v_asistant VARCHAR(255);

SET v_asistant = (SELECT creador FROM Agentes WHERE usuario = v_usuario);

SELECT * FROM MensajeDespedida WHERE usuario = v_asistant ORDER BY id DESC LIMIT 1;

END // 






DROP Procedure IF EXISTS SP_ConteoChatAsignadosAdmin;

DELIMITER // 
CREATE Procedure SP_ConteoChatAsignadosAdmin(IN v_user VARCHAR(55)) 
BEGIN 
DECLARE v_id INT;
DECLARE v_conteo INT;

SET v_id = (SELECT id FROM Agentes WHERE usuario = v_user);

SET v_conteo = (SELECT COUNT(idAgentes) FROM dialogs WHERE idAgentes = v_id);

SELECT v_conteo;
END // 






DROP PROCEDURE IF EXISTS SP_ConteoChatAbiertosAdmin;
DELIMITER // 
CREATE PROCEDURE SP_ConteoChatAbiertosAdmin(in v_usuario VARCHAR(255)) 
BEGIN 
DECLARE v_id INT;
DECLARE v_conteo INT;
SET v_id = ( SELECT id FROM Agentes WHERE usuario = v_usuario);

SET v_conteo = (SELECT count(abierto) FROM dialogs WHERE idAgentes = v_id AND abierto = TRUE);
select v_conteo;
END // 








DROP PROCEDURE IF EXISTS SP_AlmacenarMensajes;

delimiter // 
CREATE PROCEDURE SP_AlmacenarMensajes (in v_id VARCHAR(255), in v_body TEXT, in v_fromMe BOOLEAN, in v_self INT, in v_isForwarded INT, in v_author VARCHAR(100), in v_time INT, in v_chatId VARCHAR(100), in v_messageNumber INT, in v_type VARCHAR(55), in v_senderName VARCHAR(255), in v_quotedMsgBody TEXT, in v_quotedMsgId TEXT, in v_quotedMsgType TEXT, in v_metadata TEXT, in v_ack TEXT, in v_chatName VARCHAR(255), in v_sender VARCHAR(255)) 
BEGIN

INSERT INTO messages(id, body, fromMe, self, isForwarded, author, time, chatId, messageNumber, type, senderName, quotedMsgBody, quotedMsgId, quotedMsgType, metadata, ack, chatName, sender) VALUES( v_id, v_body, v_fromMe, v_self, v_isForwarded, v_author, v_time, v_chatId, v_messageNumber, v_type, v_senderName, v_quotedMsgBody, v_quotedMsgId, v_quotedMsgType, v_metadata, v_ack, v_chatName, v_sender);
END // 







DROP PROCEDURE IF EXISTS SP_ConteoChatAgente;

delimiter //
CREATE PROCEDURE SP_ConteoChatAgente(in v_usuario VARCHAR(255)) 
BEGIN 
DECLARE v_id INT;
DECLARE v_cantidadChatAbiertos INT;
DECLARE v_cantidadChatPendiente INT;

SET v_id = (SELECT id FROM Agentes WHERE usuario = v_usuario);
SET v_cantidadChatAbiertos = (SELECT count(abierto) FROM dialogs WHERe idAgentes = v_id AND abierto = 1);
SET v_cantidadChatPendiente = (SELECT count(abierto) FROM dialogs WHERE idAgentes = v_id AND abierto = 0);

SELECT v_cantidadChatAbiertos, v_cantidadChatPendiente;

END //








DROP PROCEDURE IF EXISTS SP_ConteoChatAsignados;

delimiter $$ 
CREATE PROCEDURE SP_ConteoChatAsignados() 
BEGIN 
DECLARE v_conteo INT;

SET v_conteo = (SELECT COUNT(idAgentes) FROM dialogs);

SELECT v_conteo; 
END $$ 








DROP PROCEDURE IF EXISTS SP_MostrarMensajesChat;

delimiter // 
CREATE PROCEDURE SP_MostrarMensajesChat(in v_id TEXT) 

BEGIN
SELECT * FROM messages WHERE id like concat('%', v_id, '%') ORDER BY messageNumber DESC;
END // 








DROP PROCEDURE IF EXISTS SP_MostrarConversacionAgente;

delimiter // 
CREATE PROCEDURE SP_MostrarConversacionAgente(in v_id TEXT) 
BEGIN 
DECLARE v_idDialogs TEXT;

SET v_idDialogs = (SELECT id FROM dialogs WHERE idAgentes = v_id);

SELECT * FROM messages WHERE chatId = v_idDialogs ORDER BY messageNumber DESC;
END // 










DROP PROCEDURE IF EXISTS SP_CreateDialogs;

DELIMITER // 
CREATE PROCEDURE SP_CreateDialogs(IN v_user VARCHAR(255)) 
BEGIN 
DECLARE v_id INT;

SET v_id = (SELECT id FROM Agentes WHERE usuario = v_user);
SELECT * FROM dialogs WHERE idAgentes = v_id OR Asignador = v_user;
END // 







DROP PROCEDURE IF EXISTS SP_ConteoChatCerrados;

DELIMITER // 
CREATE PROCEDURE SP_ConteoChatCerrados() 
BEGIN 
DECLARE v_conteo INT;

SET v_conteo = (SELECT COUNT(abierto) FROM dialogs WHERE abierto = 0);

SELECT v_conteo;

END //  








DROP Procedure IF EXISTS SP_ReadAccesWebToken;

DELIMITER // 
CREATE Procedure SP_ReadAccesWebToken(IN v_user VARCHAR(55)) 
BEGIN 
DECLARE v_creador VARCHAR(255);

SET v_creador = (SELECT creador FROM Agentes WHERE usuario = v_user);

SELECT * FROM TokenChatApi WHERE user = v_creador ORDER BY idToken DESC Limit 1;

END //







INSERT INTO
  Usuarios(usuario, password, admin, maestro)
VALUES
  (
    'master',
    '202cb962ac59075b964b07152d234b70',
    TRUE,
    TRUE
  );

INSERT INTO
  Agentes(
    nombre,
    apellido,
    documento,
    telefono,
    direccion,
    correo,
    creador,
    usuario
  )
VALUES
  (
    'Cristian',
    'Aguirre Cataño',
    '1035391050',
    '3166857000',
    'carrera 33 47 35',
    'dextter1913@gmail.com',
    'master',
    'master'
  );

INSERT INTO
  Usuarios(usuario, password, admin, maestro)
VALUES
  (
    'admin',
    '202cb962ac59075b964b07152d234b70',
    TRUE,
    FALSE
  );

INSERT INTO
  Agentes(
    nombre,
    apellido,
    documento,
    telefono,
    direccion,
    correo,
    creador,
    usuario
  )
VALUES
  (
    'Carlos',
    'Monsalve Builes',
    '105874596',
    '3134558574',
    'carrera 22 52 35',
    'carlithos634@gmail.com',
    'master',
    'admin'
  );

INSERT INTO
  Usuarios(usuario, password, admin, maestro)
VALUES
  (
    'regular',
    '202cb962ac59075b964b07152d234b70',
    FALSE,
    FALSE
  );

INSERT INTO
  Agentes(
    nombre,
    apellido,
    documento,
    telefono,
    direccion,
    correo,
    creador,
    usuario
  )
VALUES
  (
    'Maria',
    'Isabel Beltran',
    '105896354',
    '3167459321',
    'Avenida 7 15 30',
    'MariaBe1994@gmail.com',
    'admin',
    'regular'
  );