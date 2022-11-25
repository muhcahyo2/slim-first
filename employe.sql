-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Nov 2022 pada 10.15
-- Versi server: 10.1.37-MariaDB
-- Versi PHP: 7.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employe`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `id_order` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `total` int(11) NOT NULL,
  `id_products` int(11) NOT NULL,
  `alamat` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_orders`
--

INSERT INTO `tbl_orders` (`id_order`, `nama`, `total`, `id_products`, `alamat`, `created_at`, `updated_at`) VALUES
(4, 'alpin', 1, 50, 'asdf', '2022-11-25 13:30:41', NULL),
(5, 'alpin', 1, 50, 'asdf', '2022-11-25 13:32:00', '2022-11-25 14:40:09'),
(6, 'alpin', 1, 50, 'asdf', '2022-11-25 13:32:30', NULL),
(7, 'alpin', 1, 50, 'asdf', '2022-11-25 13:33:33', '2022-11-25 14:37:12'),
(13, 'alpin', 2, 53, 'pati', '2022-11-25 14:48:21', NULL),
(14, 'najib', 15, 53, 'ungaran', '2022-11-25 14:49:11', NULL),
(15, 'yadi', 1, 52, 'jepara', '2022-11-25 14:55:13', NULL),
(16, 'asdf', 1, 52, 'asdfas', '2022-11-25 14:56:51', NULL),
(17, 'asdf', 1, 52, 'asdfas', '2022-11-25 14:57:07', NULL),
(18, 'huda', 1000, 51, 'salatiga', '2022-11-25 14:57:46', '2022-11-25 15:01:10'),
(19, 'yadi', 100, 51, 'tengaran', '2022-11-25 14:58:31', NULL),
(20, 'yadi', 100, 51, 'tengaran', '2022-11-25 15:01:25', NULL),
(22, 'alpin', 8, 53, 'jepara', '2022-11-25 15:08:27', '2022-11-25 15:08:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_products`
--

CREATE TABLE `tbl_products` (
  `id_product` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `total` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_products`
--

INSERT INTO `tbl_products` (`id_product`, `nama`, `deskripsi`, `total`, `harga`, `gambar`) VALUES
(51, 'batako', 'batako keras dijamain mantap', 2000, 1200, '20221124075016-batako.jpg'),
(52, 'alpin', 'alpin murah', 1, 2000, '20221125033434-testi-4.jpg'),
(53, 'beng-beng', 'bengbeng murah', 15, 1200, '20221125084752-b5fa659de6a5d7f3a9b6a18945c04db9.png'),
(54, 'beng-beng', 'harga murah rasa coklat', 20, 2000, '20221125090700-b5fa659de6a5d7f3a9b6a18945c04db9.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `status` tinyint(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `username`, `first_name`, `last_name`, `gender`, `password`, `status`) VALUES
(1, 'rogers63', 'david', 'john', 'Female', 'e6a33eee180b07e563d74fee8c2c66b8', 1),
(2, 'mike28', 'rogers', 'paul', 'Male', '2e7dc6b8a1598f4f75c3eaa47958ee2f', 1),
(3, 'rivera92', 'david', 'john', 'Male', '1c3a8e03f448d211904161a6f5849b68', 1),
(4, 'ross95', 'maria', 'sanders', 'Male', '62f0a68a4179c5cdd997189760cbcf18', 1),
(5, 'paul85', 'morris', 'miller', 'Female', '61bd060b07bddfecccea56a82b850ecf', 1),
(6, 'smith34', 'daniel', 'michael', 'Female', '7055b3d9f5cb2829c26cd7e0e601cde5', 1),
(7, 'james84', 'sanders', 'paul', 'Female', 'b7f72d6eb92b45458020748c8d1a3573', 1),
(8, 'daniel53', 'mark', 'mike', 'Male', '299cbf7171ad1b2967408ed200b4e26c', 1),
(9, 'brooks80', 'morgan', 'maria', 'Female', 'aa736a35dc15934d67c0a999dccff8f6', 1),
(10, 'morgan65', 'paul', 'miller', 'Female', 'a28dca31f5aa5792e1cefd1dfd098569', 1),
(10001, 'mike23', NULL, 'name', NULL, 'fce0b8198794bb65590ae721bb1e83a2', NULL),
(10002, 'mike23', NULL, 'name', NULL, 'fce0b8198794bb65590ae721bb1e83a2', NULL),
(10003, 'mike23', 'mike', 'name', NULL, '827ccb0eea8a706c4c34a16891f84e7b', NULL),
(10004, 'yadi', 'yadi', 'muhammad', 'male', '25d55ad283aa400af464c76d713c07ad', NULL),
(10005, 'alpin', 'alpin', 'kelvin', 'male', 'e10adc3949ba59abbe56e057f20f883e', NULL),
(10006, 'zaqi', 'mirza', 'zaqi', 'male', '827ccb0eea8a706c4c34a16891f84e7b', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`id_order`);

--
-- Indeks untuk tabel `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`id_product`);

--
-- Indeks untuk tabel `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10007;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
