<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['nrp'])) {
    $_SESSION['nrp'] = '2473025'; 
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nrp = $_SESSION['nrp'];
    $id_kategori = $_POST['kategori']; 
    $lokasi_spesifik = mysqli_real_escape_string($koneksi, $_POST['lokasi_spesifik']);
    $detail_keluhan = mysqli_real_escape_string($koneksi, $_POST['detail_keluhan']);
    
    $id_pengaduan = "T-" . date('Ymd-His'); 
    $tanggal_lapor = date('Y-m-d H:i:s');
    
    $nama_foto = NULL;
    if (isset($_FILES['foto_pendukung']) && $_FILES['foto_pendukung']['error'] == 0) {
        $file_tmp = $_FILES['foto_pendukung']['tmp_name'];
        $nama_file_asli = $_FILES['foto_pendukung']['name'];
        
        $nama_foto = time() . "_" . basename($nama_file_asli);
        $lokasi_simpan = "uploads/" . $nama_foto;
        
        move_uploaded_file($file_tmp, $lokasi_simpan);
    }
    
    $query = "INSERT INTO Pengaduan (id_pengaduan, nrp, id_kategori, lokasi_spesifik, id_admin, tanggal_lapor, detail_keluhan, foto_pendukung, status) 
              VALUES ('$id_pengaduan', '$nrp', '$id_kategori', '$lokasi_spesifik', NULL, '$tanggal_lapor', '$detail_keluhan', '$nama_foto', 'Pending')";
              
    $eksekusi = mysqli_query($koneksi, $query);
    
    if ($eksekusi) {
        echo "<script>
                alert('Pengaduan berhasil dikirim!');
                window.location.href='dashboard_mahasiswa.php';
              </script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}
?>
