<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header('Location: riwayat_mahasiswa.php');
    exit;
}

$id_pengaduan = mysqli_real_escape_string($koneksi, trim($_GET['id']));
$nrp          = mysqli_real_escape_string($koneksi, $_SESSION['nrp']);

$query = "UPDATE Pengaduan
          SET status = 'Dibatalkan'
          WHERE id_pengaduan = '$id_pengaduan'
            AND nrp = '$nrp'
            AND status = 'Pending'";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_affected_rows($koneksi) > 0) {
    $_SESSION['success'] = 'Laporan berhasil dibatalkan!';
} else {
    $_SESSION['error'] = 'Gagal membatalkan laporan. Laporan tidak ditemukan atau sudah diproses admin.';
}

header('Location: riwayat_mahasiswa.php');
exit;
