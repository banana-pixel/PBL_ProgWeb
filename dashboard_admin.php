<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$total_tiket = 0;
$tiket_pending = 0;
$tiket_progress = 0;
$tiket_resolve = 0;

$kueri_stats = "SELECT status, COUNT(*) AS total FROM Pengaduan GROUP BY status";
$result_stats = mysqli_query($koneksi, $kueri_stats);
while ($row_stats = mysqli_fetch_assoc($result_stats)) {
    $status = strtolower($row_stats['status']);
    $total = (int) $row_stats['total'];
    $total_tiket += $total;
    if ($status === 'pending') {
        $tiket_pending = $total;
    } elseif ($status === 'on progress') {
        $tiket_progress = $total;
    } elseif ($status === 'resolve') {
        $tiket_resolve = $total;
    }
}

$kueri_rating = "SELECT AVG(rating) AS avg_rating FROM Pengaduan WHERE rating IS NOT NULL";
$result_rating = mysqli_query($koneksi, $kueri_rating);
$row_rating = mysqli_fetch_assoc($result_rating);
$avg_rating = !is_null($row_rating['avg_rating']) ? round($row_rating['avg_rating'], 1) : null;

$sort_action = isset($_GET['sort_action']) ? $_GET['sort_action'] : 'terlama';

$query_need_action = "SELECT p.id_pengaduan, p.tanggal_lapor, p.detail_keluhan, p.target_selesai,
                             p.lokasi_spesifik, p.urgensi, m.nama_mahasiswa
                      FROM Pengaduan p
                      LEFT JOIN Mahasiswa m ON p.nrp = m.nrp
                      WHERE p.status = 'Pending'";

if ($sort_action === 'urgensi') {
    $query_need_action .= " ORDER BY FIELD(p.urgensi, 'Tinggi', 'Sedang', 'Rendah') ASC, p.tanggal_lapor ASC";
} else {
    $query_need_action .= " ORDER BY p.tanggal_lapor ASC";
}
$query_need_action .= " LIMIT 5";

$result_need_action = mysqli_query($koneksi, $query_need_action);

$query_reviews = "SELECT p.id_pengaduan, p.rating, p.komentar_mahasiswa,
                         p.waktu_resolve, m.nama_mahasiswa
                  FROM Pengaduan p
                  LEFT JOIN Mahasiswa m ON p.nrp = m.nrp
                  WHERE p.status = 'Resolve' AND p.rating IS NOT NULL
                  ORDER BY p.waktu_resolve DESC
                  LIMIT 5";
