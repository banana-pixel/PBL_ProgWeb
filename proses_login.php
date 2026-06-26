<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'NRP / Username dan Password wajib diisi!';
    header('Location: login.php');
    exit;
}

$username_aman = mysqli_real_escape_string($koneksi, $username);
$password_md5 = md5($password);

$query_mhs = "SELECT * FROM Mahasiswa WHERE nrp = '$username_aman' AND password = '$password_md5' LIMIT 1";
$result_mhs = mysqli_query($koneksi, $query_mhs);

if ($result_mhs && mysqli_num_rows($result_mhs) > 0) {
    $data_mhs = mysqli_fetch_assoc($result_mhs);

    $_SESSION['role']           = 'mahasiswa';
    $_SESSION['nrp']            = $data_mhs['nrp'];
    $_SESSION['nama_mahasiswa'] = $data_mhs['nama_mahasiswa'];
    $_SESSION['email']          = $data_mhs['email']       ?? '';
    $_SESSION['no_hp']          = $data_mhs['no_hp']       ?? '';
    $_SESSION['foto_profil']    = $data_mhs['foto_profil'] ?? '';

    $_SESSION['success'] = 'Login berhasil! Selamat datang, ' . $data_mhs['nama_mahasiswa'] . '.';
    header('Location: dashboard_mahasiswa.php');
    exit;
}

$query_adm = "SELECT * FROM Admin WHERE username = '$username_aman' AND password = '$password_md5' LIMIT 1";
$result_adm = mysqli_query($koneksi, $query_adm);

if ($result_adm && mysqli_num_rows($result_adm) > 0) {
    $data_adm = mysqli_fetch_assoc($result_adm);

    $_SESSION['role']       = 'admin';
    $_SESSION['id_admin']   = $data_adm['id_admin'];
    $_SESSION['username']   = $data_adm['username'];
    $_SESSION['nama_admin'] = $data_adm['nama_admin'];

    $_SESSION['success'] = 'Login berhasil! Selamat datang Admin, ' . $data_adm['nama_admin'] . '.';
    header('Location: dashboard_admin.php');
    exit;
}

$_SESSION['error'] = 'NRP / Username atau Password salah!';
header('Location: login.php');
exit;
