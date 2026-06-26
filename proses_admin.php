<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

$action = $_GET['action'] ?? '';

if ($action === 'proses' && isset($_GET['id'])) {
    $id_pengaduan   = mysqli_real_escape_string($koneksi, trim($_GET['id']));
    $waktu_sekarang = date('Y-m-d H:i:s');
    $id_admin       = (int) ($_SESSION['id_admin'] ?? 1);

    $query = "UPDATE Pengaduan
              SET status = 'On Progress',
                  waktu_on_progress = '$waktu_sekarang',
                  id_admin = $id_admin
              WHERE id_pengaduan = '$id_pengaduan'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_affected_rows($koneksi) > 0) {
        $_SESSION['success'] = 'Keluhan sedang diproses! Waktu mulai telah dicatat.';
    } else {
        $_SESSION['error'] = 'Gagal memproses keluhan.';
    }

    header('Location: dashboard_admin.php');
    exit;
}

if ($action === 'resolve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengaduan    = mysqli_real_escape_string($koneksi, trim($_POST['id_pengaduan']    ?? ''));
    $tanggapan_admin = mysqli_real_escape_string($koneksi, trim($_POST['tanggapan_admin'] ?? ''));
    $waktu_sekarang  = date('Y-m-d H:i:s');

    if (empty($id_pengaduan) || empty($tanggapan_admin)) {
        $_SESSION['error'] = 'ID Pengaduan dan tanggapan wajib diisi!';
        header('Location: manajemen_tiket.php');
        exit;
    }

    $nama_foto_bukti = 'NULL';

    if (isset($_FILES['foto_bukti_selesai']) && $_FILES['foto_bukti_selesai']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto_bukti_selesai'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $tipe_diizinkan = ['image/jpeg', 'image/png', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $tipe_diizinkan)) {
                $_SESSION['error'] = 'Format bukti foto tidak valid. Gunakan JPG atau PNG.';
                header('Location: manajemen_tiket.php');
                exit;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = 'Ukuran bukti foto terlalu besar. Maksimal 5 MB.';
                header('Location: manajemen_tiket.php');
                exit;
            }

            $ekstensi    = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nama_file   = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ekstensi;
            $folder_upload = __DIR__ . '/uploads/';

            if (!is_dir($folder_upload)) {
                mkdir($folder_upload, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $folder_upload . $nama_file)) {
                $nama_foto_bukti = "'" . mysqli_real_escape_string($koneksi, $nama_file) . "'";
            }
        }
    }

    $query = "UPDATE Pengaduan
              SET status = 'Resolve',
                  tanggapan_admin = '$tanggapan_admin',
                  waktu_resolve = '$waktu_sekarang',
                  foto_bukti_selesai = $nama_foto_bukti
              WHERE id_pengaduan = '$id_pengaduan'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['success'] = 'Tanggapan berhasil dikirim dan laporan telah diselesaikan!';
    } else {
        $_SESSION['error'] = 'Gagal menyimpan penyelesaian laporan.';
    }

    header('Location: manajemen_tiket.php');
    exit;
}

if ($action === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengaduan     = mysqli_real_escape_string($koneksi, trim($_POST['id_pengaduan']    ?? ''));
    $alasan_penolakan = mysqli_real_escape_string($koneksi, trim($_POST['alasan_penolakan'] ?? ''));
    $waktu_sekarang   = date('Y-m-d H:i:s');

    if (empty($id_pengaduan) || empty($alasan_penolakan)) {
        $_SESSION['error'] = 'Alasan penolakan wajib diisi!';
        header('Location: manajemen_tiket.php');
        exit;
    }

    $query = "UPDATE Pengaduan
              SET status = 'Ditolak',
                  tanggapan_admin = '$alasan_penolakan',
                  waktu_resolve = '$waktu_sekarang'
              WHERE id_pengaduan = '$id_pengaduan'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['success'] = 'Laporan telah ditolak. Alasan penolakan telah tersimpan.';
    } else {
        $_SESSION['error'] = 'Gagal menolak laporan.';
    }

    header('Location: manajemen_tiket.php');
    exit;
}

header('Location: manajemen_tiket.php');
exit;