$result_reviews = mysqli_query($koneksi, $query_reviews);
?>
<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard Admin - E-Complaint</title>

    <meta name="description" content="Dashboard Admin E-Complaint Universitas Kristen Maranatha" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
    <style>
      :root, [data-bs-theme=light] {
        --bs-primary: #004b87 !important;
        --bs-primary-rgb: 0, 75, 135 !important;
      }
      .bg-primary { background-color: #004b87 !important; }
      .text-primary, .app-brand-text { color: #004b87 !important; }
      .btn-primary { background-color: #004b87 !important; border-color: #004b87 !important; box-shadow: 0 0.125rem 0.25rem 0 rgba(0, 75, 135, 0.4) !important; }
      .btn-primary:hover, .btn-primary:active, .btn-primary:focus { background-color: #003a69 !important; border-color: #003a69 !important; }
      .btn-outline-primary { color: #004b87 !important; border-color: #004b87 !important; }
      .btn-outline-primary:hover { background-color: #004b87 !important; color: #fff !important; }
      .bg-label-primary { background-color: #e6f1fc !important; color: #004b87 !important; }
      .app-brand-logo svg, .app-brand-logo.demo svg { color: #004b87 !important; fill: #004b87 !important; }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Layout page -->
        <div class="layout-page">

          <!-- Navbar -->
          <?php include 'navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">

            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">

              <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
              <?php endif; ?>

              <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
              <?php endif; ?>

              <!-- Welcome Banner -->
              <div class="card bg-primary text-white mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                  <div>
                    <h4 class="card-title text-white mb-1">Dashboard Admin</h4>
                    <p class="card-text mb-0">Pantau dan evaluasi seluruh pengaduan mahasiswa Universitas Kristen Maranatha secara real-time.</p>
                  </div>
                  <a href="manajemen_tiket.php" class="btn btn-white bg-white text-primary fw-bold flex-shrink-0">
                    <i class="bx bx-list-ul me-1"></i> Kelola Semua Tiket
                  </a>
                </div>
              </div>

              <!-- ── Row 1: Stat Cards ─────────────────────────────────────────── -->
              <div class="row mb-4">
                <!-- Total -->
                <div class="col-lg-3 col-md-6 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-primary text-primary">
                            <i class="bx bx-envelope bx-lg"></i>
                          </span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Total Pengaduan</p>
                      <h4 class="card-title mb-0"><?php echo $total_tiket; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Pending -->
                <div class="col-lg-3 col-md-6 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-danger text-danger">
                            <i class="bx bx-time bx-lg"></i>
                          </span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Diajukan</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_pending; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- On Progress -->
                <div class="col-lg-3 col-md-6 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-warning text-warning">
                            <i class="bx bx-loader-circle bx-lg"></i>
                          </span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Diproses</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_progress; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Resolved -->
                <div class="col-lg-3 col-md-6 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-success text-success">
                            <i class="bx bx-check-circle bx-lg"></i>
                          </span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Selesai</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_resolve; ?></h4>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Row 1 -->

              <!-- ── Row 2: Charts ────────────────────────────────────────────── -->
              <div class="row mb-4">
                <!-- Donut Chart -->
                <div class="col-md-6 mb-4 mb-md-0">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h5 class="card-title mb-1 fw-bold">Proporsi Laporan per Kategori</h5>
                      <small class="text-muted">Grafik pembagian kategori pengaduan masuk</small>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                      <div id="kategoriDonutChart" style="width: 100%; min-height: 290px;"></div>
                    </div>
                  </div>
                </div>

                <!-- Average Rating -->
                <div class="col-md-6">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h5 class="card-title mb-1 fw-bold">Rata-rata Penilaian</h5>
                      <small class="text-muted">Evaluasi layanan dari keluhan mahasiswa</small>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 290px;">
                      <?php if ($avg_rating !== null): ?>
                        <div class="text-center">
                          <h1 class="display-3 fw-bold text-primary mb-2"><?php echo $avg_rating; ?></h1>
                          <div class="text-warning mb-2" style="font-size: 1.8rem;">
                            <?php
                              $full_stars  = floor($avg_rating);
                              $half_star   = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                              $empty_stars = 5 - $full_stars - $half_star;
                              for ($i = 0; $i < $full_stars; $i++)  echo '<i class="bx bxs-star"></i>';
                              if ($half_star)                        echo '<i class="bx bxs-star-half"></i>';
                              for ($i = 0; $i < $empty_stars; $i++) echo '<i class="bx bx-star"></i>';
                            ?>
                          </div>
                          <p class="text-muted mb-1 small">Skala Penilaian 1.0 – 5.0</p>
                          <small class="text-muted">Berdasarkan tiket yang diselesaikan</small>
                        </div>
                      <?php else: ?>
                        <div class="text-center text-muted">
                          <p class="mb-0">Belum ada penilaian dari mahasiswa</p>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Row 2 -->

              <!-- ── Row 3: Action Widgets ────────────────────────────────────── -->
              <div class="row">
                <!-- Kolom Kiri: 5 Tiket Pending Perlu Tindakan -->
                <div class="col-md-6 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div>
                        <h5 class="card-title mb-1 fw-bold">
                          <i class="bx bx-error-circle text-danger me-1"></i> Perlu Tindakan
                        </h5>
                        <?php if ($sort_action === 'urgensi'): ?>
                          <small class="text-muted">5 tiket dengan urgensi tertinggi</small>
                        <?php else: ?>
                          <small class="text-muted">5 tiket Diajukan paling lama menunggu</small>
                        <?php endif; ?>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" onchange="location.href='dashboard_admin.php?sort_action=' + this.value" style="width: auto;">
                          <option value="terlama" <?php echo $sort_action === 'terlama' ? 'selected' : ''; ?>>Urut: Terlama</option>
                          <option value="urgensi" <?php echo $sort_action === 'urgensi' ? 'selected' : ''; ?>>Urut: Urgensi</option>
                        </select>
                        <a href="manajemen_tiket.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                      </div>
                    </div>
                    <div class="card-body px-0 pb-0">
                      <?php if (mysqli_num_rows($result_need_action) > 0): ?>
                        <ul class="list-group list-group-flush">
                          <?php while ($row = mysqli_fetch_assoc($result_need_action)): ?>
                            <li class="list-group-item px-4 py-3">
                              <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-label-danger p-2 flex-shrink-0 mt-1">
                                  <i class="bx bx-time"></i>
                                </span>
                                <div class="flex-grow-1 overflow-hidden">
                                  <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                      <strong class="small"><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong>
                                      <?php
                                        $urg = $row['urgensi'] ?? 'Sedang';
                                        $urg_class = 'bg-label-warning';
                                        if ($urg === 'Tinggi') {
                                            $urg_class = 'bg-label-danger';
                                        } elseif ($urg === 'Rendah') {
                                            $urg_class = 'bg-label-info';
                                        }
                                      ?>
                                      <span class="badge <?php echo $urg_class; ?> px-2 py-0.5" style="font-size: 0.65rem;"><?php echo htmlspecialchars($urg); ?></span>
                                    </div>
                                    <small class="text-muted flex-shrink-0 ms-2">
                                      <?php echo date('d M Y', strtotime($row['tanggal_lapor'])); ?>
                                    </small>
                                  </div>
                                  <p class="mb-1 small text-truncate text-muted" title="<?php echo htmlspecialchars($row['detail_keluhan']); ?>">
                                    <?php echo htmlspecialchars($row['detail_keluhan']); ?>
                                  </p>
                                  <div class="d-flex align-items-center justify-content-between">
                                    <small class="text-muted">
                                      <i class="bx bx-user me-1"></i><?php echo htmlspecialchars($row['nama_mahasiswa'] ?? 'Mahasiswa'); ?>
                                    </small>
                                    <?php if (!empty($row['target_selesai'])): ?>
                                      <small class="sla-countdown fw-bold" data-target="<?php echo htmlspecialchars($row['target_selesai']); ?>">--:--:--</small>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>
                            </li>
                          <?php endwhile; ?>
                        </ul>
                      <?php else: ?>
                        <div class="text-center text-muted py-5">
                          Tidak ada tiket yang menunggu tindakan
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <!-- Kolom Kanan: 5 Ulasan Terbaru Mahasiswa -->
                <div class="col-md-6 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div>
                        <h5 class="card-title mb-1 fw-bold">
                          <i class="bx bxs-star text-warning me-1"></i> Ulasan Terbaru
                        </h5>
                        <small class="text-muted">5 penilaian terbaru dari mahasiswa</small>
                      </div>
                    </div>
                    <div class="card-body px-0 pb-0">
                      <?php if (mysqli_num_rows($result_reviews) > 0): ?>
                        <ul class="list-group list-group-flush">
                          <?php while ($rev = mysqli_fetch_assoc($result_reviews)): ?>
                            <li class="list-group-item px-4 py-3">
                              <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-label-warning p-2 flex-shrink-0 mt-1">
                                  <i class="bx bxs-star"></i>
                                </span>
                                <div class="flex-grow-1 overflow-hidden">
                                  <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small"><?php echo htmlspecialchars($rev['nama_mahasiswa'] ?? 'Mahasiswa'); ?></strong>
                                    <span class="badge bg-label-warning ms-2 flex-shrink-0">
                                      <?php echo str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']); ?>
                                      <?php echo $rev['rating']; ?>/5
                                    </span>
                                  </div>
                                  <?php if (!empty($rev['komentar_mahasiswa'])): ?>
                                    <p class="mb-1 small text-muted fst-italic text-truncate" title="<?php echo htmlspecialchars($rev['komentar_mahasiswa']); ?>">
                                      "<?php echo htmlspecialchars($rev['komentar_mahasiswa']); ?>"
                                    </p>
                                  <?php else: ?>
                                    <p class="mb-1 small text-muted fst-italic">Tidak ada komentar.</p>
                                  <?php endif; ?>
                                  <small class="text-muted">
                                    <i class="bx bx-time me-1"></i>
                                    <?php echo date('d M Y', strtotime($rev['waktu_resolve'])); ?>
                                  </small>
                                </div>
                              </div>
                            </li>
                          <?php endwhile; ?>
                        </ul>
                      <?php else: ?>
                        <div class="text-center text-muted py-5">
                          Belum ada ulasan dari mahasiswa
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Row 3 -->

            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>

            <script>
              document.addEventListener('DOMContentLoaded', function () {

                // ── SLA Countdown Timers ──────────────────────────────────────
                (function initSLACountdowns() {
                  const elements = document.querySelectorAll('.sla-countdown');
                  if (!elements.length) return;

                  function updateTimers() {
                    const now = Date.now();
                    elements.forEach(el => {
                      const targetTime = new Date(el.getAttribute('data-target').replace(' ', 'T')).getTime();
                      const distance   = targetTime - now;
                      if (isNaN(distance)) { el.textContent = '—'; return; }

                      const abs = Math.abs(distance);
                      const hh  = String(Math.floor(abs / 3600000)).padStart(2, '0');
                      const mm  = String(Math.floor((abs % 3600000) / 60000)).padStart(2, '0');
                      const ss  = String(Math.floor((abs % 60000) / 1000)).padStart(2, '0');

                      if (distance < 0) {
                        el.style.setProperty('color', '#ff3e1d', 'important');
                        el.textContent = `Breached (-${hh}:${mm}:${ss})`;
                      } else {
                        el.style.color = '';
                        el.textContent = `${hh}:${mm}:${ss}`;
                      }
                    });
                  }

                  updateTimers();
                  setInterval(updateTimers, 1000);
                })();

                // ── ApexCharts Donut Chart ────────────────────────────────────
                (async function renderKategoriChart() {
                  const chartEl = document.querySelector('#kategoriDonutChart');
                  if (!chartEl) return;

                  try {
                    const res  = await fetch('api/get_chart_data.php');
                    const data = await res.json();

                    if (data.status === 'error' || !data.series?.length) {
                      chartEl.innerHTML = '<div class="text-muted text-center py-5">Belum ada data untuk ditampilkan</div>';
                      return;
                    }

                    const chartColors = ['#004b87', '#03c3ec', '#ffab00', '#ff3e1d', '#71dd37'];

                    new ApexCharts(chartEl, {
                      series: data.series,
                      labels: data.labels,
                      chart: { type: 'donut', height: 290, parentHeightOffset: 0 },
                      colors: chartColors.slice(0, data.series.length),
                      stroke: { width: 5, colors: ['#fff'] },
                      legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        labels: { colors: '#566a7f', useSeriesColors: false }
                      },
                      dataLabels: { enabled: false },
                      plotOptions: {
                        pie: {
                          donut: {
                            size: '70%',
                            labels: {
                              show: true,
                              value: {
                                show: true,
                                fontSize: '1.25rem',
                                fontFamily: 'Public Sans',
                                color: '#566a7f',
                                offsetY: -10,
                                formatter: val => parseInt(val) + ' Tiket'
                              },
                              total: {
                                show: true,
                                label: 'Total',
                                color: '#a1acb8',
                                fontSize: '0.85rem',
                                formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' Tiket'
                              }
                            }
                          }
                        }
                      }
                    }).render();

                  } catch (err) {
                    console.error('Chart load failed:', err);
                    chartEl.innerHTML = '<div class="text-muted text-center py-5">Gagal memuat grafik</div>';
                  }
                })();

              });
            </script>

          </div>
          <!-- / Content wrapper -->

        </div>
        <!-- / Layout page -->

      </div>
      <!-- / Layout container -->
    </div>
    <!-- / Layout wrapper -->
  </body>
</html>
