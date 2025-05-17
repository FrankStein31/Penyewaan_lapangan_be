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
  `nama_fasilitas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas` */

insert  into `fasilitas`(`id`,`nama_fasilitas`,`deskripsi`,`created_at`,`updated_at`) values 
(1,'Ruang Ganti','Pria dan Wanita','2025-03-09 04:40:39','2025-03-09 04:43:46'),
(2,'toilet','pria dan wanita','2025-03-09 04:43:56','2025-03-09 04:43:56'),
(4,'Air Mancur','mancur kolam','2025-03-28 15:32:25','2025-03-28 15:32:25'),
(5,'kantin','kantin makan','2025-03-28 15:47:21','2025-03-28 15:47:21'),
(8,'loker','loker penyimpanan','2025-04-06 08:54:03','2025-04-06 08:54:03');

/*Table structure for table `fasilitas_lapangan` */

DROP TABLE IF EXISTS `fasilitas_lapangan`;

CREATE TABLE `fasilitas_lapangan` (
  `fasilitas_id` bigint unsigned NOT NULL,
  `lapangan_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`fasilitas_id`,`lapangan_id`),
  KEY `lapangan_id` (`lapangan_id`),
  CONSTRAINT `fasilitas_lapangan_ibfk_1` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`),
  CONSTRAINT `fasilitas_lapangan_ibfk_2` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas_lapangan` */

insert  into `fasilitas_lapangan`(`fasilitas_id`,`lapangan_id`) values 
(1,2),
(2,2),
(5,2),
(2,3),
(5,3),
(1,5),
(2,5),
(5,5),
(2,6),
(5,6),
(8,6);

/*Table structure for table `hari` */

DROP TABLE IF EXISTS `hari`;

CREATE TABLE `hari` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_hari` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hari_nama_hari_unique` (`nama_hari`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `hari` */

insert  into `hari`(`id`,`nama_hari`,`created_at`,`updated_at`) values 
(1,'senin','2025-03-11 09:43:41','2025-03-11 09:43:44'),
(2,'selasa','2025-03-19 06:52:15','2025-03-19 06:52:15'),
(3,'rabu','2025-03-19 06:52:23','2025-03-19 06:52:23'),
(4,'kamis','2025-03-19 06:53:42','2025-03-19 06:54:10'),
(6,'jumat','2025-03-28 15:48:34','2025-03-28 15:48:34'),
(7,'sabtu','2025-03-29 15:08:40','2025-03-29 15:08:40'),
(8,'Minggu','2025-04-06 08:53:38','2025-04-06 08:53:38');

/*Table structure for table `kategori_laps` */

DROP TABLE IF EXISTS `kategori_laps`;

CREATE TABLE `kategori_laps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kategori_laps` */

insert  into `kategori_laps`(`id`,`nama_kategori`,`deskripsi`,`created_at`,`updated_at`) values 
(2,'Badminton','lapangan badminton','2025-03-09 04:29:11','2025-03-29 14:43:06'),
(3,'Futsal','Lapangan Futsal','2025-03-09 04:29:20','2025-04-06 08:55:39'),
(4,'Basket','Lapangan basket','2025-03-28 16:34:51','2025-03-29 14:59:11'),
(7,'Tenis','LapanganTenis','2025-05-16 21:53:21','2025-05-16 21:53:21');

/*Table structure for table `lapangan` */

DROP TABLE IF EXISTS `lapangan`;

CREATE TABLE `lapangan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `harga` decimal(10,2) NOT NULL,
  `kategori_id` bigint unsigned NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('tersedia','tidak tersedia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `lapangan_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_laps` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lapangan` */

insert  into `lapangan`(`id`,`nama`,`kapasitas`,`deskripsi`,`harga`,`kategori_id`,`foto`,`status`,`created_at`,`updated_at`) values 
(2,'Lapangan Futsal A',10,'Lapangan futsal indoor dengan rumput sintetis',100000.00,3,'lapangan/1743240744_back hitam.jpg','tersedia','2025-03-09 15:50:26','2025-04-06 08:57:10'),
(3,'Lapangan Basket A',20,'lapangan basket sintetis',35000.00,4,'lapangan/1743240238_back hitam.jpg','tersedia','2025-03-29 15:39:06','2025-03-29 16:23:58'),
(5,'Lapangan Basket B',5,'sintesis',35000.00,2,'lapangan/1743240744_back hitam.jpg','tersedia','2025-03-29 16:32:24','2025-03-29 16:32:24'),
(6,'Lapangan Futsal B',10,'Rumput sintetis',25000.00,3,'lapangan/1743240744_back hitam.jpg','tersedia','2025-04-06 08:56:27','2025-04-06 08:56:27');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2025_03_06_140421_user',1),
(2,'2025_03_06_141835_create_personal_access_tokens_table',2),
(3,'2025_03_09_040954_kategori_lap',3),
(4,'2025_03_09_043526_fasilitas',4),
(5,'2025_03_09_111603_lapangan',5),
(6,'2025_03_09_153737_status_lapangan',6),
(7,'2025_03_09_210649_create_hari_table',7),
(8,'2025_03_09_210650_create_pemesanan_table',8),
(9,'2025_03_09_210651_create_pembayaran_table',9),
(10,'2025_03_11_090033_sesi',10),
(11,'2025_05_16_120357_update_pemesanan_sesi_tables',11);

/*Table structure for table `pembayaran` */

DROP TABLE IF EXISTS `pembayaran`;

CREATE TABLE `pembayaran` (
  `id_pembayaran` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pemesanan` bigint unsigned NOT NULL,
  `metode` enum('transfer','midtrans') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_transfer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu verifikasi','belum dibayar','ditolak','diverifikasi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL DEFAULT '0.00',
  `kode_pembayaran` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  KEY `pembayaran_id_pemesanan_foreign` (`id_pemesanan`),
  CONSTRAINT `pembayaran_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pembayaran` */

/*Table structure for table `pemesanan` */

DROP TABLE IF EXISTS `pemesanan`;

CREATE TABLE `pemesanan` (
  `id_pemesanan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_lapangan` bigint unsigned NOT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `id_sesi` bigint unsigned DEFAULT NULL,
  `status` enum('menunggu verifikasi','diverifikasi','ditolak','dibatalkan','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu verifikasi',
  `total_harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemesanan`),
  KEY `pemesanan_id_user_foreign` (`id_user`),
  KEY `pemesanan_id_lapangan_foreign` (`id_lapangan`),
  KEY `pemesanan_id_sesi_foreign` (`id_sesi`),
  CONSTRAINT `pemesanan_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemesanan_id_sesi_foreign` FOREIGN KEY (`id_sesi`) REFERENCES `sesis` (`id_jam`) ON DELETE SET NULL,
  CONSTRAINT `pemesanan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemesanan` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(53,'App\\Models\\User',13,'auth_token','a74a33cf7666b004b41099c3e14409d23fc3c5e8bec3ab7deb358dd9fd5fec4a','[\"*\"]',NULL,NULL,'2025-03-29 15:12:23','2025-03-29 15:12:23'),
(71,'App\\Models\\User',15,'auth_token','9167c82bbe5382c77a861744323c59115512aef0a8f8b7ced18d4b0578252199','[\"*\"]',NULL,NULL,'2025-04-24 20:56:07','2025-04-24 20:56:07'),
(106,'App\\Models\\User',12,'auth_token','1e8a9eaf4b9ed7b78cddbec6a894cc222d12cecec1889c168c9497537f708d65','[\"*\"]',NULL,NULL,'2025-05-16 21:55:07','2025-05-16 21:55:07');

/*Table structure for table `sesis` */

DROP TABLE IF EXISTS `sesis`;

CREATE TABLE `sesis` (
  `id_jam` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `deskripsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jam`),
  KEY `sesis_hari_id_foreign` (`hari_id`),
  CONSTRAINT `sesis_hari_id_foreign` FOREIGN KEY (`hari_id`) REFERENCES `hari` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sesis` */

insert  into `sesis`(`id_jam`,`jam_mulai`,`jam_selesai`,`deskripsi`,`hari_id`,`created_at`,`updated_at`) values 
(1,'07:00:00','08:00:00','sesi 1',NULL,'2025-03-11 09:19:59','2025-03-11 09:27:26'),
(2,'08:00:00','09:00:00','sesi 2',NULL,'2025-03-11 09:20:45','2025-03-11 09:20:45'),
(4,'09:00:00','10:00:00','sesi 3',NULL,'2025-03-20 11:42:36','2025-03-20 11:42:39'),
(5,'10:00:00','11:00:00','sesi 4',NULL,'2025-03-20 11:43:00','2025-03-20 11:43:03'),
(6,'11:00:00','12:00:00','sesi 5',NULL,'2025-03-29 15:26:16','2025-03-29 15:26:16'),
(7,'12:00:00','13:00:00','sesi 6',NULL,'2025-03-29 15:27:13','2025-03-29 15:27:13'),
(8,'13:00:00','14:00:00','sesi 7',NULL,'2025-05-16 21:54:13','2025-05-16 21:54:13');

/*Table structure for table `status_lapangan` */

DROP TABLE IF EXISTS `status_lapangan`;

CREATE TABLE `status_lapangan` (
  `id_status` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_lapangan` bigint unsigned NOT NULL,
  `deskripsi_status` enum('tersedia','disewa','perbaikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date DEFAULT NULL,
  `id_sesi` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_status`),
  KEY `status_lapangan_id_lapangan_foreign` (`id_lapangan`),
  KEY `status_lapangan_id_sesi_foreign` (`id_sesi`),
  CONSTRAINT `status_lapangan_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `status_lapangan_id_sesi_foreign` FOREIGN KEY (`id_sesi`) REFERENCES `sesis` (`id_jam`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `status_lapangan` */

insert  into `status_lapangan`(`id_status`,`id_lapangan`,`deskripsi_status`,`tanggal`,`id_sesi`,`created_at`,`updated_at`) values 
(1,2,'tersedia',NULL,NULL,'2025-03-09 15:50:59','2025-03-20 04:39:08');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_nim_unique` (`no_hp`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`nama`,`email`,`password`,`role`,`no_hp`,`created_at`,`updated_at`) values 
(5,'Admin','admin@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','admin','213730071','2025-03-19 08:07:05','2025-03-19 12:48:56'),
(6,'User','user@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','user','444444','2025-03-19 13:11:35','2025-03-19 15:10:38'),
(7,'stein','stein@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','user','12345','2025-03-20 04:40:32','2025-03-20 04:40:32'),
(8,'agung wibowo','agung@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','user','321123','2025-03-20 13:31:48','2025-03-28 16:50:27'),
(12,'Frankie Steinlie','frankie.steinlie@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','user','08883866931','2025-03-29 14:48:48','2025-03-29 14:48:48'),
(15,'user agung','agungwibowo@gmail.com','$2y$12$wYTCLpqNOss1be00RtWTiOoEaj6/hzSp5w4d5Red3uD7ntUDtKV0a','user','088838669311','2025-04-24 20:56:07','2025-04-24 20:56:07');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
