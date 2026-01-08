-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jan 2026 pada 14.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aiot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absen`
--

CREATE TABLE `absen` (
  `id` int(11) NOT NULL,
  `NIM` varchar(20) DEFAULT NULL,
  `Nama` varchar(100) DEFAULT NULL,
  `PBL` varchar(100) DEFAULT NULL,
  `absen_hadir` datetime DEFAULT NULL,
  `absen_pulang` datetime DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status_kehadiran` varchar(30) DEFAULT NULL,
  `durasi_kerja` decimal(5,2) DEFAULT NULL,
  `status_masuk` varchar(20) DEFAULT NULL,
  `durasi_jam` decimal(5,2) DEFAULT NULL,
  `kategori_durasi` varchar(20) DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absen`
--

INSERT INTO `absen` (`id`, `NIM`, `Nama`, `PBL`, `absen_hadir`, `absen_pulang`, `tanggal`, `status_kehadiran`, `durasi_kerja`, `status_masuk`, `durasi_jam`, `kategori_durasi`, `bukti_foto`) VALUES
(30, '229', 'Jari Jempol', 'MALAS', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(31, '222', 'Jari Telunjuk', 'MALAS', '2025-07-07 13:48:55', '2025-07-07 16:15:03', '2025-07-07', 'Hadir', 2.44, 'Terlambat', 2.44, 'Short', NULL),
(32, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(33, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(34, '4222201021', 'Revino Jantri Putra', 'MALAS', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(35, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(36, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-07-07', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(40, '222', 'Jari Telunjuk', 'MALAS', '2025-07-08 08:00:00', '2025-07-08 12:39:25', '2025-07-08', 'Hadir', 4.66, 'Tepat Waktu', 4.66, 'Half Day', NULL),
(41, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-07-08', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(42, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-07-08 08:00:00', '2025-07-08 11:00:20', '2025-07-08', 'Hadir', 3.01, 'Tepat Waktu', 3.01, 'Short', NULL),
(43, '4222201021', 'Revino Jantri Putra', 'MALAS', NULL, NULL, '2025-07-08', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(44, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-07-08 09:49:37', NULL, '2025-07-08', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(45, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-07-08', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(46, '229', 'Jari Jempol', 'MALAS', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(47, '222', 'Jari Telunjuk', 'MALAS', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(48, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(49, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(50, '4222201021', 'Revino Jantri Putra', 'MALAS', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(51, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(52, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-07-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(53, '229', 'Jari Jempol', 'MALAS', '2025-07-10 13:22:23', '2025-07-10 13:23:31', '2025-07-10', 'Hadir', 0.02, 'Terlambat', 0.02, 'Short', NULL),
(54, '222', 'Jari Telunjuk', 'MALAS', '2025-07-10 13:21:10', NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(55, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(56, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', NULL, NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(57, '4222201021', 'Revino Jantri Putra', 'MALAS', NULL, NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(58, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', NULL, NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(59, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-07-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(60, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-12-08 17:13:56', NULL, '2025-12-08', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(67, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', NULL, NULL, '2025-12-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(68, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-12-09 13:51:26', NULL, '2025-12-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, 'kehadiran/4222201044_20251209_135126.jpg'),
(69, '229', 'Jari Jempol', 'MALAS', '2025-10-10 07:44:58', '2025-10-10 18:03:05', '2025-10-10', 'Hadir', 10.30, 'Tepat Waktu', 10.30, 'Full', NULL),
(70, '222', 'Jari Telunjuk', 'MALAS', NULL, NULL, '2025-10-10', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(71, '111', 'Jari Tengah', 'MALAS', '2025-10-10 08:15:21', '2025-10-10 19:32:43', '2025-10-10', 'Hadir', 11.29, 'Terlambat', 11.29, 'Full', NULL),
(72, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-10 07:58:29', '2025-10-10 17:23:12', '2025-10-10', 'Hadir', 9.41, 'Tepat Waktu', 9.41, 'Full', NULL),
(73, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-10 07:59:37', '2025-10-10 16:00:18', '2025-10-10', 'Hadir', 8.01, 'Tepat Waktu', 8.01, 'Full', NULL),
(74, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-10 07:58:35', '2025-10-10 19:45:45', '2025-10-10', 'Hadir', 11.79, 'Tepat Waktu', 11.79, 'Full', NULL),
(75, '12345678', 'godu', 'KIDO-KIDO', '2025-10-10 07:46:02', '2025-10-10 17:03:59', '2025-10-10', 'Hadir', 9.30, 'Tepat Waktu', 9.30, 'Full', NULL),
(76, '422201021', 'revinoo', 'SIMALAS', '2025-10-10 07:55:33', '2025-10-10 19:55:08', '2025-10-10', 'Hadir', 11.99, 'Tepat Waktu', 11.99, 'Full', NULL),
(77, '229', 'Jari Jempol', 'MALAS', '2025-10-13 07:34:24', '2025-10-13 16:42:03', '2025-10-13', 'Hadir', 9.13, 'Tepat Waktu', 9.13, 'Full', NULL),
(78, '222', 'Jari Telunjuk', 'MALAS', '2025-10-13 07:49:52', '2025-10-13 18:57:41', '2025-10-13', 'Hadir', 11.13, 'Tepat Waktu', 11.13, 'Full', NULL),
(79, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-10-13', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(80, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-13 08:29:41', '2025-10-13 16:52:38', '2025-10-13', 'Hadir', 8.38, 'Terlambat', 8.38, 'Full', NULL),
(81, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-13 07:54:27', '2025-10-13 18:34:04', '2025-10-13', 'Hadir', 10.66, 'Tepat Waktu', 10.66, 'Full', NULL),
(82, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-13 08:24:07', '2025-10-13 17:12:16', '2025-10-13', 'Hadir', 8.80, 'Terlambat', 8.80, 'Full', NULL),
(83, '12345678', 'godu', 'KIDO-KIDO', '2025-10-13 08:11:42', '2025-10-13 17:34:05', '2025-10-13', 'Hadir', 9.37, 'Terlambat', 9.37, 'Full', NULL),
(84, '422201021', 'revinoo', 'SIMALAS', '2025-10-13 07:31:44', '2025-10-13 19:56:27', '2025-10-13', 'Hadir', 12.41, 'Tepat Waktu', 12.41, 'Full', NULL),
(85, '229', 'Jari Jempol', 'MALAS', '2025-10-14 07:44:47', '2025-10-14 19:33:58', '2025-10-14', 'Hadir', 11.82, 'Tepat Waktu', 11.82, 'Full', NULL),
(86, '222', 'Jari Telunjuk', 'MALAS', '2025-10-14 08:19:33', '2025-10-14 18:39:59', '2025-10-14', 'Hadir', 10.34, 'Terlambat', 10.34, 'Full', NULL),
(87, '111', 'Jari Tengah', 'MALAS', '2025-10-14 07:39:22', '2025-10-14 17:07:09', '2025-10-14', 'Hadir', 9.46, 'Tepat Waktu', 9.46, 'Full', NULL),
(88, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-14 08:27:43', '2025-10-14 19:21:46', '2025-10-14', 'Hadir', 10.90, 'Terlambat', 10.90, 'Full', NULL),
(89, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-14 07:51:52', '2025-10-14 17:30:14', '2025-10-14', 'Hadir', 9.64, 'Tepat Waktu', 9.64, 'Full', NULL),
(90, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-14 07:45:32', '2025-10-14 18:04:31', '2025-10-14', 'Hadir', 10.32, 'Tepat Waktu', 10.32, 'Full', NULL),
(91, '12345678', 'godu', 'KIDO-KIDO', '2025-10-14 08:11:09', '2025-10-14 16:09:11', '2025-10-14', 'Hadir', 7.97, 'Terlambat', 7.97, 'Half Day', NULL),
(92, '422201021', 'revinoo', 'SIMALAS', '2025-10-14 08:44:35', '2025-10-14 17:53:47', '2025-10-14', 'Hadir', 9.15, 'Terlambat', 9.15, 'Full', NULL),
(93, '229', 'Jari Jempol', 'MALAS', NULL, NULL, '2025-10-15', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(94, '222', 'Jari Telunjuk', 'MALAS', NULL, NULL, '2025-10-15', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(95, '111', 'Jari Tengah', 'MALAS', '2025-10-15 08:03:16', '2025-10-15 18:28:04', '2025-10-15', 'Hadir', 10.41, 'Terlambat', 10.41, 'Full', NULL),
(96, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-15 08:27:51', '2025-10-15 18:55:59', '2025-10-15', 'Hadir', 10.47, 'Terlambat', 10.47, 'Full', NULL),
(97, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-15 07:46:00', '2025-10-15 17:13:20', '2025-10-15', 'Hadir', 9.46, 'Tepat Waktu', 9.46, 'Full', NULL),
(98, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-15 07:45:57', '2025-10-15 16:40:37', '2025-10-15', 'Hadir', 8.91, 'Tepat Waktu', 8.91, 'Full', NULL),
(99, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-10-15', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(100, '422201021', 'revinoo', 'SIMALAS', '2025-10-15 07:54:25', '2025-10-15 17:55:04', '2025-10-15', 'Hadir', 10.01, 'Tepat Waktu', 10.01, 'Full', NULL),
(101, '229', 'Jari Jempol', 'MALAS', '2025-10-16 07:50:31', '2025-10-16 18:53:13', '2025-10-16', 'Hadir', 11.05, 'Tepat Waktu', 11.05, 'Full', NULL),
(102, '222', 'Jari Telunjuk', 'MALAS', '2025-10-16 07:44:48', '2025-10-16 18:48:15', '2025-10-16', 'Hadir', 11.06, 'Tepat Waktu', 11.06, 'Full', NULL),
(103, '111', 'Jari Tengah', 'MALAS', '2025-10-16 07:47:27', '2025-10-16 17:07:57', '2025-10-16', 'Hadir', 9.34, 'Tepat Waktu', 9.34, 'Full', NULL),
(104, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-16 07:45:04', '2025-10-16 16:20:53', '2025-10-16', 'Hadir', 8.60, 'Tepat Waktu', 8.60, 'Full', NULL),
(105, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-16 07:46:48', '2025-10-16 19:43:19', '2025-10-16', 'Hadir', 11.94, 'Tepat Waktu', 11.94, 'Full', NULL),
(106, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-16 08:28:51', '2025-10-16 18:13:50', '2025-10-16', 'Hadir', 9.75, 'Terlambat', 9.75, 'Full', NULL),
(107, '12345678', 'godu', 'KIDO-KIDO', '2025-10-16 07:57:30', '2025-10-16 19:41:56', '2025-10-16', 'Hadir', 11.74, 'Tepat Waktu', 11.74, 'Full', NULL),
(108, '422201021', 'revinoo', 'SIMALAS', NULL, NULL, '2025-10-16', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(109, '229', 'Jari Jempol', 'MALAS', NULL, NULL, '2025-10-17', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(110, '222', 'Jari Telunjuk', 'MALAS', '2025-10-17 07:38:23', '2025-10-17 18:30:18', '2025-10-17', 'Hadir', 10.87, 'Tepat Waktu', 10.87, 'Full', NULL),
(111, '111', 'Jari Tengah', 'MALAS', '2025-10-17 07:31:20', '2025-10-17 17:18:32', '2025-10-17', 'Hadir', 9.79, 'Tepat Waktu', 9.79, 'Full', NULL),
(112, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-17 07:51:52', '2025-10-17 18:29:04', '2025-10-17', 'Hadir', 10.62, 'Tepat Waktu', 10.62, 'Full', NULL),
(113, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-17 07:40:01', '2025-10-17 16:33:53', '2025-10-17', 'Hadir', 8.90, 'Tepat Waktu', 8.90, 'Full', NULL),
(114, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-17 07:54:53', '2025-10-17 19:35:35', '2025-10-17', 'Hadir', 11.68, 'Tepat Waktu', 11.68, 'Full', NULL),
(115, '12345678', 'godu', 'KIDO-KIDO', '2025-10-17 07:48:36', '2025-10-17 18:10:39', '2025-10-17', 'Hadir', 10.37, 'Tepat Waktu', 10.37, 'Full', NULL),
(116, '422201021', 'revinoo', 'SIMALAS', '2025-10-17 07:43:46', '2025-10-17 19:33:04', '2025-10-17', 'Hadir', 11.82, 'Tepat Waktu', 11.82, 'Full', NULL),
(117, '229', 'Jari Jempol', 'MALAS', '2025-10-20 07:55:38', '2025-10-20 19:24:37', '2025-10-20', 'Hadir', 11.48, 'Tepat Waktu', 11.48, 'Full', NULL),
(118, '222', 'Jari Telunjuk', 'MALAS', '2025-10-20 07:30:07', '2025-10-20 17:34:20', '2025-10-20', 'Hadir', 10.07, 'Tepat Waktu', 10.07, 'Full', NULL),
(119, '111', 'Jari Tengah', 'MALAS', '2025-10-20 07:51:45', '2025-10-20 17:04:14', '2025-10-20', 'Hadir', 9.21, 'Tepat Waktu', 9.21, 'Full', NULL),
(120, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-20 07:38:40', '2025-10-20 17:55:26', '2025-10-20', 'Hadir', 10.28, 'Tepat Waktu', 10.28, 'Full', NULL),
(121, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-20 07:42:06', '2025-10-20 19:11:42', '2025-10-20', 'Hadir', 11.49, 'Tepat Waktu', 11.49, 'Full', NULL),
(122, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-20 08:24:14', '2025-10-20 19:27:39', '2025-10-20', 'Hadir', 11.06, 'Terlambat', 11.06, 'Full', NULL),
(123, '12345678', 'godu', 'KIDO-KIDO', '2025-10-20 07:51:48', '2025-10-20 17:01:03', '2025-10-20', 'Hadir', 9.15, 'Tepat Waktu', 9.15, 'Full', NULL),
(124, '422201021', 'revinoo', 'SIMALAS', '2025-10-20 07:35:23', '2025-10-20 16:11:43', '2025-10-20', 'Hadir', 8.61, 'Tepat Waktu', 8.61, 'Full', NULL),
(125, '229', 'Jari Jempol', 'MALAS', '2025-10-21 07:38:59', '2025-10-21 18:40:16', '2025-10-21', 'Hadir', 11.02, 'Tepat Waktu', 11.02, 'Full', NULL),
(126, '222', 'Jari Telunjuk', 'MALAS', '2025-10-21 08:01:45', '2025-10-21 16:00:51', '2025-10-21', 'Hadir', 7.99, 'Terlambat', 7.99, 'Half Day', NULL),
(127, '111', 'Jari Tengah', 'MALAS', '2025-10-21 07:52:05', '2025-10-21 18:55:24', '2025-10-21', 'Hadir', 11.06, 'Tepat Waktu', 11.06, 'Full', NULL),
(128, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-21 08:19:05', '2025-10-21 19:17:02', '2025-10-21', 'Hadir', 10.97, 'Terlambat', 10.97, 'Full', NULL),
(129, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-21 07:52:24', '2025-10-21 16:22:56', '2025-10-21', 'Hadir', 8.51, 'Tepat Waktu', 8.51, 'Full', NULL),
(130, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-21 07:32:39', '2025-10-21 19:11:07', '2025-10-21', 'Hadir', 11.64, 'Tepat Waktu', 11.64, 'Full', NULL),
(131, '12345678', 'godu', 'KIDO-KIDO', '2025-10-21 08:39:10', '2025-10-21 19:02:49', '2025-10-21', 'Hadir', 10.39, 'Terlambat', 10.39, 'Full', NULL),
(132, '422201021', 'revinoo', 'SIMALAS', '2025-10-21 07:42:19', '2025-10-21 19:25:53', '2025-10-21', 'Hadir', 11.73, 'Tepat Waktu', 11.73, 'Full', NULL),
(133, '229', 'Jari Jempol', 'MALAS', '2025-10-22 07:39:39', '2025-10-22 17:32:58', '2025-10-22', 'Hadir', 9.89, 'Tepat Waktu', 9.89, 'Full', NULL),
(134, '222', 'Jari Telunjuk', 'MALAS', '2025-10-22 07:58:24', '2025-10-22 17:08:35', '2025-10-22', 'Hadir', 9.17, 'Tepat Waktu', 9.17, 'Full', NULL),
(135, '111', 'Jari Tengah', 'MALAS', '2025-10-22 07:54:39', '2025-10-22 17:31:02', '2025-10-22', 'Hadir', 9.61, 'Tepat Waktu', 9.61, 'Full', NULL),
(136, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-22 07:49:29', '2025-10-22 16:37:05', '2025-10-22', 'Hadir', 8.79, 'Tepat Waktu', 8.79, 'Full', NULL),
(137, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-22 07:39:33', '2025-10-22 18:14:35', '2025-10-22', 'Hadir', 10.58, 'Tepat Waktu', 10.58, 'Full', NULL),
(138, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-22 08:42:49', '2025-10-22 19:07:51', '2025-10-22', 'Hadir', 10.42, 'Terlambat', 10.42, 'Full', NULL),
(139, '12345678', 'godu', 'KIDO-KIDO', '2025-10-22 08:43:32', '2025-10-22 19:51:07', '2025-10-22', 'Hadir', 11.13, 'Terlambat', 11.13, 'Full', NULL),
(140, '422201021', 'revinoo', 'SIMALAS', '2025-10-22 07:38:39', '2025-10-22 18:47:53', '2025-10-22', 'Hadir', 11.15, 'Tepat Waktu', 11.15, 'Full', NULL),
(141, '229', 'Jari Jempol', 'MALAS', '2025-10-23 07:36:54', '2025-10-23 16:20:08', '2025-10-23', 'Hadir', 8.72, 'Tepat Waktu', 8.72, 'Full', NULL),
(142, '222', 'Jari Telunjuk', 'MALAS', NULL, NULL, '2025-10-23', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(143, '111', 'Jari Tengah', 'MALAS', '2025-10-23 07:45:23', '2025-10-23 19:05:29', '2025-10-23', 'Hadir', 11.34, 'Tepat Waktu', 11.34, 'Full', NULL),
(144, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-23 07:53:54', '2025-10-23 19:53:30', '2025-10-23', 'Hadir', 11.99, 'Tepat Waktu', 11.99, 'Full', NULL),
(145, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-23 07:39:33', '2025-10-23 16:18:17', '2025-10-23', 'Hadir', 8.65, 'Tepat Waktu', 8.65, 'Full', NULL),
(146, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-23 07:32:36', '2025-10-23 17:48:08', '2025-10-23', 'Hadir', 10.26, 'Tepat Waktu', 10.26, 'Full', NULL),
(147, '12345678', 'godu', 'KIDO-KIDO', NULL, NULL, '2025-10-23', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(148, '422201021', 'revinoo', 'SIMALAS', '2025-10-23 07:35:22', '2025-10-23 16:38:37', '2025-10-23', 'Hadir', 9.05, 'Tepat Waktu', 9.05, 'Full', NULL),
(149, '229', 'Jari Jempol', 'MALAS', '2025-10-24 08:34:06', '2025-10-24 16:35:36', '2025-10-24', 'Hadir', 8.03, 'Terlambat', 8.03, 'Full', NULL),
(150, '222', 'Jari Telunjuk', 'MALAS', NULL, NULL, '2025-10-24', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(151, '111', 'Jari Tengah', 'MALAS', '2025-10-24 07:43:17', '2025-10-24 16:01:13', '2025-10-24', 'Hadir', 8.30, 'Tepat Waktu', 8.30, 'Full', NULL),
(152, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-24 08:23:50', '2025-10-24 17:02:09', '2025-10-24', 'Hadir', 8.64, 'Terlambat', 8.64, 'Full', NULL),
(153, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-24 07:57:27', '2025-10-24 17:22:17', '2025-10-24', 'Hadir', 9.41, 'Tepat Waktu', 9.41, 'Full', NULL),
(154, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', NULL, NULL, '2025-10-24', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(155, '12345678', 'godu', 'KIDO-KIDO', '2025-10-24 08:16:10', '2025-10-24 17:34:39', '2025-10-24', 'Hadir', 9.31, 'Terlambat', 9.31, 'Full', NULL),
(156, '422201021', 'revinoo', 'SIMALAS', '2025-10-24 08:44:33', '2025-10-24 18:24:26', '2025-10-24', 'Hadir', 9.66, 'Terlambat', 9.66, 'Full', NULL),
(157, '229', 'Jari Jempol', 'MALAS', '2025-10-27 08:33:32', '2025-10-27 18:37:37', '2025-10-27', 'Hadir', 10.07, 'Terlambat', 10.07, 'Full', NULL),
(158, '222', 'Jari Telunjuk', 'MALAS', '2025-10-27 08:18:23', '2025-10-27 19:42:04', '2025-10-27', 'Hadir', 11.39, 'Terlambat', 11.39, 'Full', NULL),
(159, '111', 'Jari Tengah', 'MALAS', '2025-10-27 08:16:32', '2025-10-27 18:47:19', '2025-10-27', 'Hadir', 10.51, 'Terlambat', 10.51, 'Full', NULL),
(160, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-27 07:52:16', '2025-10-27 19:10:39', '2025-10-27', 'Hadir', 11.31, 'Tepat Waktu', 11.31, 'Full', NULL),
(161, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-27 07:54:19', '2025-10-27 19:42:26', '2025-10-27', 'Hadir', 11.80, 'Tepat Waktu', 11.80, 'Full', NULL),
(162, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', NULL, NULL, '2025-10-27', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(163, '12345678', 'godu', 'KIDO-KIDO', '2025-10-27 08:44:19', '2025-10-27 17:39:13', '2025-10-27', 'Hadir', 8.92, 'Terlambat', 8.92, 'Full', NULL),
(164, '422201021', 'revinoo', 'SIMALAS', '2025-10-27 07:37:37', '2025-10-27 19:10:14', '2025-10-27', 'Hadir', 11.54, 'Tepat Waktu', 11.54, 'Full', NULL),
(165, '229', 'Jari Jempol', 'MALAS', '2025-10-28 07:31:39', '2025-10-28 17:59:10', '2025-10-28', 'Hadir', 10.46, 'Tepat Waktu', 10.46, 'Full', NULL),
(166, '222', 'Jari Telunjuk', 'MALAS', '2025-10-28 07:38:29', '2025-10-28 19:12:32', '2025-10-28', 'Hadir', 11.57, 'Tepat Waktu', 11.57, 'Full', NULL),
(167, '111', 'Jari Tengah', 'MALAS', '2025-10-28 08:34:28', '2025-10-28 17:56:56', '2025-10-28', 'Hadir', 9.37, 'Terlambat', 9.37, 'Full', NULL),
(168, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-28 08:18:29', '2025-10-28 18:24:26', '2025-10-28', 'Hadir', 10.10, 'Terlambat', 10.10, 'Full', NULL),
(169, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-28 08:32:53', '2025-10-28 17:10:04', '2025-10-28', 'Hadir', 8.62, 'Terlambat', 8.62, 'Full', NULL),
(170, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-28 07:38:09', '2025-10-28 19:42:12', '2025-10-28', 'Hadir', 12.07, 'Tepat Waktu', 12.07, 'Full', NULL),
(171, '12345678', 'godu', 'KIDO-KIDO', '2025-10-28 07:32:29', '2025-10-28 18:32:59', '2025-10-28', 'Hadir', 11.01, 'Tepat Waktu', 11.01, 'Full', NULL),
(172, '422201021', 'revinoo', 'SIMALAS', '2025-10-28 07:35:54', '2025-10-28 18:27:52', '2025-10-28', 'Hadir', 10.87, 'Tepat Waktu', 10.87, 'Full', NULL),
(173, '229', 'Jari Jempol', 'MALAS', '2025-10-29 08:33:14', '2025-10-29 17:34:36', '2025-10-29', 'Hadir', 9.02, 'Terlambat', 9.02, 'Full', NULL),
(174, '222', 'Jari Telunjuk', 'MALAS', '2025-10-29 07:54:14', '2025-10-29 18:22:42', '2025-10-29', 'Hadir', 10.47, 'Tepat Waktu', 10.47, 'Full', NULL),
(175, '111', 'Jari Tengah', 'MALAS', '2025-10-29 07:33:04', '2025-10-29 19:40:54', '2025-10-29', 'Hadir', 12.13, 'Tepat Waktu', 12.13, 'Full', NULL),
(176, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-29 07:54:02', '2025-10-29 17:28:16', '2025-10-29', 'Hadir', 9.57, 'Tepat Waktu', 9.57, 'Full', NULL),
(177, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-29 08:33:33', '2025-10-29 19:30:57', '2025-10-29', 'Hadir', 10.96, 'Terlambat', 10.96, 'Full', NULL),
(178, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-29 07:38:52', '2025-10-29 18:24:20', '2025-10-29', 'Hadir', 10.76, 'Tepat Waktu', 10.76, 'Full', NULL),
(179, '12345678', 'godu', 'KIDO-KIDO', '2025-10-29 07:59:36', '2025-10-29 17:59:19', '2025-10-29', 'Hadir', 10.00, 'Tepat Waktu', 10.00, 'Full', NULL),
(180, '422201021', 'revinoo', 'SIMALAS', '2025-10-29 07:49:18', '2025-10-29 19:27:14', '2025-10-29', 'Hadir', 11.63, 'Tepat Waktu', 11.63, 'Full', NULL),
(181, '229', 'Jari Jempol', 'MALAS', '2025-10-30 08:18:24', '2025-10-30 18:22:20', '2025-10-30', 'Hadir', 10.07, 'Terlambat', 10.07, 'Full', NULL),
(182, '222', 'Jari Telunjuk', 'MALAS', '2025-10-30 07:37:37', '2025-10-30 19:33:39', '2025-10-30', 'Hadir', 11.93, 'Tepat Waktu', 11.93, 'Full', NULL),
(183, '111', 'Jari Tengah', 'MALAS', '2025-10-30 08:32:03', '2025-10-30 18:37:34', '2025-10-30', 'Hadir', 10.09, 'Terlambat', 10.09, 'Full', NULL),
(184, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-30 08:30:17', '2025-10-30 18:22:20', '2025-10-30', 'Hadir', 9.87, 'Terlambat', 9.87, 'Full', NULL),
(185, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-30 07:33:14', '2025-10-30 17:34:36', '2025-10-30', 'Hadir', 10.02, 'Tepat Waktu', 10.02, 'Full', NULL),
(186, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-30 08:11:13', '2025-10-30 18:39:10', '2025-10-30', 'Hadir', 10.47, 'Terlambat', 10.47, 'Full', NULL),
(187, '12345678', 'godu', 'KIDO-KIDO', '2025-10-30 08:24:50', '2025-10-30 19:24:19', '2025-10-30', 'Hadir', 10.99, 'Terlambat', 10.99, 'Full', NULL),
(188, '422201021', 'revinoo', 'SIMALAS', '2025-10-30 08:18:24', '2025-10-30 19:40:24', '2025-10-30', 'Hadir', 11.37, 'Terlambat', 11.37, 'Full', NULL),
(189, '229', 'Jari Jempol', 'MALAS', '2025-10-31 08:24:32', '2025-10-31 18:43:53', '2025-10-31', 'Hadir', 10.32, 'Terlambat', 10.32, 'Full', NULL),
(190, '222', 'Jari Telunjuk', 'MALAS', '2025-10-31 07:34:10', '2025-10-31 17:28:34', '2025-10-31', 'Hadir', 9.91, 'Tepat Waktu', 9.91, 'Full', NULL),
(191, '111', 'Jari Tengah', 'MALAS', NULL, NULL, '2025-10-31', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, NULL),
(192, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-10-31 07:54:19', '2025-10-31 18:32:20', '2025-10-31', 'Hadir', 10.63, 'Tepat Waktu', 10.63, 'Full', NULL),
(193, '4222201021', 'Revino Jantri Putra', 'MALAS', '2025-10-31 08:29:43', '2025-10-31 17:15:37', '2025-10-31', 'Hadir', 8.77, 'Terlambat', 8.77, 'Full', NULL),
(194, '4222201060', 'Dhaniel Ganteng', 'KRBAI UNDERWATER', '2025-10-31 07:52:13', '2025-10-31 18:19:10', '2025-10-31', 'Hadir', 10.45, 'Tepat Waktu', 10.45, 'Full', NULL),
(195, '12345678', 'godu', 'KIDO-KIDO', '2025-10-31 07:37:37', '2025-10-31 18:32:17', '2025-10-31', 'Hadir', 10.91, 'Tepat Waktu', 10.91, 'Full', NULL),
(196, '422201021', 'revinoo', 'SIMALAS', '2025-10-31 07:33:43', '2025-10-31 18:23:24', '2025-10-31', 'Hadir', 10.83, 'Tepat Waktu', 10.83, 'Full', NULL),
(197, '4222201048', '4222201048', 'Imported', '2025-12-09 19:31:30', NULL, '2025-12-09', 'Tidak Hadir', NULL, 'Absent', NULL, NULL, 'kehadiran/4222201048_20251209_193130.jpg'),
(198, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-12-18 15:00:13', NULL, '2025-12-18', 'Hadir', NULL, 'Terlambat', NULL, NULL, NULL),
(199, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2026-01-06 15:19:47', NULL, '2026-01-06', 'Hadir', NULL, 'Terlambat', NULL, NULL, 'kehadiran/4222201044_20260106_151947.jpg'),
(200, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2026-01-07 09:08:36', NULL, '2026-01-07', 'Hadir', NULL, 'Tepat Waktu', NULL, NULL, 'kehadiran/4222201044_20260107_090836.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `http_log`
--

CREATE TABLE `http_log` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `activity_type` varchar(50) DEFAULT 'Pendaftaran',
  `latency_ms` float DEFAULT 0,
  `jitter_ms` float DEFAULT 0,
  `throughput_kbps` float DEFAULT 0,
  `packet_loss_percent` float DEFAULT 0,
  `total_packets` int(11) DEFAULT 0,
  `received_packets` int(11) DEFAULT 0,
  `total_bytes` int(11) DEFAULT 0,
  `lost_bytes` int(11) DEFAULT 0,
  `status_code` int(11) DEFAULT 0,
  `request_url` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `http_log`
--

INSERT INTO `http_log` (`id`, `user_id`, `activity_type`, `latency_ms`, `jitter_ms`, `throughput_kbps`, `packet_loss_percent`, `total_packets`, `received_packets`, `total_bytes`, `lost_bytes`, `status_code`, `request_url`, `note`, `created_at`, `timestamp`) VALUES
(1, '999', 'Verifikasi', 379, 379, 34.3, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:11:39', '2025-10-05 15:13:27'),
(2, '12', 'Pendaftaran', 90, 289, 144.36, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:03', '2025-10-05 15:14:03'),
(3, '12', 'Pendaftaran', 311, 221, 41.77, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:10', '2025-10-05 15:14:10'),
(4, '12', 'Pendaftaran', 189, 122, 68.74, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:15', '2025-10-05 15:14:15'),
(5, '12', 'Pendaftaran', 394, 205, 32.97, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:25', '2025-10-05 15:14:25'),
(6, '12', 'Pendaftaran', 125, 269, 103.94, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:34', '2025-10-05 15:14:34'),
(7, '12', 'Pendaftaran', 249, 124, 52.18, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:14:42', '2025-10-05 15:14:42'),
(8, '999', 'Verifikasi', 438, 189, 29.68, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:15:38', '2025-10-05 15:15:38'),
(9, '999', 'Verifikasi', 147, 291, 88.44, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:15:55', '2025-10-05 15:15:55'),
(10, '999', 'Verifikasi', 396, 249, 32.83, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:28:41', '2025-10-05 15:28:41'),
(11, '999', 'Verifikasi', 144, 252, 90.28, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-05 15:28:54', '2025-10-05 15:28:54'),
(12, '999', 'Verifikasi', 252, 252, 51.59, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-07 07:16:29', '2025-10-07 07:16:29'),
(13, '-1', 'Pendaftaran', 328, 76, 38.88, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 07:16:30', '2025-10-07 07:16:30'),
(14, '999', 'Verifikasi', 137, 191, 94.89, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-07 07:16:47', '2025-10-07 07:16:47'),
(15, '-1', 'Pendaftaran', 170, 33, 75.01, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 07:16:48', '2025-10-07 07:16:48'),
(16, '999', 'Verifikasi', 106, 64, 122.64, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-07 07:17:14', '2025-10-07 07:17:14'),
(17, '-1', 'Pendaftaran', 255, 149, 50.01, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 07:17:15', '2025-10-07 07:17:15'),
(18, '999', 'Verifikasi', 150, 105, 86.67, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-07 07:17:29', '2025-10-07 07:17:29'),
(19, '-1', 'Pendaftaran', 335, 185, 38.07, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 07:17:30', '2025-10-07 07:17:30'),
(20, '0', 'Pendaftaran', 176, 176, 73.86, 0, 6, 6, 768, 0, 200, 'SendFingerprint', 'OK', '2025-10-07 08:00:41', '2025-10-07 08:00:41'),
(21, '119', 'Pendaftaran', 148, 28, 86.92, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:00:42', '2025-10-07 08:00:42'),
(22, '0', 'Verifikasi', 827, 827, 15.72, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Latency tinggi (827 ms)', '2025-10-07 08:15:58', '2025-10-07 08:15:58'),
(23, '119', 'TopMatch', 199, 628, 64.64, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:15:59', '2025-10-07 08:15:59'),
(24, '119', 'Pendaftaran', 99, 100, 131.23, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:23', '2025-10-07 08:16:23'),
(25, '119', 'Pendaftaran', 93, 6, 139.7, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:29', '2025-10-07 08:16:29'),
(26, '119', 'Pendaftaran', 95, 2, 136.76, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:34', '2025-10-07 08:16:34'),
(27, '119', 'Pendaftaran', 107, 12, 121.42, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:41', '2025-10-07 08:16:41'),
(28, '119', 'Pendaftaran', 148, 41, 87.78, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:50', '2025-10-07 08:16:50'),
(29, '119', 'Pendaftaran', 58, 90, 224, 0, 6, 6, 768, 0, 200, 'Pendaftaran', 'OK', '2025-10-07 08:16:57', '2025-10-07 08:16:57'),
(30, '119', 'Verifikasi', 110, 52, 118.18, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:17:22', '2025-10-07 08:17:22'),
(31, '120', 'TopMatch', 296, 186, 43.51, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:17:24', '2025-10-07 08:17:24'),
(32, '120', 'Verifikasi', 125, 171, 104, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:17:45', '2025-10-07 08:17:45'),
(33, '119', 'TopMatch', 157, 32, 81.94, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:17:47', '2025-10-07 08:17:47'),
(34, '119', 'Verifikasi', 153, 4, 84.97, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:20:34', '2025-10-07 08:20:34'),
(35, '120', 'TopMatch', 174, 21, 74.02, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:20:35', '2025-10-07 08:20:35'),
(36, '120', 'Verifikasi', 75, 99, 173.33, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:21:31', '2025-10-07 08:21:31'),
(37, '122', 'TopMatch', 172, 97, 75.49, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:21:32', '2025-10-07 08:21:32'),
(38, '999', 'Verifikasi', 481, 481, 27.03, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:33:31', '2025-10-07 08:33:31'),
(39, '999', 'TopMatch', 637, 156, 20.22, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Latency tinggi (637 ms)', '2025-10-07 08:33:33', '2025-10-07 08:33:33'),
(40, '999', 'Verifikasi', 222, 415, 58.56, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK', '2025-10-07 08:35:46', '2025-10-07 08:35:46'),
(41, '999', 'TopMatch', 192, 30, 67.62, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK', '2025-10-07 08:35:47', '2025-10-07 08:35:47'),
(75, '999', 'Verifikasi', 55, 90, 236.36, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (236.36 kbps)', '2025-12-10 12:14:52', '2025-12-10 12:14:52'),
(76, '2', 'TopMatch', 188, 133, 68.43, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (68.43 kbps)', '2025-12-10 12:14:57', '2025-12-10 12:14:57'),
(77, '999', 'Verifikasi', 32, 156, 406.25, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (406.25 kbps)', '2025-12-10 12:15:06', '2025-12-10 12:15:06'),
(78, '472', 'TopMatch', 205, 173, 63.34, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (63.34 kbps)', '2025-12-10 12:15:10', '2025-12-10 12:15:10'),
(79, '999', 'Verifikasi', 42, 163, 309.52, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (309.52 kbps)', '2025-12-10 12:15:26', '2025-12-10 12:15:26'),
(80, '2', 'TopMatch', 123, 81, 104.59, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (104.59 kbps)', '2025-12-10 12:15:28', '2025-12-10 12:15:28'),
(81, '999', 'Verifikasi', 60, 63, 216.67, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (216.67 kbps)', '2025-12-10 12:15:42', '2025-12-10 12:15:42'),
(82, '2', 'TopMatch', 117, 57, 109.95, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (109.95 kbps)', '2025-12-10 12:15:45', '2025-12-10 12:15:45'),
(83, '999', 'Verifikasi', 70, 47, 185.71, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (185.71 kbps)', '2025-12-10 12:16:10', '2025-12-10 12:16:10'),
(84, '2', 'TopMatch', 113, 43, 113.84, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (113.84 kbps)', '2025-12-10 12:16:13', '2025-12-10 12:16:13'),
(85, '999', 'Verifikasi', 40, 73, 325, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (325 kbps)', '2025-12-10 12:16:32', '2025-12-10 12:16:32'),
(86, '2', 'TopMatch', 139, 99, 92.55, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (92.55 kbps)', '2025-12-10 12:16:35', '2025-12-10 12:16:35'),
(87, '999', 'Verifikasi', 50, 89, 260, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (260 kbps)', '2025-12-10 12:18:06', '2025-12-10 12:18:06'),
(88, '1', 'TopMatch', 188, 138, 68.34, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (68.34 kbps)', '2025-12-10 12:18:09', '2025-12-10 12:18:09'),
(89, '999', 'Verifikasi', 61, 127, 213.11, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (213.11 kbps)', '2025-12-10 12:18:25', '2025-12-10 12:18:25'),
(90, '12', 'TopMatch', 179, 118, 72.49, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (72.49 kbps)', '2025-12-10 12:18:28', '2025-12-10 12:18:28'),
(91, '999', 'Verifikasi', 39, 140, 333.33, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (333.33 kbps)', '2025-12-10 12:19:20', '2025-12-10 12:19:20'),
(92, '3', 'TopMatch', 178, 139, 72.18, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (72.18 kbps)', '2025-12-10 12:19:23', '2025-12-10 12:19:23'),
(93, '999', 'Verifikasi', 83, 95, 156.63, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (156.63 kbps)', '2025-12-10 12:19:55', '2025-12-10 12:19:55'),
(94, '2', 'TopMatch', 100, 17, 128.64, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (128.64 kbps)', '2025-12-10 12:20:00', '2025-12-10 12:20:00'),
(95, '999', 'Verifikasi', 80, 20, 162.5, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (162.5 kbps)', '2025-12-10 12:21:01', '2025-12-10 12:21:01'),
(96, '12', 'TopMatch', 151, 71, 85.93, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (85.93 kbps)', '2025-12-10 12:21:04', '2025-12-10 12:21:04'),
(103, '999', 'Verifikasi', 67, 26, 194.03, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (194.03 kbps)', '2025-12-11 05:28:35', '2025-12-11 05:28:35'),
(104, '3', 'TopMatch', 89, 22, 69.03, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (69.03 kbps)', '2025-12-11 05:28:36', '2025-12-11 05:28:36'),
(105, '999', 'Verifikasi', 29, 60, 448.28, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (448.28 kbps)', '2025-12-11 05:28:47', '2025-12-11 05:28:47'),
(106, '472', 'TopMatch', 174, 145, 35.31, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (35.31 kbps)', '2025-12-11 05:28:48', '2025-12-11 05:28:48'),
(107, '999', 'Verifikasi', 33, 141, 393.94, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (393.94 kbps)', '2025-12-11 05:29:01', '2025-12-11 05:29:01'),
(108, '12', 'TopMatch', 138, 105, 44.52, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (44.52 kbps)', '2025-12-11 05:29:02', '2025-12-11 05:29:02'),
(109, '999', 'Verifikasi', 41, 97, 317.07, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (317.07 kbps)', '2025-12-11 05:29:13', '2025-12-11 05:29:13'),
(110, '2', 'TopMatch', 110, 69, 55.85, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (55.85 kbps)', '2025-12-11 05:29:14', '2025-12-11 05:29:14'),
(111, '999', 'Verifikasi', 69, 41, 188.41, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (188.41 kbps)', '2025-12-11 05:29:26', '2025-12-11 05:29:26'),
(112, '1', 'TopMatch', 127, 58, 48.38, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (48.38 kbps)', '2025-12-11 05:29:27', '2025-12-11 05:29:27'),
(113, '999', 'Verifikasi', 53, 74, 245.28, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (245.28 kbps)', '2025-12-11 05:29:40', '2025-12-11 05:29:40'),
(114, '472', 'TopMatch', 123, 70, 49.95, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (49.95 kbps)', '2025-12-11 05:29:41', '2025-12-11 05:29:41'),
(115, '999', 'Verifikasi', 29, 94, 448.28, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (448.28 kbps)', '2025-12-11 05:30:09', '2025-12-11 05:30:09'),
(116, '2', 'TopMatch', 88, 59, 69.82, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (69.82 kbps)', '2025-12-11 05:30:11', '2025-12-11 05:30:11'),
(117, '999', 'Verifikasi', 49, 39, 265.31, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (265.31 kbps)', '2025-12-11 05:30:20', '2025-12-11 05:30:20'),
(118, '2', 'TopMatch', 175, 126, 35.11, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (35.11 kbps)', '2025-12-11 05:30:22', '2025-12-11 05:30:22'),
(119, '999', 'Verifikasi', 30, 145, 433.33, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (433.33 kbps)', '2025-12-11 05:30:43', '2025-12-11 05:30:43'),
(120, '12', 'TopMatch', 137, 107, 44.85, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (44.85 kbps)', '2025-12-11 05:30:45', '2025-12-11 05:30:45'),
(121, '999', 'Verifikasi', 199, 199, 65.33, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (65.33 kbps)', '2025-12-11 06:39:07', '2025-12-11 06:39:07'),
(122, '3', 'TopMatch', 168, 31, 36.57, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (36.57 kbps)', '2025-12-11 06:39:09', '2025-12-11 06:39:09'),
(123, '999', 'Verifikasi', 41, 127, 317.07, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (317.07 kbps)', '2025-12-11 06:39:20', '2025-12-11 06:39:20'),
(124, '2', 'TopMatch', 163, 122, 37.69, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (37.69 kbps)', '2025-12-11 06:39:21', '2025-12-11 06:39:21'),
(125, '999', 'Verifikasi', 44, 119, 295.45, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (295.45 kbps)', '2025-12-11 06:39:33', '2025-12-11 06:39:33'),
(126, '3', 'TopMatch', 221, 177, 27.8, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (27.8 kbps)', '2025-12-11 06:39:34', '2025-12-11 06:39:34'),
(127, '999', 'Verifikasi', 51, 170, 254.9, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (254.9 kbps)', '2025-12-11 06:39:46', '2025-12-11 06:39:46'),
(128, '1', 'TopMatch', 89, 38, 69.03, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (69.03 kbps)', '2025-12-11 06:39:47', '2025-12-11 06:39:47'),
(129, '999', 'Verifikasi', 55, 34, 236.36, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Throughput rendah (236.36 kbps)', '2025-12-11 06:40:04', '2025-12-11 06:40:04'),
(130, '2', 'TopMatch', 99, 44, 62.06, 0, 6, 6, 768, 0, 200, 'TopMatch', 'OK | Throughput rendah (62.06 kbps)', '2025-12-11 06:40:05', '2025-12-11 06:40:05'),
(131, '999', 'Verifikasi', 934, 934, 13.92, 0, 6, 6, 768, 0, 200, 'Verifikasi', 'OK | Delay sangat tinggi (934 ms) | Throughput rendah (13.92 kbps)', '2025-12-19 08:37:16', '2025-12-19 08:37:16'),
(132, '999', 'TopMatch', 5002, 4068, 0, 100, 6, 6, 768, 768, 500, 'TopMatch', 'FAILED | Delay sangat tinggi (5002 ms) | Packet loss parah (100%) | Throughput rendah (0 kbps) | Hilang 768 byte dari total 768 byte', '2025-12-19 08:37:22', '2025-12-19 08:37:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `locker_status`
--

CREATE TABLE `locker_status` (
  `id` int(11) NOT NULL,
  `locker_number` int(11) DEFAULT NULL,
  `PBL` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `locker_status`
--

INSERT INTO `locker_status` (`id`, `locker_number`, `PBL`) VALUES
(142, 3, ''),
(200, 2, ''),
(207, 1, 'MALAS'),
(255, 4, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `loker_terpilih`
--

CREATE TABLE `loker_terpilih` (
  `id` int(11) NOT NULL,
  `locker_number` int(11) DEFAULT NULL,
  `pbl` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `loker_terpilih`
--

INSERT INTO `loker_terpilih` (`id`, `locker_number`, `pbl`) VALUES
(1, 1, 'CDS'),
(2, 1, 'CDS'),
(3, 2, 'DCS');

-- --------------------------------------------------------

--
-- Struktur dari tabel `packet_log`
--

CREATE TABLE `packet_log` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `Template1` varbinary(1536) DEFAULT NULL,
  `Template2` varbinary(1536) DEFAULT NULL,
  `Template3` varbinary(1536) DEFAULT NULL,
  `Template4` varbinary(1536) DEFAULT NULL,
  `Template5` varbinary(1536) DEFAULT NULL,
  `Template6` varbinary(1536) DEFAULT NULL,
  `Template7` varbinary(1536) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `packet_log`
--

INSERT INTO `packet_log` (`id`, `user_id`, `Template1`, `Template2`, `Template3`, `Template4`, `Template5`, `Template6`, `Template7`, `created_at`) VALUES
(339, '999', 0x303330333633313230303031323030313939303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030313530303032303037353030303063663333336663663366666666666666666662626165616161616161396139393539363535353635343435343534343034353130343434343030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303533386563313565363361313030626532623235303564653639613935383165323862323036336536376236343133653537333663336265336533633835626533373237396262663438626134356266366333663032316636303430343366663661393232633163363931353937396334383236303331633439613930336264363761666330316136643266643639613030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030306430613030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030303030, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-27 12:28:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pbl`
--

