CREATE DATABASE siame DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;

USE siame;

/****** Módulo de Seguridad ******/
-- modulo 
CREATE TABLE permiso(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
descripcion VARCHAR(50) NOT NULL,
baja BOOL NOT NULL DEFAULT FALSE
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- modulo
CREATE TABLE tabla(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(50) NOT NULL,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `modu_unique` (`nombre`)
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- rol 
CREATE TABLE rol(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
descripcion VARCHAR(100) NOT NULL,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `rol_unique` (`descripcion`)
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- rol_permiso_tabla
CREATE TABLE rol_permiso_tabla(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
permiso_id TINYINT(3) UNSIGNED NOT NULL,
tabla_id TINYINT(3) UNSIGNED NOT NULL,
rol_id TINYINT(3) UNSIGNED NOT NULL,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `rpm_unique` (`permiso_id`, `tabla_id`, `rol_id`),
CONSTRAINT fk_rpm_perm FOREIGN KEY(permiso_id) REFERENCES Permiso(id) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT fk_rpm_tabl FOREIGN KEY(tabla_id) REFERENCES tabla(id) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT fk_rpm_rol FOREIGN KEY (rol_id) REFERENCES Rol(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;

/****** Dirección ******/
-- provincia
CREATE TABLE provincia(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(100) NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL COMMENT 'cuando halla creado tabla "Usuario", agregar constraint',
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `prov_unique` (`nombre`, `baja`)
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- departamento
CREATE TABLE departamento(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(100) NOT NULL,
provincia_id TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL COMMENT 'cuando halla creado tabla "Usuario", agregar constraint',
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `dept_unique` (`nombre`,provincia_id,baja),
CONSTRAINT fk_dept_prov FOREIGN KEY (provincia_id) REFERENCES Provincia(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- localidad
CREATE TABLE localidad(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(100) NOT NULL,
departamento_id TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL COMMENT 'cuando halla creado tabla "Usuario", agregar constraint',
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `loca_unique` (`nombre`, departamento_id,baja),
CONSTRAINT fk_loca_dept FOREIGN KEY (departamento_id) REFERENCES Departamento(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- direccion
-- No establezco un índice UNIQUE en esta tabla porque puede que vivan dos personas en la misma dirección
CREATE TABLE direccion(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
calle VARCHAR(100) NOT NULL,
numero SMALLINT(6) NOT NULL,
localidad_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL COMMENT 'cuando halla creado tabla "Usuario", agregar constraint',
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT fk_dire_loca FOREIGN KEY (localidad_id) REFERENCES Localidad(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** usuarios ******/
-- usuario
CREATE TABLE usuario(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
documento INT(11) UNSIGNED NOT NULL,
nombre VARCHAR(75) NOT NULL,
apellido VARCHAR(75) NOT NULL,
fecha_nacimiento DATE NOT NULL,
`user` VARCHAR(15) BINARY NOT NULL COMMENT 'establezco como BINARY para que sea case-sensitive',
pass VARCHAR(128) BINARY NOT NULL COMMENT 'establezco como BINARY para que sea case-sensitive',
email VARCHAR(150) NOT NULL DEFAULT '',
telefono_fijo INT(11) UNSIGNED,
telefono_movil INT(11) UNSIGNED, 
direccion_id INT(11) UNSIGNED NOT NULL,
rol_id TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL COMMENT 'cuando halla creado tabla "Usuario", agregar constraint',
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT fk_usua_dire FOREIGN KEY (direccion_id) REFERENCES direccion(id) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT fk_usua_rol FOREIGN KEY (rol_id) REFERENCES rol(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- bitacora
CREATE TABLE bitacora(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
usuario_id INT(11) UNSIGNED NOT NULL,
descripcion VARCHAR(100) NOT NULL COMMENT 'operacion realizada',
sentencia VARCHAR(255) NOT NULL,
latitud DOUBLE,
longitud DOUBLE,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
ip VARCHAR(32) NOT NULL,
otros VARCHAR(255) COMMENT 'otros no contemplados como dispositivo',
CONSTRAINT fk_bita_usua FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=MYISAM CHARSET utf8 COLLATE utf8_spanish_ci;
/****** Cargo un Usuario(con dirección y rol) que será el usuario 'root' para poder luego establecer las FK 'operadorAlta/Baja' en las tablas ******/
-- modulo de seguridad
INSERT INTO rol(descripcion) VALUES('ADMINISTRADOR'),('BEDEL'),('PROFESOR'),('ALUMNO'),('ADMINISTRATIVO'),('USUARIO SIN AUTENTICAR');
INSERT INTO permiso(id,descripcion) VALUES(1,'CRUD'),(2,'Lectura, Alta y Modificaci&oacute;n'),(3,'Lectura y Alta'),(4,'Lectura y Modificaci&oacute;n'),(5, 'Lectura');
-- direccion 
INSERT INTO provincia(nombre, operador_alta) VALUES('Mendoza',1);
INSERT INTO departamento (nombre,provincia_id,operador_alta) VALUES ('Capital',1,1),('General Alvear',1,1),
('Godoy Cruz',1,1),('Guaymall&eacute;n',1,1),('Jun&iacute;n',1,1),('La Paz',1,1),('La Heras',1,1),('Lavalle',1,1),
('Luj&aacute;n de Cuyo',1,1),('Maip&uacute;',1,1),('Malarg&uuml;e',1,1),('Rivadavia',1,1),('San Carlos',1,1),('San Mart&iacute;n',1,1),
('San Rafael',1,1),('Santa Rosa',1,1),('Tunuy&aacute;n',1,1),('Tupungato',1,1);
/****** Localidades******/
-- capital
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('1&ordf; Secci&oacute;n Parque Central',1,1),
('2&ordf; Secci&oacute;n Barrio C&iacute;vico',1,1),("3&ordf; Secci&oacute;n Parque O&#39;Higgins",1,1),('4&ordf; Secci&oacute;n &Aacute;rea Fundacional',1,1),
('5&ordf; Secci&oacute;n Residencial Sur',1,1),('6&ordf; Secci&oacute;n Residencial Norte',1,1),('7&ordf; Secci&oacute;n Residencial Parque',1,1),
('8&ordf; Secci&oacute;n Aeroparque',1,1),('9&ordf; Secci&oacute;n Parque General San Mart&iacute;n',1,1),('10&ordf; Secci&oacute;n Residencial Los Cerros',1,1)
,('11v Secci&oacute;n San Agust&iacute;n',1,1),('12&ordf; Secci&oacute;n Piedemonte',1,1);
-- general alvear
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Bowen',2,1),('General Alvear',2,1),('San Pedro del Atuel',2,1);
-- godoy cruz
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Gobernador Benegas',3,1),('Godoy Cruz',3,1),('Las Tortugas',3,1),('Presidente Sarmiento',3,1),('San Francisco del Monte',3,1);
-- guaymallén
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Belgrano',4,1),('El Bermejo',4,1),('Buena Nueva',4,1),
('Capilla del Rosario',4,1),('Colonia Molina',4,1),('Colonia Segovia',4,1),('Dorrego',4,1),('El Sauce',4,1),
('Jes&uacute;s Nazareno',4,1),('Kil&oacute;metro 8',4,1),('Kil&oacute;metro 11',4,1),('La Primavera',4,1),('Las Ca&ntilde;as',4,1),('Los Corralitos',4,1),
('Nueva Ciudad',4,1),('Pedro Molina',4,1),('Puente de Hierro',4,1),('Rodeo de la Cruz',4,1),('San Francisco del Monte',4,1),
('San Jos&eacute;',4,1),('Villa Nueva',4,1);
-- junín
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Algarrobo Grande',5,1),('Alto Verde',5,1),
('Ingeniero Giagnoni',5,1),('Jun&iacute;n',5,1),('La Colonia',5,1),('Los Barriales',5,1),('Medrano',5,1),('Mundo Nuevo',5,1),
('Phillips',5,1),('Rodr&iacute;guez Pe&ntilde;a',5,1);
-- la paz
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('La Paz Norte',6,1),('La Paz Sur',6,1),('Desaguadero',6,1),
('Villa Antigua',6,1),('Villa Nueva (Cabecera) de La Paz',6,1);
-- las heras
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Capdevilla',7,1),('El Algarrobal',7,1),
('El Borboll&oacute;n',7,1),('El Challao',7,1),('El Pastal',7,1),('El Plumerillo',7,1),('El Resguardo',7,1),('El Zapallar',7,1),
('La Cieneguita',7,1),('Las Cuevas',7,1),('Las Heras',7,1),('Panquehua',7,1),('Penitentes',7,1),('Sierras de Encalada',7,1),('Uspallata',7,1);
-- lavalle
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Costa de Araujo',8,1),('El Carmen',8,1),
('El Chilcal',8,1),('El Plumero',8,1),('El Vergel',8,1),('Gustavo Andr&eacute;',8,1),('Jocol&iacute;',8,1),('Jocol&iacute; Viejo',8,1),
('La Asunci&Oacute;n',8,1),('La Holanda',8,1),('La Palmera',8,1),('La Pega',8,1),('Las Violetas',8,1),('Lagunas del Rosario',8,1),
('El Paramillo',8,1),('San Francisco',8,1),('San Jos&eacute;',8,1),('San Miguel',8,1),('Tres de Mayo',8,1),('Villa Tulumaya',8,1);
-- luján de cuyo
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Agrelo',9,1),('Cacheuta',9,1),('Carrodilla',9,1),
('Chacras de Coria',9,1),('El Carrizal',9,1),('Industrial',9,1),('La Puntilla',9,1),('Las Compuertas',9,1),
('Luj&aacute;n de Cuyo',9,1),('Mayor Drummond',9,1),('Perdriel',9,1),('Potrerillos',9,1),('Ugarteche',9,1),('Vistalba',9,1);
-- maipú
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Coquimbito',10,1),('Cruz de Piedra',10,1),
('Fray Luis Beltr&aacute;n',10,1),('General Guti&eacute;rrez',10,1),('General Ortega',10,1),('Las Barrancas',10,1),('Lunlunta',10,1),
('Luzuriaga',10,1),('Maip&uacute;',10,1),('Rodeo del Medio',10,1),('Russell',10,1),('San Roque',10,1);
-- Malargüe
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Agua Escondida',11,1),('Malarg&#252;e',11,1),
('R&iacute;o Barrancas',11,1),('R&iacute;o Grande',11,1);
-- rivadavia
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Andrade',12,1),('El Mirador',12,1),
('La Central',12,1),('La Libertad',12,1),('Los &Aacute;rboles',12,1),('Los Campamentos',12,1),('Los Huarpes',12,1),
('Medrano',12,1),('Mundo Nuevo',12,1),('Reducci&oacute;n',12,1),('Rivadavia',12,1),('Santa Mar&iacute;a de Oro',12,1),('San Isidro',12,1);
-- san carlos
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Chilecito',13,1),('Eugenio Bustos',13,1),
('La Consulta',13,1),('Pareditas',13,1),('Tres Esquinas',13,1),('Villa San Carlos',13,1);
-- san martín
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Alto Salvador',14,1),('Alto Verde',14,1),
('Buen Orden',14,1),('Chapanay',14,1),('Chivilcoy',14,1),('El Central',14,1),('El Divisadero',14,1),('El Espino',14,1),
('El Rambl&oacute;n',14,1),('Las Chimbas',14,1),('Montecaseros',14,1),('Nueva California',14,1),('Palmira',14,1),
('San Mart&iacute;n',14,1),('Tres Porte&ntilde;as',14,1);
-- san rafael
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Ca&ntilde;ada Seca',15,1),('Cuadro Benegas',15,1),
('Cuadro Nacional',15,1),('El Cerrito',15,1),('El Sosneado',15,1),('El Nihuil',15,1),('Goudge',15,1),('Jaime Prats',15,1),
('La Llave',15,1),('Las Malvinas',15,1),('Las Paredes',15,1),('Monte Com&aacute;n',15,1),('Punta del Agua',15,1),
('Rama Ca&iacute;da',15,1),('Real del Padre',15,1),('San Rafael',15,1),('Veinticinco de Mayo',15,1),('Villa Atuel',15,1);
-- santa rosa
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Doce de Octubre',16,1),('Las Catitas',16,1),
('La Dormida',16,1),('Santa Rosa',16,1),('&Ntilde;acu&ntilde;&aacute;n',16,1);
-- tunuyán
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Campo de los Andes',17,1),('Colonia Las Rosas',17,1),
('El Algarrobo',17,1),('El Totoral',17,1),('La Primavera',17,1),('Las Pintadas',17,1),('Los &Aacute;rboles',17,1),
('Los Chacayes',17,1),('Los Sauces',17,1),('Tunuy&aacute;n',17,1),('Villa Seca',17,1),('Vista Flores',17,1);
-- tupungato
INSERT INTO localidad(nombre,departamento_id,operador_alta)VALUES ('Anchoris',18,1),('Cord&oacute;n del Plata',18,1),
('El Peral',18,1),('El Zampal',18,1),('El Zampalito',18,1),('Gualtallary',18,1),('La Arboleda',18,1),('La Carrera',18,1),
('San Jos&eacute;',18,1),('Santa Clara',18,1),('Tupungato',18,1),('Villa Bast&iacute;as',18,1),('Zapata',18,1);
INSERT INTO direccion(calle,numero,localidad_id, operador_alta) VALUES('Juvenilia',157,101,1);
-- usuario 'ROOT'
INSERT INTO usuario(documento,nombre,apellido,fecha_nacimiento,email,USER,pass,telefono_fijo,telefono_movil,direccion_id,rol_id,operador_alta) VALUES(11111111,'Usuario de prueba','El Administrador','1996-02-07','ejemplo_admin@ejemplo.net','admin','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413',NULL,NULL,1,1,1);
/****** Modificación de tablas -agrego FK */
-- usuario
	ALTER TABLE `siame`.`usuario`  
	  ADD CONSTRAINT `fk_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
	  ADD CONSTRAINT `fk_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
-- provincia
ALTER TABLE `siame`.`provincia`  
  ADD CONSTRAINT `fk_prov_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_prov_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
-- departamento
ALTER TABLE `siame`.`departamento`  
  ADD CONSTRAINT `fk_depa_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_depa_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
-- localidad
ALTER TABLE `siame`.`localidad`  
  ADD CONSTRAINT `fk_loca_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_loca_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;  
-- direccion
ALTER TABLE `siame`.`direccion`  
  ADD CONSTRAINT `fk_dire_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_dire_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `siame`.`usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
/****** instituto ******/  
-- sede
CREATE TABLE sede(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
responsable VARCHAR(150) NOT NULL,
telefono INT(11) UNSIGNED,
direccion_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `sede_respo_unique` (`responsable`),
UNIQUE INDEX `sede_dire_unique` (direccion_id),
UNIQUE INDEX `sede_tele_unique` (telefono),
CONSTRAINT `fk_sede_dire` FOREIGN KEY (`direccion_id`) REFERENCES `direccion`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_sede_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_sede_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- plan estudio
CREATE TABLE plan_estudio(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'prefiero manejar como PK claves externas al modelo de datos',
numero_resolucion VARCHAR(10) NOT NULL COMMENT 'VARCHAR porque puede contener barras',
nombre_carrera VARCHAR(150) NOT NULL,
nombre_titulo VARCHAR(150) NOT NULL,
modalidad ENUM('PRESENCIAL','A_DISTANCIA') NOT NULL DEFAULT 'PRESENCIAL',
duracion TINYINT(3) UNSIGNED NOT NULL COMMENT 'en años',
condiciones_ingreso TEXT NOT NULL,
articulaciones TEXT NOT NULL,
horas_catedra SMALLINT(4) UNSIGNED NOT NULL,
horas_reloj SMALLINT(4) UNSIGNED NOT NULL,
`path` VARCHAR(255) COMMENT '[OPCIONAL] ruta hacia el archivo que contien la resolucion de la carrera',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `plan_unique` (numero_resolucion),
CONSTRAINT `fk_plan_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_plan_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- profesor 
CREATE TABLE profesor(
legajo INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
estado ENUM('ACTIVO','INACTIVO','DE_LICENCIA','DE_VACIONES') NOT NULL DEFAULT 'ACTIVO' COMMENT 'VER esto',
usuario_id INT(11) UNSIGNED NOT NULL COMMENT 'con esto intento representar la relacion de herencia (especialización) con "usuario"',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_prof_usua` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_prof_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_prof_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- carrera
CREATE TABLE carrera(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
observaciones VARCHAR(255),
plan_estudio_id TINYINT(3) UNSIGNED NOT NULL,
coordinador INT(11) UNSIGNED NOT NULL COMMENT 'aquí iría en realidad PROFESOR_ID',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `carr_unique` (plan_estudio_id),
CONSTRAINT `fk_carr_plan` FOREIGN KEY (`plan_estudio_id`) REFERENCES `plan_estudio`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_carr_prof` FOREIGN KEY (`coordinador`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_carr_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_carr_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- carrera_sede
CREATE TABLE carrera_sede(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
carrera_id TINYINT(3) UNSIGNED NOT NULL,
sede_id TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `case_unique` (carrera_id,sede_id,baja),
CONSTRAINT `fk_case_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_case_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_case_carr` FOREIGN KEY (`carrera_id`) REFERENCES `carrera`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_case_sede` FOREIGN KEY (`sede_id`) REFERENCES `sede`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- establecimiento
CREATE TABLE establecimiento(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'por las dudas, aunque no creo que carguen más de 255 establecimientos',
nombre VARCHAR(100) NOT NULL,
tipo_establecimiento ENUM('UNIVERSIDAD','TERCEARIO') NOT NULL DEFAULT 'UNIVERSIDAD',
direccion_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `esta_unique` (nombre,direccion_id,baja),
CONSTRAINT `fk_esta_dire` FOREIGN KEY (`direccion_id`) REFERENCES `direccion`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_esta_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_esta_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- titulo
CREATE TABLE titulo(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(100) NOT NULL,
tipo ENUM('SUPERIOR','UNIVERSITARIO','LICENCIATURA','MAESTRIA','DOCTORADO','POS-GRADO') NOT NULL DEFAULT 'UNIVERSITARIO' COMMENT 'con POS-GRADO pretendo abarcar los "cursos de especialización" -o como se llamen',
duracion TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `titu_unique` (nombre,tipo,baja),
CONSTRAINT `fk_titu_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_titu_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- titulo_establecimiento
CREATE TABLE titulo_establecimiento(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
titulo_id INT(11) UNSIGNED NOT NULL,
establecimiento_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `ties_unique` (titulo_id,establecimiento_id,baja),
CONSTRAINT `fk_ties_titu` FOREIGN KEY (`titulo_id`) REFERENCES `titulo`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_ties_esta` FOREIGN KEY (`establecimiento_id`) REFERENCES `establecimiento`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_ties_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_ties_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- profesor_titulo_establecimiento 
CREATE TABLE `profesor_titulo_establecimiento` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor_id` INT(11) UNSIGNED NOT NULL,
  `titulo_establecimiento_id` INT(11) UNSIGNED NOT NULL,
  `fecha_alta` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `operador_alta` INT(11) UNSIGNED NOT NULL,
  `fecha_modificacion` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `operador_modificacion` INT(11) UNSIGNED NULL DEFAULT NULL,
  baja BOOL NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `prti_unique` (`profesor_id`, `titulo_establecimiento_id`,baja),
  CONSTRAINT `fk_prties_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_prties_ties` FOREIGN KEY (`titulo_establecimiento_id`) REFERENCES `titulo_establecimiento`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_prties_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_prties_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_spanish_ci; 
-- tipo_registro_inasistencia
/** 
* creo una tabla -en vez de etablecer como un campo enumerado en la tabla "registro_inasistencia"- 
* porque pueden agregarse nuevas causas (o eliminarse)
*/
CREATE TABLE tipo_registro_inasistencia(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(50)NOT NULL COMMENT 'ej, "Resolucion XXX", "Causa Personal", etc',
descripcion VARCHAR(255) COMMENT 'ej, "presento certificado médico trucho", "Cualquier cosa que se lo ocurra", etc', 
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `prti_unique` (nombre,descripcion,baja),
CONSTRAINT `fk_tirein_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_tirein_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- registro_asistencia
CREATE TABLE registro_inasistencia(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
profesor_id INT(11) UNSIGNED NOT NULL,
fecha DATE NOT NULL COMMENT 'fecha del registro (no es lo mismo que "fecha_alta")',
tipo_registro_inasistencia_id INT(11) UNSIGNED NOT NULL COMMENT 'perdón por el nombre XD', 
justificado BOOLEAN NOT NULL DEFAULT FALSE,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `rein_unique` (profesor_id,fecha,baja),
CONSTRAINT `fk_rein_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_rein_tirein` FOREIGN KEY (`tipo_registro_inasistencia_id`) REFERENCES `tipo_registro_inasistencia`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_rein_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_rein_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- sanciones
CREATE TABLE sancion(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
descripcion TEXT NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_sanc_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_sanc_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- registro_sancion
CREATE TABLE registro_sancion(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
fecha DATE NOT NULL COMMENT 'fecha del registro (no es lo mismo que "fecha_alta")',
horas TINYINT(3) UNSIGNED NOT NULL,
sancion_id INT(11) UNSIGNED NOT NULL,
profesor_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `resa_unique` (sancion_id,profesor_id,baja),
CONSTRAINT `fk_resa_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_resa_sanc` FOREIGN KEY (`sancion_id`) REFERENCES `sancion`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_resa_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_resa_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- cargo
CREATE TABLE cargo(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(50) NOT NULL,
descripcion VARCHAR(255) NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `carg_unique` (nombre,descripcion,baja),
CONSTRAINT `fk_carg_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_carg_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- concurso
CREATE TABLE concurso(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
fecha_toma_efectiva DATE NOT NULL,
horas TINYINT(3) UNSIGNED NOT NULL,
tipo ENUM('ABIERTO','CERRADO') NOT NULL DEFAULT 'ABIERTO',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_conc_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_conc_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- concurso_cargo
CREATE TABLE concurso_cargo(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
concurso_id INT(11) UNSIGNED NOT NULL,
cargo_id INT(11) UNSIGNED NOT NULL,
condicion_entrante ENUM('TITULAR','SUPLENTE','SUPLENTE EN CARGO VACANTE') NOT NULL DEFAULT 'TITULAR' COMMENT 'profesor entrante',
condicion_saliente ENUM('JUBILACION','BAJA','LICENCIA','VACACIONES','OTRO') NOT NULL DEFAULT 'BAJA'  COMMENT 'profesor saliente',
descripcion VARCHAR(255) COMMENT 'cualquier caso "no contemplado"',
fecha_alta_cargo DATE NOT NULL,
fecha_baja_cargo DATE,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `coca_unique` (concurso_id,cargo_id,baja),
CONSTRAINT `fk_coca_conc` FOREIGN KEY (`concurso_id`) REFERENCES `concurso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_coca_carg` FOREIGN KEY (`cargo_id`) REFERENCES `cargo`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_coca_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_coca_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- profesor_concurso
CREATE TABLE profesor_concurso_cargo(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
profesor_id INT(11) UNSIGNED NOT NULL,
concurso_cargo_id INT(11) UNSIGNED NOT NULL,
puntaje FLOAT(5,2) UNSIGNED NOT NULL,
cargo_tomado BOOLEAN NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `prco_unique` (profesor_id,concurso_cargo_id,baja),
CONSTRAINT `fk_prco_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_prco_conca` FOREIGN KEY (`concurso_cargo_id`) REFERENCES `concurso_cargo`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_prco_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_prco_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- jurado
CREATE TABLE jurado(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
profesor_id INT(11) UNSIGNED NOT NULL,
concurso_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_jura_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_jura_conc` FOREIGN KEY (`concurso_id`) REFERENCES `concurso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_jura_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_jura_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** materias ******/
-- materia drop database siame
CREATE TABLE materia(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(100) NOT NULL,
carga_horaria_semanal TINYINT(3) UNSIGNED NOT NULL,
carga_horaria_total TINYINT(3) UNSIGNED NOT NULL,
formato ENUM('LABORATORIO','MODULO','TALLER','MODULO/TALLER','MODULO/LABORATORIO','ASIG','SEM','') DEFAULT '' NOT NULL COMMENT 'no tengo idea lo que significa ASIG o SEM, pero así figura en la resolución',
duracion ENUM('ANUAL','1° CUATRIMESTRE','2° CUATRIMESTRE') DEFAULT 'ANUAL',
anio TINYINT(3) UNSIGNED NOT NULL COMMENT 'año al que pertenece, ocea de 1-3',
acreditable BOOLEAN NOT NULL DEFAULT FALSE,
promocional BOOLEAN NOT NULL DEFAULT FALSE,
libre BOOLEAN NOT NULL DEFAULT FALSE,
presencial VARCHAR(15) NOT NULL COMMENT 'varchar porque alguna materia puede ser 6P-2NP, habrá que darle una vuelta más de rosca al programarlo...',
plan_estudio_id TINYINT(3) UNSIGNED NOT NULL,
descipcion TEXT COMMENT 'Acá iría los temas que abarca como también la bibliografía requerida/recomendada',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `mate_unique` (nombre,plan_estudio_id,baja),
CONSTRAINT `fk_mate_plan` FOREIGN KEY (`plan_estudio_id`) REFERENCES `plan_estudio`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mate_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mate_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- materia_correlatividad
CREATE TABLE materia_correlatividad(
materia_id INT(11) UNSIGNED NOT NULL,
materia_correlativa_id INT(11) UNSIGNED NOT NULL COMMENT 'se que es largo el nombre, pero transmite mi intención',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
PRIMARY KEY(materia_id,materia_correlativa_id,baja),
CONSTRAINT `fk_maco_mate1` FOREIGN KEY (`materia_id`) REFERENCES `materia`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_maco_mate2` FOREIGN KEY (`materia_correlativa_id`) REFERENCES `materia`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_maco_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_maco_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
-- mesa 
CREATE TABLE mesa(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
apertura_inscripciones DATE NOT NULL,
cierre_inscripciones DATE NOT NULL,
tipo ENUM('ABIERTA','ESPECIAL') NOT NULL DEFAULT 'ABIERTA',
carrera_sede_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `mesa_unique` (apertura_inscripciones,cierre_inscripciones),
CONSTRAINT `fk_mesa_case` FOREIGN KEY (`carrera_sede_id`) REFERENCES `carrera_sede`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mesa_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mesa_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** administrador ******/
CREATE TABLE administrador(
legajo INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
usuario_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED, 
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_admin_usua` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_admin_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_admin_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****Administrativo****/
CREATE TABLE administrativo(
legajo INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
usuario_id INT(11) UNSIGNED NOT NULL,
carrera_sede_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED, 
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_adminis_usua` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_adminis_case` FOREIGN KEY (`carrera_sede_id`) REFERENCES `carrera_sede`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_adminis_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_adminis_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** bedel ******/
CREATE TABLE bedel(
legajo INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
carrera_sede_id INT(11) UNSIGNED NOT NULL,
usuario_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_bede_usua` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_bede_case` FOREIGN KEY (`carrera_sede_id`) REFERENCES `carrera_sede`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_bede_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_bede_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** alumno ******/
CREATE TABLE alumno(
legajo INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
anio_matricula SMALLINT(5) UNSIGNED NOT NULL,
estado ENUM('ACTIVO','INACTIVO','EGRESADO') NOT NULL DEFAULT 'ACTIVO',
usuario_id INT(11) UNSIGNED NOT NULL,
mesa_castigo BOOLEAN NOT NULL DEFAULT FALSE,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
CONSTRAINT `fk_alum_usua` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_alum_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_alum_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** matria_mesa******/
CREATE TABLE materia_mesa(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
materia_id INT(11) UNSIGNED NOT NULL,
mesa_id INT(11) UNSIGNED NOT NULL,
profesor_id INT(11) UNSIGNED NOT NULL,
observaciones VARCHAR(255) DEFAULT NULL COMMENT 'aquí irán comentarios para casos como "prof. X, para el curso Y tomó la mesael dia dd/mm/aaaa"',
fecha_examen DATE NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `mame_unique` (materia_id,profesor_id,fecha_examen),
CONSTRAINT `fk_mame_mate` FOREIGN KEY (`materia_id`) REFERENCES `materia`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mame_mesa` FOREIGN KEY (`mesa_id`) REFERENCES `mesa`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mame_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mame_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_mame_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** curso******/
CREATE TABLE curso(
id TINYINT(3) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
anio TINYINT(1) UNSIGNED NOT NULL,
division CHAR(1) NOT NULL,
carrera_sede_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `curs_unique` (anio,division,carrera_sede_id),
CONSTRAINT `fk_curs_case` FOREIGN KEY (`carrera_sede_id`) REFERENCES `carrera_sede`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_curs_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_curs_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** materia_curso ******/
CREATE TABLE materia_curso(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
materia_id INT(11) UNSIGNED NOT NULL,
curso_id TINYINT(3) UNSIGNED NOT NULL,
profesor_id INT(11) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `macu_unique` (materia_id,curso_id),
CONSTRAINT `fk_macu_mate` FOREIGN KEY (`materia_id`) REFERENCES `materia`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_macu_curs` FOREIGN KEY (`curso_id`) REFERENCES `curso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_macu_prof` FOREIGN KEY (`profesor_id`) REFERENCES `profesor`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_macu_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_macu_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** alumno_curso ******/
/**
* Los registros en alumno_curso no se actualizarían (a no ser que se uno se cambie de sede, por ende cambiaría de curso por lo que debería actulizar sólo en ese caso)
*/
CREATE TABLE alumno_curso(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
alumno_id INT(11) UNSIGNED NOT NULL,
curso_id TINYINT(3) UNSIGNED NOT NULL,
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `alcu_unique` (alumno_id,curso_id),
CONSTRAINT `fk_alcu_curs` FOREIGN KEY (`curso_id`) REFERENCES `curso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_alcu_alum` FOREIGN KEY (`alumno_id`) REFERENCES `alumno`(`legajo`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_alcu_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_alcu_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** calificacion ******/
CREATE TABLE calificacion(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
materia_mesa_id INT(11) UNSIGNED COMMENT 'puede ser nulo, ya que puede ser aprobada por equivalencia',
alumno_curso_id INT(11) UNSIGNED NOT NULL,
nota FLOAT(5,2) UNSIGNED NOT NULL COMMENT 'establezco para 5,2 porque pueden poner una nota como 100,00 o 90,5 o simplemente 4',
estado ENUM('','REGULAR','PROMOCIONADO','EQUIVALENCIA') NOT NULL DEFAULT '',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `cali_unique` (materia_mesa_id,alumno_curso_id),
CONSTRAINT `fk_cali_mame` FOREIGN KEY (`materia_mesa_id`) REFERENCES `materia_mesa`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_cali_alcu` FOREIGN KEY (`alumno_curso_id`) REFERENCES `alumno_curso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_cali_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_cali_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/****** inscripcion_mesa ******/
CREATE TABLE inscripcion_materia_mesa(
id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
alumno_curso_id INT(11) UNSIGNED NOT NULL,
materia_mesa_id INT(11) UNSIGNED NOT NULL,
estado ENUM('INSCRIPTO','DE_BAJA') NOT NULL DEFAULT 'INSCRIPTO',
fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
operador_alta INT(11) UNSIGNED NOT NULL,
fecha_modificacion TIMESTAMP,
operador_modificacion INT(11) UNSIGNED,
baja BOOL NOT NULL DEFAULT FALSE,
UNIQUE INDEX `cali_unique` (alumno_curso_id,materia_mesa_id),
CONSTRAINT `fk_inme_mame` FOREIGN KEY (`materia_mesa_id`) REFERENCES `materia_mesa`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_inme_alcu` FOREIGN KEY (`alumno_curso_id`) REFERENCES `alumno_curso`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_inme_usua_opal` FOREIGN KEY (`operador_alta`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT `fk_inme_usua_opmo` FOREIGN KEY (`operador_modificacion`) REFERENCES `usuario`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB CHARSET utf8 COLLATE utf8_spanish_ci;
/*==Stored Procedures para consultar correlatividades NO TENGO PRIVILEGIOS PARA CREARLOS Y EJECUTARLOS-FUCK==*/
-- devuelve un error "cerca de DELIMITER en la línea 1", lo crea igual, creo que es porque debería estar en un archivo aparte
DELIMITER$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_correlatividades`(mat_req INT(11))SQL SECURITY INVOKER

   BEGIN

      DECLARE continuar TINYINT DEFAULT 0;
      DECLARE id_mat INT(11);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
      DECLARE nombre_mat VARCHAR(50);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
      DECLARE correlatividades_cursor CURSOR FOR  SELECT m_cor.id, m_cor.nombre                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                                  FROM materia AS m                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                                                  INNER JOIN materia_correlatividad AS cor ON cor.`materia_id` = m.id                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                                                  INNER JOIN materia AS m_cor ON cor.materia_correlativa_id = m_cor.id                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                                                  WHERE m.id = mat_req;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                                                  
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET continuar = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
      OPEN correlatividades_cursor;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
      recorrer_correlatividades:LOOP                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
         FETCH correlatividades_cursor INTO id_mat,nombre_mat;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
         IF continuar=1 THEN                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
            LEAVE recorrer_correlatividades;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
         END IF;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
         INSERT INTO materia_correlatividad_temp(id,nombre) VALUES(id_mat,nombre_mat);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
         CALL sp_obtener_correlatividades(id_mat);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
      END LOOP recorrer_correlatividades;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
      CLOSE correlatividades_cursor;	                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
END$$

/*--SP para mostrar las correlatividades consultadas--*/
DELIMITER$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mostrar_correlatividades`(mat INT(11))SQL SECURITY INVOKER                                                                                                                                                                                                                                                                                                        

   BEGIN                               
   
       CREATE TEMPORARY TABLE IF NOT EXISTS materia_correlatividad_temp(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
         id INT(11) UNSIGNED NOT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
         nombre VARCHAR(50) NOT NULL                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
        );    
        -- establezco el nivel máximo de recursión
       SET max_sp_recursion_depth = 20;	   
       -- limpio latabla (por si ya existía)
       TRUNCATE materia_correlatividad_temp;
       CALL sp_obtener_correlatividades(mat);
       SELECT DISTINCT * FROM materia_correlatividad_temp;
	   
   END$$  
   
