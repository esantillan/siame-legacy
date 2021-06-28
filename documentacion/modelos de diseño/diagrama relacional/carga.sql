insert into tabla(nombre) values
-- direccion
('direccion'),
('localidad'),
('departamento'),
('provincia'),
-- usuarios
('usuario'),
('administrador'),
('administrativo'),
('alumno'),
('bedel'),
('profesor'),
-- institucional
('carrera'),
('curso'),
('carrera_sede'),
('materia'),
('materia_correlatividad'),
('materia_curso'),
('mesa'),
('plan_estudio'),
('sede'),
-- profesor
('cargo'),
('concurso'),
('concurso_cargo'),
('establecimiento'),
('profesor_concurso'),
('registro_inasistencia'),
('profesor_titulo_establecimiento'),
('registro_sancion'),
('sancion'),
('titulo'),
('titulo_establecimiento'),
('tipo_registro_inasistencia'),
-- principal
('alumno_curso'),
('calificacion'),
('inscripcion_materia_mesa'),
('materia_mesa');
/******* rol_permiso_tabla *******/

INSERT INTO rol_permiso_tabla(tabla_id,permiso_id,rol_id) VALUES
/*administrador*/
-- direccion
( 1, 1, 1),
( 2, 1, 1),
( 3, 1, 1),
( 4, 1, 1),
-- usuarios
( 5, 1, 1),
( 6, 1, 1),
( 7, 1, 1),
( 8, 1, 1),
( 9, 1, 1),
( 10, 1, 1),
-- institucional
( 11, 1, 1),
( 12, 1, 1),
( 13, 1, 1),
( 14, 1, 1),
( 15, 1, 1),
( 16, 1, 1),
( 17, 1, 1),
( 18, 1, 1),
( 19, 1, 1),
-- profesor
( 20, 1, 1),
( 21, 1, 1),
( 22, 1, 1),
( 23, 1, 1),
( 24, 1, 1),
( 25, 1, 1),
( 26, 1, 1),
( 27, 1, 1),
( 28, 1, 1),
( 29, 1, 1),
( 30, 1, 1),
( 31, 1, 1),
-- principal
( 32, 1, 1),
( 33, 1, 1),
( 34, 1, 1),
( 35, 1, 1),
/*bedel*/
( 1, 1, 2),
( 2, 1, 2),
( 3, 1, 2),
( 4, 1, 2),
-- usuarios
( 5, 1, 2),
( 8, 1, 2),
( 10, 1, 2),
-- institucional
( 11, 5, 2),
( 12, 5, 2),
( 13, 5, 2),
( 14, 5, 2),
( 15, 5, 2),
( 16, 5, 2),
( 17, 1, 2),
( 18, 5, 2),
( 19, 5, 2),
-- profesor @FIXME revisar esto
( 20, 1, 2),
( 21, 1, 2),
( 22, 1, 2),
( 23, 1, 2),
( 24, 1, 2),
( 25, 1, 2),
( 26, 1, 2),
( 27, 1, 2),
( 28, 1, 2),
( 29, 1, 2),
( 30, 1, 2),
( 31, 1, 2),
-- principal
( 32, 1, 2),
( 33, 1, 2),
( 34, 1, 2),
( 35, 1, 2),
/*profesor*/
( 1, 4, 3),
( 2, 5, 3),
( 3, 5, 3),
( 4, 5, 3),
-- usuarios
( 5, 4, 3),
-- institucional
( 11, 5, 3),
( 12, 5, 3),
( 13, 5, 3),
( 14, 5, 3),
( 15, 5, 3),
( 16, 5, 3),
( 17, 5, 3),
( 18, 5, 3),
( 19, 5, 3),
-- profesor
( 20, 5, 3),
( 21, 5, 3),
( 22, 5, 3),
( 23, 5, 3),
( 24, 5, 3),
( 25, 5, 3),
( 26, 5, 3),
( 27, 5, 3),
( 28, 5, 3),
( 29, 5, 3),
( 30, 5, 3),
( 31, 5, 3),
-- principal
( 32, 5, 3),
( 33, 2, 3),
( 34, 5, 3),
( 35, 5, 3),
/*alumno*/
( 1, 4, 4),
( 2, 5, 4),
( 3, 5, 4),
( 4, 5, 4),
-- usuarios
( 5, 4, 4),
-- institucional
( 11, 5, 4),
( 12, 5, 4),
( 13, 5, 4),
( 14, 5, 4),
( 15, 5, 4),
( 16, 5, 4),
( 17, 5, 4),
( 18, 5, 4),
( 19, 5, 4),
-- principal
( 32, 5, 4),
( 33, 5, 4),
( 34, 2, 4),
( 35, 5, 4),
/*administrativo*/
( 1, 5, 5),
( 2, 5, 5),
( 3, 5, 5),
( 4, 5, 5),
-- usuarios
( 5, 5, 5),
( 6, 5, 5),
( 7, 5, 5),
( 8, 5, 5),
( 9, 5, 5),
( 10, 5, 5),
-- institucional
( 11, 5, 5),
( 12, 5, 5),
( 13, 5, 5),
( 14, 5, 5),
( 15, 5, 5),
( 16, 5, 5),
( 17, 5, 5),
( 18, 5, 5),
( 19, 5, 5),
-- profesor
( 20, 5, 5),
( 21, 5, 5),
( 22, 5, 5),
( 23, 5, 5),
( 24, 5, 5),
( 25, 5, 5),
( 26, 5, 5),
( 27, 5, 5),
( 28, 5, 5),
( 29, 5, 5),
( 30, 5, 5),
( 31, 5, 5),
-- principal
( 32, 5, 5),
( 33, 5, 5),
( 34, 5, 5),
( 35, 5, 5),
/*usuario sin autenticar*/
( 5, 5, 6);
/****** institucional ******/
-- sedes (godoy cruz,ciudad,las heras, rodeo del medio)
INSERT INTO direccion (calle,numero,localidad_id,operador_alta) VALUES('Sa&eacute;nz Pe&ntilde;a', 1271, 17,1),('Rodr&iacute;guez', 191, 5,1),('Doctor Moreno', 1424, 67,1),('Ruta Provincial N&uacute;mero 50', 4566, 115,1);
INSERT INTO sede(responsable,telefono,direccion_id,operador_alta) VALUES("Hugo Del Pozo",4494994,2,1),("Don Fantasma",4495445,3,1),("Marina Ismail",4493226,4,1),("Jose Durazno",4494789,5,1);
-- planes de estudio
INSERT INTO plan_estudio(numero_resolucion,nombre_carrera,nombre_titulo,modalidad,duracion,condiciones_ingreso,
articulaciones,horas_catedra,horas_reloj,operador_alta) VALUES
('0623/13','Tecnicatura Superior en An&aacute;lisis y Programaci&oacute;n de Sistemas','T&eacute;cnico Superior en An&aacute;lisis y Programaci&oacute;n de Sistemas','PRESENCIAL', 3,
'a.- Haber aprobado el Nivel Secundario o Ciclo Polimodal, o bien,
b.-Ser mayor de 25 anos seg&uacute;n lo establecido en el Art. 7&ordf; de la Ley de Educaci&oacute;n Superior N&ordf; 24.521 y cumplimentar lo establecido en la normativa provincial vigente.',
'',1860 ,2790 ,1);
/****** materias ******/
-- 1° 
INSERT INTO materia (nombre,anio,duracion,carga_horaria_semanal,carga_horaria_total,formato,acreditable,presencial,libre,promocional,plan_estudio_id, operador_alta)
VALUES  ("Programaci&oacute;n I",1,"1° CUATRIMESTRE",8,240,'MODULO/TALLER',FALSE,"NO(6P-2NP)",FALSE,TRUE,1,1),
	("Arquitectura de las Computadoras",1,DEFAULT,3,90,'MODULO',TRUE,"S&iacute;",FALSE,FALSE,1,1),
	("&Aacute;lgebra",1,"1° CUATRIMESTRE",4,60,'MODULO',FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("An&aacute;lisis de Sistemas",1,"2° CUATRIMESTRE",4,60,'MODULO/TALLER',FALSE,"S&iacute;",FALSE,TRUE,1,1),
	("Ingl&eacute;s",1,DEFAULT,3,90,'MODULO',FALSE,"S&iacute;",TRUE,FALSE,1,1),
	("Comunicaci&oacute;n, Comprensi&oacute;n y Producci&oacute;n de Textos",1,DEFAULT,3,90,'TALLER',FALSE,"S&iacute;",FALSE,TRUE,1,1),
	("L&oacute;gica",1,DEFAULT,3,90,'MODULO',FALSE,"S&iacute;",TRUE,FALSE,1,1),
	("Sistemas Administrativos Aplicados",1,DEFAULT,4,120,"ASIG",TRUE,"S&iacute;",FALSE,FALSE,1,1),
	("Pr&aacute;ctica Profesionalizante I",1,DEFAULT,4,120,"",FALSE,"S&iacute;",FALSE,FALSE,1,1);
-- 2°
INSERT INTO materia (nombre,anio,duracion,carga_horaria_semanal,carga_horaria_total,formato,acreditable,presencial,libre,promocional,plan_estudio_id, operador_alta)
VALUES  ("Programaci&oacute;n II",2,DEFAULT,6,180,"MODULO/LABORATORIO",FALSE,"NO(4P-2NP)",FALSE,FALSE,1,1),
	("Comicaciones y Redes",2,DEFAULT,3,90,"MODULO",TRUE,"S&iacute;",TRUE,FALSE,1,1),
	("Matem&aacute;tica Discreta",2,"1° CUATRIMESTRE",4,60,"MODULO",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("An&aacute;lisis Matem&aacute;tico",2,"2° CUATRIMESTRE",5,75,"MODULO",FALSE,"S&iacute;",TRUE,FALSE,1,1),
	("Ingl&eacute;s T&eacute;cnico I",2,DEFAULT,3,90,"TALLER",TRUE,"S&iacute;",FALSE,TRUE,1,1),
	("An&aacute;lisis y Dise&ntilde;o de Sistemas",2,DEFAULT,5,150,"MODULO/TALLER",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Sistemas Operativos",2,DEFAULT,3,90,"LABORATORIO",FALSE,"S&iacute;",FALSE,TRUE,1,1),
	("Base de Datos I",2,"anual",3,90,"MODULO/LANORATORIO",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Pr&aacute;ctica Profesionalizante II",2,DEFAULT,4,120,"",FALSE,"NO(2P-2NP)",FALSE,FALSE,1,1);
-- 3°
INSERT INTO materia (nombre,anio,duracion,carga_horaria_semanal,carga_horaria_total,formato,acreditable,presencial,libre,promocional,plan_estudio_id, operador_alta)
VALUES  ("Programaci&oacute;n III",3,DEFAULT,4,120,"MODULO/TALLER",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Dise&ntilde;o de Interfaces",3,"ANUAL",4,120,"MODULO/TALLER",FALSE,"S&iacute;",FALSE,TRUE,1,1),
	("Auditor&iacute;a y Calidad de Sistemas",3,"1° cuatrimestre",4,60,"MODULO/TALLER",TRUE,"NO(3P-1NP)",FALSE,TRUE,1,1),
	("Seguridad Inform&aacute;tica",3,"2° CUATRIMESTRE",4,60,"MODULO/TALLER",TRUE,"NO(3P-1NP)",FALSE,TRUE,1,1),
	("Ingl&eacute;s T&eacute;cnico II",3,"ANUAL",2,60,"TALLER",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Base de Datos II",3,"ANUAL",3,90,"LABORATORIO",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Derecho Inform&aacute;tico",3,"1° cuatrimestre",2,30,"MODULO",TRUE,"S&iacute;",FALSE,FALSE,1,1),
	("&Eacute;tica Profesional",3,"2° CUATRIMESTRE",2,30,"MODULO",FALSE,"S&iacute;",FALSE,FALSE,1,1),
	("Probabilidad y Estad&iacute;stica",3,"1° CUATRIMESTRE",4,60,"MODULO",TRUE,"S&iacute;",TRUE,FALSE,1,1),
	("Problem&aacute;tica Sociocultural y del Trabajo",3,"2° CUATRIMESTRE",3,45,"SEM",TRUE,"S&iacute;",FALSE,TRUE,1,1),
	("Pr&aacute;ctica Profesionalizante III",3,"ANUAL",7,210,"",FALSE,"NO(2P-2NP)",FALSE,FALSE,1,1);
/****** correlatividades ******/
-- Primer Año--
-- Programacion I: Logica, Inglés, Álgebra
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)
VALUES(1,7,1),(1,5,1),(1,3,1);
-- Análisis de Sistemas: Sistemas Administrativos Aplicados
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(4,8,1);
-- Inglés: Comunicación,Comprensión y Producción de Textos
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(5,6,1);
-- Práctica Profesionalizante I: Programación I, Arquitectura de las Computadoras, Sistemas Administrativos Aplicados
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(9,1,1),(9,2,1),(9,8,1);

-- Segundo Año--
-- Programacion II: Programación 
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(10,1,1);
-- Comunicaciones y Redes: Arquitectura de las Computadoras
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(11,2,1);
-- Matemática Discreta: Álgebra
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(12,3,1);
-- Inglés Técnico I: Inglés
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(14,5,1);
-- Análisis y Diseño de Sistemas: Análisis de Sistemas, Lógica
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(15,4,1),(15,7,1);
-- Sistemas Operativos: Arquitectura de las Computadoras
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(16,2,1);
-- Análisis Matemático: Matemática Discreta
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(13,12,1);
-- Base de Datos I: Arquitectura de las Computadoras, Lógica*/
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(17,2,1),(17,7,1);
/*Práctica Profesionalizante II: Práctica Profesional I*/
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(18,9,1);

-- Tercer Año--
-- Programacion III: Programación II
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(19,10,1);
-- Diseño de Interfaces: Análisis y Diseño de Sistemas
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(20,15,1);
-- Auditoría y Calidad de Sistemas: Análisis y Diseño de Sistemas
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(21,15,1);
-- Inglés Técnico II: Inglés Técnico I
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(23,14,1);
-- Ética Profesional: Arquitectura de las Computadoras
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(26,2,1);
-- Base de Datos II: Base de Datos I
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(24,17,1);
-- Probabilidad y Estadística: Análisis Matemático
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(27,13,1);
-- Seguridad Informática: Sistemas Operativos, Auditoría y Calidad de Sistemas
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(22,16,1),(22,21,1);
/*Práctica Profesionalizante III: TODOS(Comunicación y Redes,Ética Profesional, 
Problemática Sociocultural y del Trabajo, Práctica Profesionalizante II, Diseño de Interfaces,
Programación III,Base de Datos II, Derecho Informático, Inglés Técnico I,Inglés Técnico II,Base de Datos I,Base de Datos II,Auditoría y Calidad de Sistemas, Seguridad Informática, Probabilidad y Estadística*/
INSERT INTO materia_correlatividad(materia_id,materia_correlativa_id,operador_alta)VALUES(29,14,1),(29,17,1),(29,23,1),(29,24,1),(29,27,1),(29,11,1),(29,26,1),(29,28,1),(29,18,1),(29,20,1),(29,19,1),(29,25,1),(29,21,1),(29,22,1);
/****** Modulo profesor******/
-- direccion
INSERT INTO direccion(calle,numero,localidad_id, operador_alta) 
VALUES	('San Juan',1234,4,1),
	('Cervantes',2050,16,1),
	('Lavalle',321,17,1),
	('Belgrano',721,17,1),
	('Catamarca',147,4,1),
	('Av. Champagnat',5500,9,1),
	('J. Rodriguez',273,5,1);
-- usuario
INSERT INTO usuario(documento,nombre,apellido,fecha_nacimiento,USER,pass,email,telefono_fijo,telefono_movil,direccion_id,rol_id,operador_alta) 
VALUES  (12345678,'Juan','P&eacute;rez','1980-04-05','administrador','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'juanp@ejemplo.com',4488875,2612185104,6,1,1),
	(19756321,'Mabel','Quintana','1990-04-05','bedel','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'mquintana@ejemplo.com',NULL,2611235104,7,2,1),
	(19756321,'Carlos','Sosa','1992-04-05','profesor','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413','profe@ejemplo.com',NULL,2611235104,8,3,1),
	(20005654,'Jimena','Gomez','1996-11-14','alumno','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413','jime87@ejemplo.com',NULL,NULL,9,4,1),
	(20005654,'Ximena','Peralta','1988-07-21','administrativo','ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413','ejemplo@ejemplo.com',NULL,NULL,10,5,1);
-- profesor
INSERT INTO profesor(legajo,usuario_id,operador_alta)
VALUES	( 10001, 2,1),
	( 10002, 3,1),
	( 10003, 4,1);
-- establecimiento
INSERT INTO establecimiento(nombre,direccion_id,operador_alta)
VALUES	('Universidad Champagnat', 9,1),
	('Universidad del Aconcagua', 10,1),
	('Facultad de Ingenier&iacute;a - Universidad Nacional de Cuyo', 11,1),
	('Universidad Tecnol&oacute;gica Nacional - Facultad Regional Mendoza', 12,1);
-- titulo (a esto sí que le mande fruta)
INSERT INTO titulo(nombre,duracion,operador_alta) 
VALUES	('Licenciado en Sistemas de Informaci&oacute;n', 4,1),
	('Licenciado en Administraci&oacute;n', 4,1),
	('Ingeniero en Sistemas', 5,1), 
	('Ingeniero en Mecatr&oacute;nica ', 6,1);
-- titulo_establecimiento
INSERT INTO titulo_establecimiento(titulo_id,establecimiento_id,operador_alta)
VALUES	( 1, 1,1),
	( 2, 2,1),
	( 3, 3,1),
	( 4, 4,1);
-- profesor_titulo
INSERT INTO profesor_titulo_establecimiento(profesor_id,titulo_establecimiento_id,operador_alta)
VALUES	(10001,1,1),
	(10002,2,1),
	(10003,3,1);
/******	continuo con institucional ******/
-- carrera
INSERT INTO carrera(plan_estudio_id,coordinador,operador_alta)
VALUES	(1,10001,1);		
-- carrea_sede
INSERT INTO carrera_sede(carrera_id,sede_id,operador_alta)VALUES(1,2,1),(1,3,1),(1,4,1);
-- curso no se realmente cuantos cursos hay, pero voy a cargar 1 curso para cada y cadad sede, excepto ciudad que se que teiene 3 primeros
INSERT INTO curso(anio,division,carrera_sede_id,operador_alta)
VALUES	( 1,'A', 1,1),
	( 1,'B', 1,1),
	( 1,'C', 1,1),
	( 2,'A', 1,1),
	( 3,'A', 1,1),
	( 1,'A', 2,1),
	( 2,'A', 2,1),
	( 3,'A', 2,1),
	( 1,'A', 3,1),
	( 2,'A', 3,1),
	( 3,'A', 3,1);
GRANT INSERT, SELECT, UPDATE ON `siame`.* TO 'siame_std_user'@'%' IDENTIFIED BY '123456'; 
GRANT INSERT, SELECT, UPDATE ON `siame`.* TO 'siame_std_user'@'localhost' IDENTIFIED BY '123456';  
/*
--Listar permisos y según roles por tabla

select t.nombre as TABLA, t.id,p.descripcion as PERMISO,r.descripcion as ROL
from rol_permiso_tabla rpt 
join tabla t on t.id = rpt.tabla_id
join permiso p on p.id = rpt.permiso_id
join rol r on r.id = rpt.rol_id
where rpt.rol_id = 2
*/