CREATE TABLE `pbl` (
  `id` int(11) NOT NULL,
  `nama_pbl` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pbl`
--

INSERT INTO `pbl` (`id`, `nama_pbl`, `created_at`) VALUES
(1, 'AIOT', '2025-07-07 06:02:43'),
(2, 'KIDO-KIDO', '2025-07-07 06:02:43'),
(3, 'KRBAI UNDERWATER', '2025-07-07 06:02:43'),
(4, 'MALAS', '2025-07-07 06:02:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_izin`
--

CREATE TABLE `pengajuan_izin` (
  `id` int(11) NOT NULL,
  `NIM` varchar(20) NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `PBL` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_izin` enum('Sakit','Izin') NOT NULL,
  `keterangan` text NOT NULL,
  `bukti_dokumen` varchar(255) DEFAULT NULL,
  `status_approval` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan_izin`
--

INSERT INTO `pengajuan_izin` (`id`, `NIM`, `Nama`, `PBL`, `tanggal`, `jenis_izin`, `keterangan`, `bukti_dokumen`, `status_approval`, `created_at`, `updated_at`) VALUES
(1, '4222201044', 'Abdi Wijaya Sasmita', 'AIOT', '2025-12-09', 'Sakit', 'Demam Tinggi', 'dokumen_izin/8PHiXLanfFCF6n2aWpSlXuNCIXcNBrNuthWgB4HG.png', 'Disetujui', '2025-12-08 23:41:20', '2025-12-08 23:42:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` bigint(19) NOT NULL,
  `userid` bigint(19) NOT NULL,
  `Nama` varchar(50) NOT NULL,
  `NIM` varchar(20) NOT NULL,
  `PBL` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `Angkatan` varchar(100) DEFAULT NULL,
  `role` enum('admin','dosen','user') NOT NULL DEFAULT 'user',
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `userid`, `Nama`, `NIM`, `PBL`, `Password`, `email`, `gender`, `Angkatan`, `role`, `date`) VALUES
(119, 1, 'Jari Jempol', '229', 'MALAS', '123', 'tucol@gmail.com', 'Laki-L', '2023', 'admin', '2025-12-08 10:30:46'),
(120, 2, 'Jari Telunjuk', '222', 'MALAS', '$2y$10$ajl4tiH1inf1fD0LMS9th.cl3bp2Rlh9iod9P/AdaPzNlZpl/iiiy', '2tucol@gmail.com', 'Laki-L', '2023', 'user', '2025-07-07 06:40:09'),
(121, 3, 'Jari Tengah', '111', 'MALAS', '$2y$10$eNR6Hsl2zxGaCbcQCD8Jv.MxHkgJn8WOue13tHB6BHxmRoaN6uUGi', '12tucol@gmail.com', 'Laki-L', '2021', 'user', '2025-07-05 05:41:55'),
(122, 12, 'Abdi Wijaya Sasmita', '4222201044', 'AIOT', '$2y$12$HN6fBED3Rg9v28YqeeaPCek/eKY4K7tJMMp4w.x1.VGwaGOtxbG0.', 'abdiwijayas1@gmail.com', 'Laki-L', '2024', 'user', '2025-12-10 11:20:52'),
(123, 4, 'Revino Jantri Putra', '4222201021', 'MALAS', '$2y$10$BuxDZUqRDGOJ4zYHPIfsvubIsG8Nup3rklpdDgwE/nr1l8MdUJNSO', 'revino1234@gmail.com', 'Laki-L', '2023', 'user', '2025-12-18 03:39:06'),
(124, 7, 'Dhaniel Ganteng', '4222201060', 'KRBAI UNDERWATER', '$2y$10$EYTsIwiP2AQRkzwQUbIfsuUFhSFYaj.W71BSUVILw1Mq75wdAs8Ai', 'aingdhaniel@gmail.com', 'Laki-L', '2022', 'user', '2025-07-05 05:41:33'),
(127, 5, 'godu', '12345678', 'KIDO-KIDO', '$2y$10$EprgxVM5NQ03DEtLKXO/r.Z5zvHMuzRpczSVL1To2PDbScjnJfSAK', 'abdiwijayas@gmail.com', 'Laki-L', '2022', 'user', '2025-12-18 03:39:06'),
(128, 6, 'revinoo', '422201021', 'SIMALAS', '$2y$10$mL3Kq5OD5N5w6OQ.55NWQuBrzKvdS3FFD7XJslSwtA/kpiAD8/u26', 'abdiwijayas122@gmail.com', 'Laki-L', '2022', 'user', '2025-12-18 03:39:06'),
(129, 8, 'ABDI', '12345', 'Laboran', '$2y$12$sZezcC8aW3J9.4yU.WPRbe5uA1.0tkkfeMzHpwShtYTgGuGww.eN6', 'abdi@gmail.com', 'Laki-L', '2022', 'admin', '2025-12-18 03:39:06'),
(130, 9, 'Bapak Dosen Pembimbing', 'NIP19982025', 'Kepala Lab', '$2y$12$X8NMRGbqFnsnXI07YgR19eIY1fQk5sbjaxprobFNaZCUp4BLiSQoS', 'KepalaLab@gmail.com', 'Laki-Laki', '-', 'dosen', '2025-12-18 03:39:06');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absen`
--
ALTER TABLE `absen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `http_log`
--
ALTER TABLE `http_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `locker_status`
--
ALTER TABLE `locker_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locker_number` (`locker_number`);

--
-- Indeks untuk tabel `loker_terpilih`
--
ALTER TABLE `loker_terpilih`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `packet_log`
--
ALTER TABLE `packet_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pbl`
--
ALTER TABLE `pbl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pbl` (`nama_pbl`);

--
-- Indeks untuk tabel `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `Nama` (`Nama`),
  ADD KEY `NIM` (`NIM`),
  ADD KEY `PBL` (`PBL`),
  ADD KEY `Password` (`Password`),
  ADD KEY `email` (`email`),
  ADD KEY `gender` (`gender`),
  ADD KEY `date` (`date`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absen`
--
ALTER TABLE `absen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT untuk tabel `http_log`
--
ALTER TABLE `http_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT untuk tabel `locker_status`
--
ALTER TABLE `locker_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT untuk tabel `loker_terpilih`
--
ALTER TABLE `loker_terpilih`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `packet_log`
--
ALTER TABLE `packet_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=406;

--
-- AUTO_INCREMENT untuk tabel `pbl`
--
ALTER TABLE `pbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(19) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
