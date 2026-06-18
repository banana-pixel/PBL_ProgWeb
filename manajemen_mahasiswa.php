<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $nrp = mysqli_real_escape_string($koneksi, $_POST['nrp']);
    $status_akademik = mysqli_real_escape_string($koneksi, $_POST['status_akademik']);
    
    $query = "UPDATE Mahasiswa SET status_akademik = '$status_akademik' WHERE nrp = '$nrp'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Status akademik mahasiswa berhasil diperbarui!');
                window.location.href='manajemen_mahasiswa.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Gagal memperbarui status: " . mysqli_real_escape_string($koneksi, mysqli_error($koneksi)) . "');
                window.location.href='manajemen_mahasiswa.php';
              </script>";
        exit;
    }
}

$query_mhs = "SELECT * FROM Mahasiswa ORDER BY nrp ASC";
$result_mhs = mysqli_query($koneksi, $query_mhs);
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

    <title>Master Mahasiswa - E-Complaint</title>

    <meta name="description" content="Master Data Mahasiswa E-Complaint" />

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
        
        <!-- Sidebar (Menu) -->
        <?php include 'sidebar.php'; ?>
        <!-- / Sidebar (Menu) -->

        <!-- Layout container -->
        <div class="layout-page">
          
          <!-- Navbar -->
          <?php include 'navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Kelola Master Data Mahasiswa</h4>

              <!-- Tabel Data Mahasiswa -->
              <div class="card">
                <h5 class="card-header">Daftar Mahasiswa</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>NRP</th>
                        <th>Nama Mahasiswa</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Status Akademik</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php if (mysqli_num_rows($result_mhs) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_mhs)): ?>
                          <?php
                            $status = $row['status_akademik'] ?? 'Aktif';
                            $badge_class = 'bg-label-primary';
                            if ($status === 'Tidak Aktif') {
                                $badge_class = 'bg-label-danger';
                            } elseif ($status === 'Lulus') {
                                $badge_class = 'bg-label-success';
                            } elseif ($status === 'Keluar') {
                                $badge_class = 'bg-label-warning';
                            }
                          ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['nrp']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['no_hp'] ?? '-'); ?></td>
                            <td>
                              <span class="badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                              </span>
                            </td>
                            <td>
                              <button type="button" class="btn btn-sm btn-primary btn-edit-status" data-bs-toggle="modal" data-bs-target="#editStatusModal" data-nrp="<?php echo htmlspecialchars($row['nrp']); ?>" data-nama="<?php echo htmlspecialchars($row['nama_mahasiswa']); ?>" data-status="<?php echo htmlspecialchars($status); ?>">
                                Edit Status
                              </button>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center text-muted py-4">Belum ada data mahasiswa.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Tabel Data Mahasiswa -->
            </div>
            <!-- / Content -->

            <!-- Modal Edit Status Akademik -->
            <div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editStatusModalTitle">Edit Status Akademik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="manajemen_mahasiswa.php" method="POST">
                    <div class="modal-body">
                      <input type="hidden" name="nrp" id="modal_nrp" value="" />
                      
                      <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mahasiswa</label>
                        <input type="text" class="form-control-plaintext fs-6 ps-1" id="modal_nama" readonly />
                      </div>
                      
                      <div class="mb-3">
                        <label for="status_akademik" class="form-label fw-bold">Status Akademik</label>
                        <select class="form-select" id="modal_status" name="status_akademik" required>
                          <option value="Aktif">Aktif</option>
                          <option value="Tidak Aktif">Tidak Aktif</option>
                          <option value="Lulus">Lulus</option>
                          <option value="Keluar">Keluar</option>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" name="update_status" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- / Modal Edit Status Akademik -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                var editButtons = document.querySelectorAll('.btn-edit-status');
                editButtons.forEach(function(button) {
                  button.addEventListener('click', function() {
                    var nrp = this.getAttribute('data-nrp');
                    var nama = this.getAttribute('data-nama');
                    var status = this.getAttribute('data-status');
                    
                    document.getElementById('modal_nrp').value = nrp;
                    document.getElementById('modal_nama').value = nama;
                    document.getElementById('modal_status').value = status;
                  });
                });
              });
            </script>
  </body>
</html>
