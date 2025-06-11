/*
SQLyog Enterprise
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas` */

insert  into `fasilitas`(`id`,`nama_fasilitas`,`deskripsi`,`created_at`,`updated_at`) values 
(1,'Toilet','Toilet pria dan wanita','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,'Ruang Ganti','Ruang ganti pria dan wanita','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Parkir','Area parkir luas','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'Kantin','Kantin dan tempat makan','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(5,'Loker','Loker tas','2025-06-11 14:36:30','2025-06-11 14:36:30');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `fasilitas_lapangan` */

insert  into `fasilitas_lapangan`(`id`,`lapangan_id`,`fasilitas_id`,`created_at`,`updated_at`) values 
(1,1,1,NULL,NULL),
(2,4,1,NULL,NULL),
(3,4,2,NULL,NULL),
(4,4,3,NULL,NULL),
(5,4,4,NULL,NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lapangan` */

insert  into `lapangan`(`id`,`nama`,`kapasitas`,`deskripsi`,`harga`,`kategori_id`,`foto`,`status`,`created_at`,`updated_at`) values 
(1,'Lapangan Basket A',10,'Lapangan basket standar',100000.00,1,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-23 18:32:54'),
(2,'Lapangan Futsal A',10,'Lapangan futsal dengan rumput sintetis',150000.00,2,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,'Lapangan Badminton A',4,'Lapangan badminton standar',50000.00,3,'lapangan/1747999974_logo.png','tersedia','2025-05-22 21:04:37','2025-05-22 21:04:37'),
(4,'coba',1,'lapangan coba',35000.00,2,'lapangan/1749627170_back hitam 2.jpg','tersedia','2025-06-11 14:32:50','2025-06-11 14:32:50');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(22,'2025_05_22_205316_update_sesi_table',2),
(23,'2024_03_20_add_midtrans_columns_to_pembayaran',3);

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
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_time` timestamp NULL DEFAULT NULL,
  `payment_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `total_bayar` decimal(10,2) DEFAULT NULL,
  `kode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  KEY `pembayaran_id_pemesanan_foreign` (`id_pemesanan`),
  CONSTRAINT `pembayaran_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pembayaran` */

insert  into `pembayaran`(`id_pembayaran`,`id_pemesanan`,`metode`,`bukti_transfer`,`status`,`created_at`,`updated_at`,`snap_token`,`transaction_id`,`payment_type`,`transaction_status`,`transaction_time`,`payment_code`,`pdf_url`,`paid_at`,`total_bayar`,`kode_pembayaran`) values 
(1,4,'transfer',NULL,'diverifikasi','2025-05-23 15:01:16','2025-05-23 15:01:16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(2,5,'transfer',NULL,'diverifikasi','2025-05-23 18:39:38','2025-05-23 18:39:38',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(3,6,'transfer',NULL,'diverifikasi','2025-06-11 14:13:51','2025-06-11 14:13:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(4,7,'transfer',NULL,'diverifikasi','2025-06-11 14:15:01','2025-06-11 14:15:01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(5,8,'midtrans',NULL,'diverifikasi','2025-06-11 14:23:12','2025-06-11 22:20:28','c4edfaf5-f2d2-48fd-a3ac-41e86d5b27c6','4bd29d19-434a-4325-995e-08ddfa7e6c41','bank_transfer','settlement',NULL,NULL,NULL,'2025-06-11 15:20:27',350000.00,NULL),
(6,9,'midtrans',NULL,'diverifikasi','2025-06-11 14:39:43','2025-06-11 20:11:15','937828bc-3590-4719-a1bd-6ea092f11b9d','99cd16cc-0b68-44d9-9a75-bc56593d8166','bank_transfer','settlement',NULL,NULL,NULL,'2025-06-11 13:11:14',170000.00,NULL),
(8,11,'midtrans',NULL,'diverifikasi','2025-06-11 23:24:49','2025-06-11 23:26:19','4d3d27de-937b-4dcf-9641-887818fafd28','d1257bcb-1491-411d-9543-41d2f2251ead','bank_transfer','settlement',NULL,NULL,NULL,'2025-06-11 16:26:18',70000.00,NULL),
(9,12,'midtrans',NULL,'diverifikasi','2025-06-12 00:13:48','2025-06-12 00:31:47','3bc442bf-6c6a-4c75-a839-296edd116112','7d88e27b-ca9e-49af-a77c-a894b6bf265a','bank_transfer','settlement',NULL,NULL,NULL,'2025-06-11 17:31:45',60000.00,NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemesanan` */

insert  into `pemesanan`(`id_pemesanan`,`id_user`,`id_lapangan`,`tanggal`,`jam_mulai`,`jam_selesai`,`id_sesi`,`status`,`total_harga`,`nama_pelanggan`,`email`,`no_hp`,`catatan`,`created_at`,`updated_at`) values 
(4,2,3,'2025-05-23','17:00:00','19:00:00','[9, 10]','selesai',175000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-05-23 15:01:16','2025-05-23 15:01:16'),
(5,2,2,'2025-05-23','20:00:00','21:00:00','[12]','selesai',200000.00,'frankie','frankie.steinlie@gmail.com','08512345678','','2025-05-23 18:39:38','2025-05-23 18:39:38'),
(6,2,1,'2025-06-11','15:00:00','17:00:00','[7, 8]','selesai',250000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-06-11 14:13:51','2025-06-11 14:13:51'),
(7,2,3,'2025-06-11','16:00:00','17:00:00','[8]','selesai',75000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-06-11 14:15:01','2025-06-11 14:15:01'),
(8,2,2,'2025-06-11','15:00:00','17:00:00','[7, 8]','selesai',350000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-06-11 14:23:12','2025-06-11 23:27:30'),
(9,2,4,'2025-06-11','19:00:00','21:00:00','[12, 11]','selesai',170000.00,'Frankie Steinlie','frankie.steinlie@gmail.com','08883866931','','2025-06-11 14:39:43','2025-06-11 23:27:39'),
(11,2,4,'2025-06-12','08:00:00','10:00:00','[1, 2]','selesai',70000.00,'frankie','frankie.steinlie@gmail.com','08512345678','','2025-06-11 23:24:49','2025-06-12 00:32:10'),
(12,2,4,'2025-06-12','16:00:00','17:00:00','[8]','diverifikasi',60000.00,'frankie','frankie.steinlie@gmail.com','08512345678','','2025-06-12 00:13:48','2025-06-12 00:31:47');

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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(16,'App\\Models\\User',3,'auth_token','45c437ee096baf45b95ddee146ad090b6df95b228deb31be87d686fac81576c6','[\"*\"]',NULL,NULL,'2025-05-23 13:48:45','2025-05-23 13:48:45'),
(47,'App\\Models\\User',1,'auth_token','c150314de291ca685e09acd3f9eb82cd293a8710c4ab8783aa0027b974214171','[\"*\"]',NULL,NULL,'2025-06-11 23:27:03','2025-06-11 23:27:03'),
(48,'App\\Models\\User',1,'auth_token','43d2716de3c9bd9d0b96d61819101c3d63e1089d1193978791f013483efdc090','[\"*\"]',NULL,NULL,'2025-06-11 23:28:35','2025-06-11 23:28:35'),
(49,'App\\Models\\User',1,'auth_token','99ea651978d82c306528db5a5ea2cd50deffcb75ad67c9e6c23beb926e5ef57e','[\"*\"]',NULL,NULL,'2025-06-11 23:30:15','2025-06-11 23:30:15'),
(50,'App\\Models\\User',1,'auth_token','124862edb71d79251c0f08ec8e36891bb5b3ecc85ec3ab087e02fbeb268e71cb','[\"*\"]',NULL,NULL,'2025-06-11 23:34:14','2025-06-11 23:34:14'),
(51,'App\\Models\\User',1,'auth_token','3abf15507195cefb4a2406f51da0b1cfbbf4f2f8f7c79f335c6d9cd7546ba147','[\"*\"]',NULL,NULL,'2025-06-11 23:35:43','2025-06-11 23:35:43'),
(52,'App\\Models\\User',1,'auth_token','968c763b9751bd79842fd8b87cfc7bd36782f1ca1bd2252f0c01f0625e770920','[\"*\"]',NULL,NULL,'2025-06-11 23:42:16','2025-06-11 23:42:16'),
(53,'App\\Models\\User',1,'auth_token','d92fdd7edab2a030840084f2afeb8ed841ad28ace57d967b643e0e07fdf14953','[\"*\"]',NULL,NULL,'2025-06-11 23:43:52','2025-06-11 23:43:52'),
(54,'App\\Models\\User',1,'auth_token','0c41f2f46384dde9a77629ab66e9352d56f623dfad94e0554b010a6eca84b5dd','[\"*\"]',NULL,NULL,'2025-06-11 23:51:17','2025-06-11 23:51:17'),
(55,'App\\Models\\User',1,'auth_token','dd1372368651de9b6641fec66bae7eadf8597c0a65db9b5e54019d6c65400b39','[\"*\"]',NULL,NULL,'2025-06-11 23:53:59','2025-06-11 23:53:59'),
(56,'App\\Models\\User',1,'auth_token','c1b74d6c3091621e76fd819435099b328d3484235aba7c750c38f09bfa956fb4','[\"*\"]',NULL,NULL,'2025-06-11 23:58:32','2025-06-11 23:58:32'),
(57,'App\\Models\\User',2,'auth_token','7fda53bc2c0e3d08f438a055e1dd020eb20181daa007e178372364d7fe522ce7','[\"*\"]',NULL,NULL,'2025-06-12 00:04:35','2025-06-12 00:04:35'),
(58,'App\\Models\\User',1,'auth_token','4bda681b1ab28df1505de0c10ae7a90f54a4e67a7e53fa699603fab679e58837','[\"*\"]',NULL,NULL,'2025-06-12 00:14:33','2025-06-12 00:14:33'),
(59,'App\\Models\\User',1,'auth_token','0f973b79f09aa9b5350d3e6c3d9f8bf664a367235abb06e26e76d2c01025d899','[\"*\"]',NULL,NULL,'2025-06-12 00:26:42','2025-06-12 00:26:42'),
(60,'App\\Models\\User',1,'auth_token','31bf57f167434a8c66bc8b0a5b04a53d27c2124a1be48568a508aeb7e49eb344','[\"*\"]',NULL,NULL,'2025-06-12 00:30:46','2025-06-12 00:30:46');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `status_lapangan` */

insert  into `status_lapangan`(`id_status`,`id_lapangan`,`deskripsi_status`,`tanggal`,`id_sesi`,`created_at`,`updated_at`) values 
(1,1,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(2,2,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(3,3,'tersedia',NULL,NULL,'2025-05-22 21:04:37','2025-05-22 21:04:37'),
(5,2,'disewa','2025-05-23','6','2025-05-23 13:31:07','2025-05-23 13:31:07'),
(6,2,'disewa','2025-05-23','7','2025-05-23 13:31:07','2025-05-23 13:31:07'),
(9,3,'disewa','2025-05-23','9','2025-05-23 15:01:16','2025-05-23 15:01:16'),
(10,3,'disewa','2025-05-23','10','2025-05-23 15:01:16','2025-05-23 15:01:16'),
(11,2,'disewa','2025-05-23','12','2025-05-23 18:39:38','2025-05-23 18:39:38'),
(12,1,'disewa','2025-06-11','7','2025-06-11 14:13:51','2025-06-11 14:13:51'),
(13,1,'disewa','2025-06-11','8','2025-06-11 14:13:51','2025-06-11 14:13:51'),
(14,3,'disewa','2025-06-11','8','2025-06-11 14:15:01','2025-06-11 14:15:01'),
(15,2,'tersedia','2025-06-11','7','2025-06-11 14:23:12','2025-06-11 23:27:30'),
(16,2,'disewa','2025-06-11','8','2025-06-11 14:23:12','2025-06-11 14:23:12'),
(17,4,'tersedia','2025-06-11','12','2025-06-11 14:39:43','2025-06-11 23:27:39'),
(18,4,'disewa','2025-06-11','11','2025-06-11 14:39:43','2025-06-11 14:39:43'),
(19,2,'disewa','2025-06-11','12','2025-06-11 19:41:22','2025-06-11 19:41:22'),
(20,4,'tersedia','2025-06-12','1','2025-06-11 23:24:49','2025-06-12 00:32:10'),
(21,4,'disewa','2025-06-12','2','2025-06-11 23:24:49','2025-06-11 23:24:49'),
(22,4,'disewa','2025-06-12','8','2025-06-12 00:13:48','2025-06-12 00:13:48');

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
