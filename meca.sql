-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 06 Sep 2024 pada 16.02
-- Versi server: 8.2.0
-- Versi PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meca`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_keranjang`
--

DROP TABLE IF EXISTS `detail_keranjang`;
CREATE TABLE IF NOT EXISTS `detail_keranjang` (
  `id_detail_keranjang` int NOT NULL AUTO_INCREMENT,
  `id_keranjang` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `catatan` text,
  PRIMARY KEY (`id_detail_keranjang`),
  KEY `id_keranjang` (`id_keranjang`),
  KEY `id_produk` (`id_produk`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `id_detail_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` varchar(50) NOT NULL,
  `id_produk` int NOT NULL,
  `ukuran` varchar(50) NOT NULL,
  `jumlah` int NOT NULL,
  `catatan` text,
  PRIMARY KEY (`id_detail_pesanan`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gambar_produk`
--

DROP TABLE IF EXISTS `gambar_produk`;
CREATE TABLE IF NOT EXISTS `gambar_produk` (
  `id_gambar` int NOT NULL AUTO_INCREMENT,
  `id_produk` int DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tanggal_ditambahkan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gambar`),
  KEY `id_produk` (`id_produk`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gambar_review`
--

DROP TABLE IF EXISTS `gambar_review`;
CREATE TABLE IF NOT EXISTS `gambar_review` (
  `id_gambar` int NOT NULL AUTO_INCREMENT,
  `id_review` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gambar`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id_kategori`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

DROP TABLE IF EXISTS `keranjang`;
CREATE TABLE IF NOT EXISTS `keranjang` (
  `id_keranjang` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `tanggal_dibuat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'aktif',
  PRIMARY KEY (`id_keranjang`),
  KEY `id_pelanggan` (`id_pelanggan`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `id_user`, `nama`, `email`, `no_telp`, `alamat`) VALUES
(1, 10, 'Abdullah Sajad', 'doel.jad@gmail.com', '0812345654', 'Jln. Hos Cokroaminoto');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
CREATE TABLE IF NOT EXISTS `pesanan` (
  `id_pesanan` varchar(50) NOT NULL,
  `id_pelanggan` int DEFAULT NULL,
  `tanggal_pesanan` timestamp NOT NULL,
  `tanggal_pengiriman` date DEFAULT NULL,
  `status` varchar(2) NOT NULL,
  `total` int DEFAULT NULL,
  PRIMARY KEY (`id_pesanan`),
  KEY `customer_id` (`id_pelanggan`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

DROP TABLE IF EXISTS `produk`;
CREATE TABLE IF NOT EXISTS `produk` (
  `id_produk` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `id_kategori` int NOT NULL,
  `deskripsi` text,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_produk`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id_review` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NOT NULL,
  `id_user` int NOT NULL,
  `id_detail_pesanan` int NOT NULL,
  `rating` int NOT NULL,
  `review_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_review`),
  KEY `id_produk` (`id_produk`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_transaksi` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` varchar(50) DEFAULT NULL,
  `tanggal_pembayaran` timestamp NOT NULL,
  `total` int NOT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id_transaksi`)
) ENGINE=MyISAM DEFAULT;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `name`, `username`, `password`, `role`) VALUES
(1, 'Admin', 'admin', 'admin', 1),
(2, 'Manager', 'manager', 'manager', 2),
(10, 'Abdullah Sajad', 'doeljad', 'doeljad', 3);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
