<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nrp = mysqli_real_escape_string($koneksi, $_POST['nrp']);
    $nama_mahasiswa = mysqli_real_escape_string($koneksi, $_POST['nama_mahasiswa']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    if (empty($nrp) || empty($nama_mahasiswa) || empty($email) || empty($no_hp) || empty($password)) {
        echo "<script>
                alert('Semua bidang formulir wajib diisi!');
                window.location.href='register.php';
              </script>";
        exit;
    }

    $cek_query = "SELECT * FROM Mahasiswa WHERE nrp = '$nrp'";
    $cek_result = mysqli_query($koneksi, $cek_query);

    if (mysqli_num_rows($cek_result) > 0) {
        echo "<script>
                alert('NRP sudah terdaftar! Gunakan NRP lain atau silakan login.');
                window.location.href='register.php';
              </script>";
        exit;
    }

    $query = "INSERT INTO Mahasiswa (nrp, nama_mahasiswa, email, no_hp, password) 
              VALUES ('$nrp', '$nama_mahasiswa', '$email', '$no_hp', '$password')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Registrasi berhasil! Silakan login menggunakan akun Anda.');
                window.location.href='login.php?status=register_success';
              </script>";
    } else {
        echo "<script>
                alert('Registrasi gagal: " . mysqli_real_escape_string($koneksi, mysqli_error($koneksi)) . "');
                window.location.href='register.php';
              </script>";
    }
} else {
    header('Location: register.php');
}
?>
