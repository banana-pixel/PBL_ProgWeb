<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manajemen_mahasiswa.php');
    exit;
}

if (isset($_POST['update_status'])) {
    $nrp             = mysqli_real_escape_string($koneksi, trim($_POST['nrp']            ?? ''));
    $status_akademik = mysqli_real_escape_string($koneksi, trim($_POST['status_akademik'] ?? ''));

    $status_valid = ['Aktif', 'Tidak Aktif', 'Lulus', 'Keluar'];
    if (empty($nrp) || !in_array($status_akademik, $status_valid)) {
        $_SESSION['error'] = 'Data tidak valid. Silakan coba lagi.';
        header('Location: manajemen_mahasiswa.php');
        exit;
    }

    $query = "UPDATE Mahasiswa SET status_akademik = '$status_akademik' WHERE nrp = '$nrp'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_affected_rows($koneksi) > 0) {
        $_SESSION['success'] = 'Status akademik mahasiswa berhasil diperbarui!';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui status akademik.';
    }
}

header('Location: manajemen_mahasiswa.php');
exit;
