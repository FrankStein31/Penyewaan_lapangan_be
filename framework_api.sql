/*
SQLyog Enterprise v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - framework_api
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`framework_api` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `framework_api`;

/*Table structure for table `fasilitas` */

DROP TABLE IF EXISTS `fasilitas`;

CREATE TABLE `fasilitas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_fasilitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas` */

insert  into `fasilitas`(`id`,`nama_fasilitas`,`deskripsi`,`created_at`,`updated_at`) values 
(1,'Toilet','Toilet pria dan wanita','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'Ruang Ganti','Ruang ganti pria dan wanita','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Parkir','Area parkir luas','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'Kantin','Kantin dan tempat makan','2025-05-22 21:04:37','2025-05-22 21:04:37');

/*Table structure for table `fasilitas_lapangan` */

DROP TABLE IF EXISTS `fasilitas_lapangan`;

CREATE TABLE `fasilitas_lapangan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lapangan_id` bigint unsigned NOT NULL,
  `fasilitas_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fasilitas_lapangan_lapangan_id_foreign` (`lapangan_id`),
  KEY `fasilitas_lapangan_fasilitas_id_foreign` (`fasilitas_id`),
  CONSTRAINT `fasilitas_lapangan_fasilitas_id_foreign` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fasilitas_lapangan_lapangan_id_foreign` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas_lapangan` */

/*Table structure for table `hari` */

DROP TABLE IF EXISTS `hari`;

