
-- Versión limpia: sin datos, solo usuario administrador y registro de migraciones
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `isgb_blog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `isgb_blog`;
DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carreras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `activa` tinyint(4) NOT NULL DEFAULT 1,
  `duracion` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `imagen_id` int(10) unsigned DEFAULT NULL,
  `plan_pdf_id` int(10) unsigned DEFAULT NULL,
  `resolucion_pdf_id` int(10) unsigned DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  `fecha_borrado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carreras_slug_uq` (`slug`),
  KEY `carreras_imagen_id_fk` (`imagen_id`),
  KEY `carreras_plan_pdf_id_fk` (`plan_pdf_id`),
  KEY `carreras_resolucion_pdf_id_fk` (`resolucion_pdf_id`),
  CONSTRAINT `carreras_imagen_id_fk` FOREIGN KEY (`imagen_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `carreras_plan_pdf_id_fk` FOREIGN KEY (`plan_pdf_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `carreras_resolucion_pdf_id_fk` FOREIGN KEY (`resolucion_pdf_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(60) DEFAULT NULL,
  `email_contacto` varchar(150) DEFAULT NULL,
  `inscripcion_abierta` tinyint(4) NOT NULL DEFAULT 1,
  `inscripcion_texto` varchar(100) DEFAULT NULL,
  `descripcion_pie` varchar(255) DEFAULT NULL,
  `portada_imagen_id` int(10) unsigned DEFAULT NULL,
  `portada_etiqueta` varchar(100) DEFAULT '',
  `portada_titulo` varchar(200) DEFAULT '',
  `portada_texto` varchar(400) DEFAULT '',
  `portada_boton1_texto` varchar(60) DEFAULT '',
  `portada_boton1_enlace` varchar(255) DEFAULT '',
  `portada_boton2_texto` varchar(60) DEFAULT '',
  `portada_boton2_enlace` varchar(255) DEFAULT '',
  `portada_boton2_visible` tinyint(1) DEFAULT 1,
  `instagram` varchar(200) DEFAULT NULL,
  `facebook` varchar(200) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `configuracion_portada_imagen_id_fk` (`portada_imagen_id`),
  CONSTRAINT `configuracion_portada_imagen_id_fk` FOREIGN KEY (`portada_imagen_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interacciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `posteo_id` int(10) unsigned NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `interacciones_usuario_id_posteo_id_tipo_uq` (`usuario_id`,`posteo_id`,`tipo`),
  KEY `interacciones_posteo_id_fk` (`posteo_id`),
  CONSTRAINT `interacciones_posteo_id_fk` FOREIGN KEY (`posteo_id`) REFERENCES `posteos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `interacciones_usuario_id_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrera_id` int(10) unsigned NOT NULL,
  `anio` tinyint(4) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `materias_carrera_id_fk` (`carrera_id`),
  CONSTRAINT `materias_carrera_id_fk` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alt` varchar(200) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  `fecha_borrado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noticias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `bajada` varchar(255) NOT NULL,
  `imagen_id` int(10) unsigned DEFAULT NULL,
  `contenido` text NOT NULL,
  `categoria` varchar(20) NOT NULL DEFAULT 'institucionales',
  `autor_id` int(10) unsigned DEFAULT NULL,
  `publicado_en` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  `fecha_borrado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `noticias_slug_uq` (`slug`),
  KEY `noticias_imagen_id_fk` (`imagen_id`),
  KEY `noticias_autor_id_fk` (`autor_id`),
  CONSTRAINT `noticias_autor_id_fk` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `noticias_imagen_id_fk` FOREIGN KEY (`imagen_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posteos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posteos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `extracto` varchar(255) NOT NULL,
  `imagen_id` int(10) unsigned DEFAULT NULL,
  `contenido` text NOT NULL,
  `carrera_id` int(10) unsigned DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'borrador',
  `autor_id` int(10) unsigned DEFAULT NULL,
  `publicado_en` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  `fecha_borrado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posteos_slug_uq` (`slug`),
  KEY `posteos_imagen_id_fk` (`imagen_id`),
  KEY `posteos_carrera_id_fk` (`carrera_id`),
  KEY `posteos_autor_id_fk` (`autor_id`),
  CONSTRAINT `posteos_autor_id_fk` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `posteos_carrera_id_fk` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `posteos_imagen_id_fk` FOREIGN KEY (`imagen_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'alumnado',
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT NULL,
  `fecha_borrado` datetime DEFAULT NULL,
  `token_reset` varchar(64) DEFAULT NULL,
  `token_reset_expira` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_uq` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
-- (se conserva para que CodeIgniter no intente volver a correr las migraciones)
--

LOCK TABLES `migrations` WRITE;
INSERT INTO `migrations` VALUES (1,'2026-11-16-000001','App\\Database\\Migrations\\CrearUsuarios','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (2,'2026-11-16-000002','App\\Database\\Migrations\\CrearMedia','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (3,'2026-11-16-000003','App\\Database\\Migrations\\CrearCarreras','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (4,'2026-11-16-000004','App\\Database\\Migrations\\CrearMaterias','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (5,'2026-11-16-000005','App\\Database\\Migrations\\CrearTags','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (6,'2026-11-16-000006','App\\Database\\Migrations\\CrearPosteos','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (7,'2026-11-16-000007','App\\Database\\Migrations\\CrearPosteosTags','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (8,'2026-11-16-000008','App\\Database\\Migrations\\CrearNoticias','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (9,'2026-11-16-000009','App\\Database\\Migrations\\CrearInteracciones','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (10,'2026-11-16-000010','App\\Database\\Migrations\\CrearSuscriptores','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (11,'2026-11-17-000011','App\\Database\\Migrations\\AgregarBoletinNoticias','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (12,'2026-11-17-000012','App\\Database\\Migrations\\AgregarRestablecerUsuarios','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (13,'2026-11-17-000013','App\\Database\\Migrations\\CrearBoletinEnvios','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (14,'2026-11-17-000014','App\\Database\\Migrations\\CrearComentarios','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (15,'2026-11-17-000015','App\\Database\\Migrations\\CrearConfiguracion','default','App',1763337600,1);
INSERT INTO `migrations` VALUES (16,'2026-11-17-000016','App\\Database\\Migrations\\EliminarModulosNoNecesarios','default','App',1794968521,2);
INSERT INTO `migrations` VALUES (17,'2026-11-18-000017','App\\Database\\Migrations\\AgregarPortadaConfiguracion','default','App',1794968522,2);
INSERT INTO `migrations` VALUES (18,'2026-11-18-000018','App\\Database\\Migrations\\UnificarRolAlumnado','default','App',1794990000,3);
INSERT INTO `migrations` VALUES (19,'2026-11-18-000019','App\\Database\\Migrations\\QuitarAudienciaNoticias','default','App',1794999000,4);
INSERT INTO `migrations` VALUES (20,'2026-11-18-000020','App\\Database\\Migrations\\AgregarResolucionCarreras','default','App',1795003000,5);
INSERT INTO `migrations` VALUES (21,'2026-11-18-000021','App\\Database\\Migrations\\AgregarBorradoLogico','default','App',1795010000,6);
INSERT INTO `migrations` VALUES (22,'2026-11-18-000022','App\\Database\\Migrations\\AgregarTextosPortada','default','App',1795012601,7);
UNLOCK TABLES;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`, `fecha_edicion`) VALUES (2,'Administrador ISGB','admin@isgbenavento.edu.ar','$2b$12$fKU2RD4QCWWVPWrfz2Tmjeb/kHRy3YdiHvP4k32iKGPzrLmKnmRW6','admin','2026-11-17 08:56:00','2026-11-17 08:56:00');
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


