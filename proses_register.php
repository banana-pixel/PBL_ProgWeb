<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nrp            = trim($_POST['nrp']            ?? '');
$nama_mahasiswa = trim($_POST['nama_mahasiswa'] ?? '');
$email          = trim($_POST['email']          ?? '');
$no_hp          = trim($_POST['no_hp']          ?? '');
$password       = trim($_POST['password']       ?? '');

if (empty($nrp) || empty($nama_mahasiswa) || empty($email) || empty($no_hp) || empty($password)) {
    $_SESSION['error'] = 'Semua bidang formulir wajib diisi!';
    header('Location: register.php');
    exit;
}

$nrp_aman            = mysqli_real_escape_string($koneksi, $nrp);
$nama_mahasiswa_aman = mysqli_real_escape_string($koneksi, $nama_mahasiswa);
$email_aman          = mysqli_real_escape_string($koneksi, $email);
$no_hp_aman          = mysqli_real_escape_string($koneksi, $no_hp);

$query_cek = "SELECT nrp FROM Mahasiswa WHERE nrp = '$nrp_aman' LIMIT 1";
$result_cek = mysqli_query($koneksi, $query_cek);

if ($result_cek && mysqli_num_rows($result_cek) > 0) {
    $_SESSION['error'] = 'NRP sudah terdaftar! Gunakan NRP lain atau silakan login.';
    header('Location: register.php');
    exit;
}

$password_md5 = md5($password);

$query_insert = "INSERT INTO Mahasiswa (nrp, nama_mahasiswa, email, no_hp, password, status_akademik)
                 VALUES ('$nrp_aman', '$nama_mahasiswa_aman', '$email_aman', '$no_hp_aman', '$password_md5', 'Aktif')";
$result_insert = mysqli_query($koneksi, $query_insert);

if ($result_insert) {
    $_SESSION['success'] = 'Registrasi berhasil! Silakan login menggunakan akun Anda.';
    header('Location: login.php');
    exit;
} else {
    $_SESSION['error'] = 'Registrasi gagal. Silakan coba lagi.';
    header('Location: register.php');
    exit;
}