CREATE TABLE `hari` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_hari` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hari_nama_hari_unique` (`nama_hari`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `hari` */

insert  into `hari`(`id`,`nama_hari`,`created_at`,`updated_at`) values 
(1,'Senin','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'Selasa','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Rabu','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'Kamis','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(5,'Jumat','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(6,'Sabtu','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(7,'Minggu','2025-05-22 21:04:37','2025-05-22 21:04:37');

/*Table structure for table `kategori_laps` */

DROP TABLE IF EXISTS `kategori_laps`;

CREATE TABLE `kategori_laps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kategori_laps` */

insert  into `kategori_laps`(`id`,`nama_kategori`,`deskripsi`,`created_at`,`updated_at`) values 
(1,'Basket','Lapangan basket','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'Futsal','Lapangan futsal','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Badminton','Lapangan badminton','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'Tenis','Lapangan tenis','2025-05-22 21:04:37','2025-05-22 21:04:37');

/*Table structure for table `lapangan` */

DROP TABLE IF EXISTS `lapangan`;

CREATE TABLE `lapangan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga` decimal(10,2) NOT NULL,
  `kategori_id` bigint unsigned NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('tersedia','tidak tersedia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lapangan_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `lapangan_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_laps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lapangan` */

insert  into `lapangan`(`id`,`nama`,`kapasitas`,`deskripsi`,`harga`,`kategori_id`,`foto`,`status`,`created_at`,`updated_at`) values 
(1,'Lapangan Basket A',10,'Lapangan basket standar',100000.00,1,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-23 18:32:54'),
(2,'Lapangan Futsal A',10,'Lapangan futsal dengan rumput sintetis',150000.00,2,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Lapangan Badminton A',4,'Lapangan badminton standar',50000.00,3,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-22 21:04:37');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2025_03_06_140421_users',1),
(2,'2025_03_06_141835_create_personal_access_tokens_table',1),
(3,'2025_03_09_040954_kategori_laps',1),
(4,'2025_03_09_043526_fasilitas',1),
(5,'2025_03_09_111603_lapangan',1),
(13,'2025_03_09_153737_status_lapangan',2),
(14,'2025_03_09_210649_create_hari_table',2),
(15,'2025_03_09_210650_create_pemesanan_table',2),
(16,'2025_03_09_210651_create_pembayaran_table',2),
(17,'2025_03_11_090033_sesi',2),
(18,'2025_05_16_120357_update_pemesanan_sesi_tables',2),
(19,'2025_05_16_145239_add_customer_info_to_pemesanan_table',2),
(20,'2025_05_22_205225_update_status_lapangan_table',2),
(21,'2025_05_22_205257_update_pemesanan_table',2),
(22,'2025_05_22_205316_update_sesi_table',2);

/*Table structure for table `pembayaran` */

DROP TABLE IF EXISTS `pembayaran`;

CREATE TABLE `pembayaran` (
  `id_pembayaran` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pemesanan` bigint unsigned NOT NULL,
  `metode` enum('transfer','midtrans') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu verifikasi','belum dibayar','ditolak','diverifikasi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  KEY `pembayaran_id_pemesanan_foreign` (`id_pemesanan`),
  CONSTRAINT `pembayaran_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pembayaran` */

insert  into `pembayaran`(`id_pembayaran`,`id_pemesanan`,`metode`,`bukti_transfer`,`status`,`created_at`,`updated_at`) values 
(1,4,'transfer',NULL,'belum dibayar','2025-05-23 15:01:16','2025-05-23 15:01:16');

/*Table structure for table `pemesanan` */

DROP TABLE IF EXISTS `pemesanan`;

CREATE TABLE `pemesanan` (
  `id_pemesanan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_lapangan` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `id_sesi` json DEFAULT NULL,
  `status` enum('menunggu verifikasi','diverifikasi','ditolak','dibatalkan','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu verifikasi',
  `total_harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemesanan`),
  KEY `pemesanan_id_user_foreign` (`id_user`),
  KEY `pemesanan_id_lapangan_foreign` (`id_lapangan`),
  CONSTRAINT `pemesanan_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemesanan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemesanan` */

insert  into `pemesanan`(`id_pemesanan`,`id_user`,`id_lapangan`,`tanggal`,`jam_mulai`,`jam_selesai`,`id_sesi`,`status`,`total_harga`,`nama_pelanggan`,`email`,`no_hp`,`catatan`,`created_at`,`updated_at`) values 
(2,2,2,'2025-05-23','14:00:00','16:00:00','[6, 7]','menunggu verifikasi',300000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-05-23 13:31:07','2025-05-23 13:31:07'),
(4,2,3,'2025-05-23','17:00:00','19:00:00','[9, 10]','menunggu verifikasi',175000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-05-23 15:01:16','2025-05-23 15:01:16');

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(16,'App\\Models\\User',3,'auth_token','45c437ee096baf45b95ddee146ad090b6df95b228deb31be87d686fac81576c6','[\"*\"]',NULL,NULL,'2025-05-23 13:48:45','2025-05-23 13:48:45');

/*Table structure for table `sesis` */

DROP TABLE IF EXISTS `sesis`;

CREATE TABLE `sesis` (
  `id_jam` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jam`),
  KEY `sesis_hari_id_foreign` (`hari_id`),
  CONSTRAINT `sesis_hari_id_foreign` FOREIGN KEY (`hari_id`) REFERENCES `hari` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sesis` */

insert  into `sesis`(`id_jam`,`jam_mulai`,`jam_selesai`,`deskripsi`,`hari_id`,`created_at`,`updated_at`) values 
(1,'08:00:00','09:00:00','Sesi Pagi 1',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'09:00:00','10:00:00','Sesi Pagi 2',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'10:00:00','11:00:00','Sesi Pagi 3',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'11:00:00','12:00:00','Sesi Siang 1',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(5,'13:00:00','14:00:00','Sesi Siang 2',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(6,'14:00:00','15:00:00','Sesi Siang 3',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(7,'15:00:00','16:00:00','Sesi Sore 1',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(8,'16:00:00','17:00:00','Sesi Sore 2',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(9,'17:00:00','18:00:00','Sesi Sore 3',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(10,'18:00:00','19:00:00','Sesi Malam 1',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(11,'19:00:00','20:00:00','Sesi Malam 2',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(12,'20:00:00','21:00:00','Sesi Malam 3',NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37');

/*Table structure for table `status_lapangan` */

DROP TABLE IF EXISTS `status_lapangan`;

CREATE TABLE `status_lapangan` (
  `id_status` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_lapangan` bigint unsigned NOT NULL,
  `deskripsi_status` enum('tersedia','disewa','perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date DEFAULT NULL,
  `id_sesi` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_status`),
  KEY `status_lapangan_id_lapangan_foreign` (`id_lapangan`),
  CONSTRAINT `status_lapangan_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `status_lapangan` */

insert  into `status_lapangan`(`id_status`,`id_lapangan`,`deskripsi_status`,`tanggal`,`id_sesi`,`created_at`,`updated_at`) values 
(1,1,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,2,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,3,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(5,2,'disewa','2025-05-23','6','2025-05-23 13:31:07','2025-05-23 13:31:07'),
(6,2,'disewa','2025-05-23','7','2025-05-23 13:31:07','2025-05-23 13:31:07'),
(9,3,'disewa','2025-05-23','9','2025-05-23 15:01:16','2025-05-23 15:01:16'),
(10,3,'disewa','2025-05-23','10','2025-05-23 15:01:16','2025-05-23 15:01:16');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_no_hp_unique` (`no_hp`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`nama`,`email`,`password`,`role`,`no_hp`,`created_at`,`updated_at`) values 
(1,'Admin','admin@admin.com','$2y$12$Scl/UKiCRdzL6sv6ixARJeRQBf87CexkRD3Eotr/rp.g0iah.a3iy','admin','081234567890','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'User','user@user.com','$2y$12$6zslFXv53LPl5RSi9novFuRIPirDiEiq03ncKMhMoAxXk48KlWqum','user','081234567891','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Frankie Steinlie','frankie.steinlie@gmail.com','$2y$12$dXH5eNU8RuehfDueSf1Ziu8iRI/W53j1q7Au8XKI5dvntjhLGZeLC','user','08883866931','2025-05-23 13:48:45','2025-05-23 13:48:45');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
