<?php
/**
 * api/get_chart_data.php
 * Fetch complaint counts grouped by category for chart visualization
 */
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

// Guard: Only allow authenticated admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak. Sesi tidak valid.']);
    exit;
}

try {
    $kueri = "SELECT k.nama_kategori, COUNT(p.id_pengaduan) AS total 
              FROM Kategori k 
              LEFT JOIN Pengaduan p ON k.id_kategori = p.id_kategori 
              GROUP BY k.id_kategori, k.nama_kategori 
              ORDER BY total DESC";
    $result = mysqli_query($koneksi, $kueri);
    if (!$result) {
        throw new mysqli_sql_exception('Gagal mengeksekusi query.');
    }

    $labels = [];
    $series = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['nama_kategori'] ?? 'Tidak Diketahui';
        $series[] = (int) $row['total'];
    }

    echo json_encode([
        'status' => 'success',
        'labels' => $labels,
        'series' => $series
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Log the error server-side
    error_log('[get_chart_data] Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data grafik.'
    ]);
}
