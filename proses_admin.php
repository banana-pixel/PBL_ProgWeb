<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if (isset($_GET['action']) && $_GET['action'] == 'proses' && isset($_GET['id'])) {
    
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_GET['id']);
    $waktu_sekarang = date('Y-m-d H:i:s');
    $id_admin = isset($_SESSION['id_admin']) ? $_SESSION['id_admin'] : 1; 

    $query = "UPDATE Pengaduan 
              SET status = 'On Progress', 
                  waktu_on_progress = '$waktu_sekarang',
                  id_admin = '$id_admin'
              WHERE id_pengaduan = '$id_pengaduan'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Keluhan sedang diproses! Waktu mulai dicatat.');
                window.location.href='dashboard_admin.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'resolve' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_POST['id_pengaduan']);
    $tanggapan_admin = mysqli_real_escape_string($koneksi, $_POST['tanggapan_admin']);
    $waktu_sekarang = date('Y-m-d H:i:s');

    $query = "UPDATE Pengaduan 
              SET status = 'Resolve', 
                  tanggapan_admin = '$tanggapan_admin',
                  waktu_resolve = '$waktu_sekarang'
              WHERE id_pengaduan = '$id_pengaduan'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Tanggapan berhasil dikirim dan laporan selesai diselesaikan!');
                window.location.href='dashboard_admin.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
