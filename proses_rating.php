<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: riwayat_mahasiswa.php');
    exit;
}

if (!isset($_SESSION['nrp'])) {
    header('Location: login.php');
    exit;
}

$id_pengaduan = mysqli_real_escape_string($koneksi, trim($_POST['id_pengaduan'] ?? ''));
$rating       = (int) ($_POST['rating'] ?? 0);
$komentar     = mysqli_real_escape_string($koneksi, trim($_POST['komentar_mahasiswa'] ?? ''));

if (empty($id_pengaduan) || $rating < 1 || $rating > 5) {
    $_SESSION['error'] = 'Nilai rating tidak valid!';
    header('Location: riwayat_mahasiswa.php');
    exit;
}

$query_rating = "UPDATE Pengaduan
                 SET rating = $rating,
                     komentar_mahasiswa = '$komentar'
                 WHERE id_pengaduan = '$id_pengaduan'";
$result_rating = mysqli_query($koneksi, $query_rating);

if ($result_rating) {
    $nrp_aman = mysqli_real_escape_string($koneksi, $_SESSION['nrp']);
    $query_poin = "UPDATE Mahasiswa SET poin = poin + 10 WHERE nrp = '$nrp_aman'";
    mysqli_query($koneksi, $query_poin);

    $_SESSION['success'] = 'Terima kasih! Penilaian Anda berhasil disimpan dan Anda mendapatkan +10 Poin.';
} else {
    $_SESSION['error'] = 'Gagal menyimpan penilaian. Silakan coba lagi.';
}

header('Location: riwayat_mahasiswa.php');
exit;
