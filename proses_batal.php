<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $nrp = $_SESSION['nrp'];
    
    // Melakukan query UPDATE Pengaduan SET status = 'Dibatalkan' WHERE id_pengaduan = '$id' AND status = 'Pending'
    // ditambahkan filter nrp untuk memastikan mahasiswa hanya bisa membatalkan tiketnya sendiri
    $query = "UPDATE Pengaduan 
              SET status = 'Dibatalkan' 
              WHERE id_pengaduan = '$id' 
                AND nrp = '$nrp' 
                AND status = 'Pending'";
                
    if (mysqli_query($koneksi, $query)) {
        if (mysqli_affected_rows($koneksi) > 0) {
            echo "<script>
                    alert('Laporan berhasil dibatalkan!');
                    window.location.href='riwayat_mahasiswa.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal membatalkan laporan. Laporan tidak ditemukan atau sudah diproses admin.');
                    window.location.href='riwayat_mahasiswa.php';
                  </script>";
        }
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("Location: riwayat_mahasiswa.php");
    exit();
}
?>
