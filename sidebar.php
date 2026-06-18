<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="<?php echo $is_admin ? 'dashboard_admin.php' : 'dashboard_mahasiswa.php'; ?>" class="app-brand-link d-flex align-items-center">
      <img src="assets/img/logo-maranatha.png" alt="Logo UK Maranatha" width="48" style="height: auto; object-fit: contain;" class="me-2">
      <div class="d-flex flex-column">
        <span class="app-brand-text demo menu-text fw-bold" style="font-size: 1.15rem; line-height: 1.1; text-transform: none; letter-spacing: -0.5px;">E-Complaint</span>
        <span class="text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px;">UK Maranatha</span>
      </div>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <?php if ($is_admin): ?>
      <li class="menu-item <?php echo ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
        <a href="dashboard_admin.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-smile"></i>
          <div class="text-truncate" data-i18n="Dashboard Admin">Dashboard Admin</div>
        </a>
      </li>
      <li class="menu-item <?php echo ($current_page == 'manajemen_tiket.php') ? 'active' : ''; ?>">
        <a href="manajemen_tiket.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-list-ul"></i>
          <div class="text-truncate" data-i18n="Manajemen Tiket">Manajemen Tiket</div>
        </a>
      </li>
      <li class="menu-item <?php echo ($current_page == 'admin_kategori.php') ? 'active' : ''; ?>">
        <a href="admin_kategori.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-category"></i>
          <div class="text-truncate" data-i18n="Master Kategori">Master Kategori</div>
        </a>
      </li>
      <li class="menu-item <?php echo ($current_page == 'manajemen_mahasiswa.php') ? 'active' : ''; ?>">
        <a href="manajemen_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div class="text-truncate" data-i18n="Master Mahasiswa">Master Mahasiswa</div>
        </a>
      </li>
    <?php else: ?>
      <li class="menu-item <?php echo ($current_page == 'dashboard_mahasiswa.php') ? 'active' : ''; ?>">
        <a href="dashboard_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-smile"></i>
          <div class="text-truncate" data-i18n="Beranda">Beranda</div>
        </a>
      </li>

      <li class="menu-item <?php echo ($current_page == 'buat_tiket.php') ? 'active' : ''; ?>">
        <a href="buat_tiket.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-edit"></i>
          <div class="text-truncate" data-i18n="Buat Tiket">Buat Tiket</div>
        </a>
      </li>

      <li class="menu-item <?php echo ($current_page == 'riwayat_mahasiswa.php') ? 'active' : ''; ?>">
        <a href="riwayat_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-history"></i>
          <div class="text-truncate" data-i18n="Riwayat Pengaduan">Riwayat Pengaduan</div>
        </a>
      </li>
    <?php endif; ?>
  </ul>
</aside>
