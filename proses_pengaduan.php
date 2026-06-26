<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: buat_tiket.php');
    exit;
}

if (!isset($_SESSION['nrp'])) {
    header('Location: login.php');
    exit;
}

$id_kategori     = (int) ($_POST['kategori']        ?? 0);
$lokasi_spesifik = trim($_POST['lokasi_spesifik']   ?? '');
$detail_keluhan  = trim($_POST['detail_keluhan']    ?? '');

if ($id_kategori === 0 || empty($lokasi_spesifik) || empty($detail_keluhan)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header('Location: buat_tiket.php');
    exit;
}

$urgensi = trim($_POST['urgensi'] ?? 'Sedang');
if (!in_array($urgensi, ['Rendah', 'Sedang', 'Tinggi'])) {
    $urgensi = 'Sedang';
}

$nrp             = $_SESSION['nrp'];
$id_pengaduan    = 'T-' . date('Ymd-His');
$tanggal_lapor   = date('Y-m-d H:i:s');
$target_selesai  = date('Y-m-d H:i:s', strtotime('+48 hours'));

$lokasi_spesifik_aman = mysqli_real_escape_string($koneksi, $lokasi_spesifik);
$detail_keluhan_aman  = mysqli_real_escape_string($koneksi, $detail_keluhan);
$urgensi_aman         = mysqli_real_escape_string($koneksi, $urgensi);
$nrp_aman             = mysqli_real_escape_string($koneksi, $nrp);

$nama_foto = 'NULL';

if (isset($_FILES['foto_pendukung']) && $_FILES['foto_pendukung']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['foto_pendukung'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Gagal mengunggah file. Kode error: ' . $file['error'];
        header('Location: buat_tiket.php');
        exit;
    }

    $tipe_diizinkan = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $tipe_diizinkan)) {
        $_SESSION['error'] = 'Tipe file tidak didukung. Gunakan JPG, PNG, atau PDF.';
        header('Location: buat_tiket.php');
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 5 MB.';
        header('Location: buat_tiket.php');
        exit;
    }

    $ekstensi  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nama_file = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ekstensi;
    $folder_upload = __DIR__ . '/uploads/';

    if (!is_dir($folder_upload)) {
        mkdir($folder_upload, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $folder_upload . $nama_file)) {
        $_SESSION['error'] = 'Gagal menyimpan file ke server.';
        header('Location: buat_tiket.php');
        exit;
    }

    $nama_foto = "'" . mysqli_real_escape_string($koneksi, $nama_file) . "'";
}

$query_insert = "INSERT INTO Pengaduan (id_pengaduan, nrp, id_kategori, lokasi_spesifik, id_admin, tanggal_lapor, target_selesai, detail_keluhan, foto_pendukung, status, urgensi)
                 VALUES ('$id_pengaduan', '$nrp_aman', $id_kategori, '$lokasi_spesifik_aman', NULL, '$tanggal_lapor', '$target_selesai', '$detail_keluhan_aman', $nama_foto, 'Pending', '$urgensi_aman')";
$result_insert = mysqli_query($koneksi, $query_insert);

if ($result_insert) {
    $_SESSION['success'] = 'Pengaduan berhasil dikirim! Kami akan segera memprosesnya.';
    header('Location: dashboard_mahasiswa.php');
    exit;
} else {
    $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    header('Location: buat_tiket.php');
    exit;
}
