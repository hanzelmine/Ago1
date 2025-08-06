-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Agu 2025 pada 15.15
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
-- Database: `gtm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `atestasi`
--

CREATE TABLE `atestasi` (
  `id_atestasi` int(11) NOT NULL,
  `id_jemaat` int(11) NOT NULL,
  `jenis_atestasi` enum('Masuk','Keluar') NOT NULL,
  `gereja_asal_tujuan` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `baptisan`
--

CREATE TABLE `baptisan` (
  `id_baptisan` int(11) NOT NULL,
  `id_jemaat` int(11) NOT NULL,
  `tempat_baptis` varchar(100) DEFAULT NULL,
  `tanggal_baptis` date NOT NULL,
  `no_surat_baptis` varchar(100) DEFAULT NULL,
  `pendeta` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jemaat`
--

CREATE TABLE `jemaat` (
  `id_jemaat` int(11) NOT NULL,
  `id_keluarga` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status_dlm_keluarga` varchar(50) DEFAULT NULL,
  `status_baptis` varchar(20) DEFAULT NULL,
  `status_sidi` varchar(20) DEFAULT NULL,
  `status_perkawinan` varchar(50) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `status_jemaat` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluarga`
--

CREATE TABLE `keluarga` (
  `id_keluarga` int(11) NOT NULL,
  `kode_kk` varchar(50) NOT NULL,
  `nama_keluarga` varchar(150) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tempat_tinggal` varchar(100) DEFAULT NULL,
  `id_rayon` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `meninggal`
--

CREATE TABLE `meninggal` (
  `id_meninggal` int(11) NOT NULL,
  `id_jemaat` int(11) NOT NULL,
  `tanggal_meninggal` date NOT NULL,
  `tempat_meninggal` varchar(100) DEFAULT NULL,
  `sebab_meninggal` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pernikahan`
--

CREATE TABLE `pernikahan` (
  `id_pernikahan` int(11) NOT NULL,
  `id_suami` int(11) DEFAULT NULL,
  `id_istri` int(11) DEFAULT NULL,
  `tempat_nikah` varchar(100) DEFAULT NULL,
  `tanggal_nikah` date NOT NULL,
  `no_surat_nikah` varchar(100) DEFAULT NULL,
  `pendeta` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rayon`
--

CREATE TABLE `rayon` (
  `id_rayon` int(11) NOT NULL,
  `nama_rayon` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sidi`
--

CREATE TABLE `sidi` (
  `id_sidi` int(11) NOT NULL,
  `id_jemaat` int(11) NOT NULL,
  `tempat_sidi` varchar(100) DEFAULT NULL,
  `tanggal_sidi` date NOT NULL,
  `no_surat_sidi` varchar(100) DEFAULT NULL,
  `pendeta` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `atestasi`
--
ALTER TABLE `atestasi`
  ADD PRIMARY KEY (`id_atestasi`);

--
-- Indeks untuk tabel `baptisan`
--
ALTER TABLE `baptisan`
  ADD PRIMARY KEY (`id_baptisan`),
  ADD UNIQUE KEY `id_jemaat` (`id_jemaat`),
  ADD UNIQUE KEY `no_surat_baptis` (`no_surat_baptis`);

--
-- Indeks untuk tabel `jemaat`
--
ALTER TABLE `jemaat`
  ADD PRIMARY KEY (`id_jemaat`);

--
-- Indeks untuk tabel `keluarga`
--
ALTER TABLE `keluarga`
  ADD PRIMARY KEY (`id_keluarga`),
  ADD UNIQUE KEY `kode_kk` (`kode_kk`);

--
-- Indeks untuk tabel `meninggal`
--
ALTER TABLE `meninggal`
  ADD PRIMARY KEY (`id_meninggal`);

--
-- Indeks untuk tabel `pernikahan`
--
ALTER TABLE `pernikahan`
  ADD PRIMARY KEY (`id_pernikahan`),
  ADD UNIQUE KEY `no_surat_nikah` (`no_surat_nikah`);

--
-- Indeks untuk tabel `rayon`
--
ALTER TABLE `rayon`
  ADD PRIMARY KEY (`id_rayon`),
  ADD UNIQUE KEY `unique_nama_rayon` (`nama_rayon`);

--
-- Indeks untuk tabel `sidi`
--
ALTER TABLE `sidi`
  ADD PRIMARY KEY (`id_sidi`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `atestasi`
--
ALTER TABLE `atestasi`
  MODIFY `id_atestasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `baptisan`
--
ALTER TABLE `baptisan`
  MODIFY `id_baptisan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jemaat`
--
ALTER TABLE `jemaat`
  MODIFY `id_jemaat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `keluarga`
--
ALTER TABLE `keluarga`
  MODIFY `id_keluarga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `meninggal`
--
ALTER TABLE `meninggal`
  MODIFY `id_meninggal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pernikahan`
--
ALTER TABLE `pernikahan`
  MODIFY `id_pernikahan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rayon`
--
ALTER TABLE `rayon`
  MODIFY `id_rayon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sidi`
--
ALTER TABLE `sidi`
  MODIFY `id_sidi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
