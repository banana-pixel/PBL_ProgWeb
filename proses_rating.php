<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pengaduan']) && isset($_POST['rating'])) {
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_POST['id_pengaduan']);
    $rating = (int) $_POST['rating'];
    $komentar = isset($_POST['komentar_mahasiswa']) ? mysqli_real_escape_string($koneksi, $_POST['komentar_mahasiswa']) : '';

    if ($rating >= 1 && $rating <= 5) {
        $query = "UPDATE Pengaduan 
                  SET rating = '$rating', 
                      komentar_mahasiswa = '$komentar' 
                  WHERE id_pengaduan = '$id_pengaduan'";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                    alert('Terima kasih! Penilaian Anda berhasil disimpan.');
                    window.location.href='riwayat_mahasiswa.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>
                alert('Nilai rating tidak valid!');
                window.location.href='riwayat_mahasiswa.php';
              </script>";
    }
} else {
    header("Location: riwayat_mahasiswa.php");
    exit();
}
?>
