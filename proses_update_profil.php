<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role   = $_SESSION['role'];
$action = trim($_POST['action'] ?? '');

if ($action === 'update_profile') {

    if ($role !== 'mahasiswa') {
        $_SESSION['error'] = 'Aksi ini hanya tersedia untuk mahasiswa.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    $nrp   = $_SESSION['nrp'];
    $no_hp = trim($_POST['no_hp'] ?? '');

    if (empty($no_hp)) {
        $_SESSION['error'] = 'Nomor HP tidak boleh kosong.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    $no_hp_aman = mysqli_real_escape_string($koneksi, $no_hp);
    $nrp_aman   = mysqli_real_escape_string($koneksi, $nrp);

    $nama_foto_baru = null;

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto_profil'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Upload foto gagal. Coba lagi.';
            header('Location: dashboard_mahasiswa.php');
            exit;
        }

        $tipe_diizinkan = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $tipe_diizinkan)) {
            $_SESSION['error'] = 'Hanya file JPG atau PNG yang diizinkan.';
            header('Location: dashboard_mahasiswa.php');
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran foto maksimal 2 MB.';
            header('Location: dashboard_mahasiswa.php');
            exit;
        }

        $ekstensi    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nama_file   = 'avatar_' . $nrp . '.' . $ekstensi;
        $folder_avatar = __DIR__ . '/uploads/avatars/';

        if (!is_dir($folder_avatar)) {
            mkdir($folder_avatar, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $folder_avatar . $nama_file)) {
            $_SESSION['error'] = 'Gagal menyimpan foto ke server.';
            header('Location: dashboard_mahasiswa.php');
            exit;
        }

        $nama_foto_baru = $nama_file;
    }

    if ($nama_foto_baru) {
        $nama_foto_aman = mysqli_real_escape_string($koneksi, $nama_foto_baru);
        $query = "UPDATE Mahasiswa SET no_hp = '$no_hp_aman', foto_profil = '$nama_foto_aman' WHERE nrp = '$nrp_aman'";
    } else {
        $query = "UPDATE Mahasiswa SET no_hp = '$no_hp_aman' WHERE nrp = '$nrp_aman'";
    }

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['no_hp'] = $no_hp;
        if ($nama_foto_baru) {
            $_SESSION['foto_profil'] = $nama_foto_baru;
        }
        $_SESSION['success'] = 'Profil berhasil diperbarui.';
    } else {
        $_SESSION['error'] = 'Gagal menyimpan perubahan ke database.';
    }

    header('Location: dashboard_mahasiswa.php');
    exit;
}

if ($action === 'change_password') {
    $password_lama    = $_POST['old_password']     ?? '';
    $password_baru    = $_POST['new_password']     ?? '';
    $konfirmasi_pass  = $_POST['confirm_password'] ?? '';

    if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_pass)) {
        $_SESSION['error'] = 'Semua field password wajib diisi.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    if ($password_baru !== $konfirmasi_pass) {
        $_SESSION['error'] = 'Konfirmasi password baru tidak cocok.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    if (strlen($password_baru) < 6) {
        $_SESSION['error'] = 'Password baru minimal 6 karakter.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    if ($password_baru === $password_lama) {
        $_SESSION['error'] = 'Password baru tidak boleh sama dengan password lama.';
        header('Location: dashboard_mahasiswa.php');
        exit;
    }

    $password_lama_md5 = md5($password_lama);
    $password_baru_md5 = md5($password_baru);
    $berhasil = false;

    if ($role === 'mahasiswa') {
        $nrp_aman = mysqli_real_escape_string($koneksi, $_SESSION['nrp']);

        $query_cek = "SELECT nrp FROM Mahasiswa WHERE nrp = '$nrp_aman' AND password = '$password_lama_md5' LIMIT 1";
        $result_cek = mysqli_query($koneksi, $query_cek);

        if (!$result_cek || mysqli_num_rows($result_cek) === 0) {
            $_SESSION['error'] = 'Password lama yang Anda masukkan salah.';
            header('Location: dashboard_mahasiswa.php');
            exit;
        }

        $query_update = "UPDATE Mahasiswa SET password = '$password_baru_md5' WHERE nrp = '$nrp_aman'";
        $berhasil = mysqli_query($koneksi, $query_update);
        $halaman_redirect = 'dashboard_mahasiswa.php';

    } elseif ($role === 'admin') {
        $id_admin = (int) $_SESSION['id_admin'];

        $query_cek = "SELECT id_admin FROM Admin WHERE id_admin = $id_admin AND password = '$password_lama_md5' LIMIT 1";
        $result_cek = mysqli_query($koneksi, $query_cek);

        if (!$result_cek || mysqli_num_rows($result_cek) === 0) {
            $_SESSION['error'] = 'Password lama yang Anda masukkan salah.';
            header('Location: dashboard_admin.php');
            exit;
        }

        $query_update = "UPDATE Admin SET password = '$password_baru_md5' WHERE id_admin = $id_admin";
        $berhasil = mysqli_query($koneksi, $query_update);
        $halaman_redirect = 'dashboard_admin.php';

    } else {
        header('Location: login.php');
        exit;
    }

    if ($berhasil) {
        $_SESSION['success'] = 'Password berhasil diubah!';
    } else {
        $_SESSION['error'] = 'Gagal mengubah password di database.';
    }

    header('Location: ' . $halaman_redirect);
    exit;
}

$halaman_default = ($role === 'admin') ? 'dashboard_admin.php' : 'dashboard_mahasiswa.php';
header('Location: ' . $halaman_default);
exit;
