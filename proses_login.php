<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    if (empty($username) || empty($password)) {
        echo "<script>
                alert('NRP / Username Admin dan Password wajib diisi!');
                window.location.href='login.php';
              </script>";
        exit;
    }

    $query_mhs = "SELECT * FROM Mahasiswa WHERE nrp = '$username' AND password = '$password'";
    $result_mhs = mysqli_query($koneksi, $query_mhs);

    if ($result_mhs && mysqli_num_rows($result_mhs) > 0) {
        $row_mhs = mysqli_fetch_assoc($result_mhs);
        
        $_SESSION['role'] = 'mahasiswa';
        $_SESSION['nrp'] = $row_mhs['nrp'];
        $_SESSION['nama_mahasiswa'] = $row_mhs['nama_mahasiswa'];

        echo "<script>
                alert('Login Berhasil! Selamat datang, " . mysqli_real_escape_string($koneksi, $row_mhs['nama_mahasiswa']) . "');
                window.location.href='dashboard_mahasiswa.php';
              </script>";
        exit;
    }

    $query_adm = "SELECT * FROM Admin WHERE username = '$username' AND password = '$password'";
    $result_adm = mysqli_query($koneksi, $query_adm);

    if ($result_adm && mysqli_num_rows($result_adm) > 0) {
        $row_adm = mysqli_fetch_assoc($result_adm);
        
        $_SESSION['role'] = 'admin';
        $_SESSION['id_admin'] = $row_adm['id_admin'];
        $_SESSION['username'] = $row_adm['username'];
        $_SESSION['nama_admin'] = $row_adm['nama_admin'];

        echo "<script>
                alert('Login Berhasil! Selamat datang Admin, " . mysqli_real_escape_string($koneksi, $row_adm['nama_admin']) . "');
                window.location.href='dashboard_admin.php';
              </script>";
        exit;
    }

    echo "<script>
            alert('NRP / Username atau Password salah!');
            window.location.href='login.php?status=login_failed';
          </script>";
} else {
    header('Location: login.php');
}
?>
