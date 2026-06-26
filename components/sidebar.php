<?php
/**
 * Reusable Sidebar Component
 * /components/sidebar.php
 *
 * Required variables (set BEFORE including this file):
 *   $active_page  (string) — The key of the currently active menu item.
 *                            Admin keys:    'dashboard_admin', 'manajemen_tiket',
 *                                           'admin_kategori', 'manajemen_mahasiswa'
 *                            Student keys:  'dashboard_mahasiswa', 'buat_tiket',
 *                                           'riwayat_mahasiswa'
 *
 *   $base_path    (string) — Relative path prefix to reach the project root.
 *                            Root-level pages  : ''   (empty string)
 *                            /admin pages      : '../'
 *                            /components pages : '../'
 *
 * Example usage (from /admin/dashboard_admin.php):
 *   $active_page = 'dashboard_admin';
 *   $base_path   = '../';
 *   include '../components/sidebar.php';
 */

// Fallback defaults so the component never throws an undefined-variable error
if (!isset($active_page)) $active_page = '';
if (!isset($base_path))   $base_path   = '';

// Helper: returns 'active' if $key matches $active_page, otherwise ''
function sidebar_active(string $key, string $active_page): string {
    return ($key === $active_page) ? 'active' : '';
}

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin');
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="<?php echo $base_path . ($is_admin ? 'dashboard_admin.php' : 'dashboard_mahasiswa.php'); ?>"
       class="app-brand-link d-flex align-items-center">
      <img src="<?php echo $base_path; ?>assets/img/logo-maranatha.png"
           alt="Logo UK Maranatha" width="48"
           style="height: auto; object-fit: contain;" class="me-2">
      <div class="d-flex flex-column">
        <span class="app-brand-text demo menu-text fw-bold"
              style="font-size: 1.15rem; line-height: 1.1; text-transform: none; letter-spacing: -0.5px;">
          E-Complaint
        </span>
        <span class="text-muted fw-semibold"
              style="font-size: 0.65rem; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px;">
          UK Maranatha
        </span>
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

      <!-- ── Admin Menu Items ───────────────────── -->
      <li class="menu-item <?php echo sidebar_active('dashboard_admin', $active_page); ?>">
        <a href="<?php echo $base_path; ?>dashboard_admin.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-smile"></i>
          <div class="text-truncate" data-i18n="Dashboard Admin">Dashboard Admin</div>
        </a>
      </li>

      <li class="menu-item <?php echo sidebar_active('manajemen_tiket', $active_page); ?>">
        <a href="<?php echo $base_path; ?>manajemen_tiket.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-list-ul"></i>
          <div class="text-truncate" data-i18n="Manajemen Tiket">Manajemen Tiket</div>
        </a>
      </li>

      <li class="menu-item <?php echo sidebar_active('admin_kategori', $active_page); ?>">
        <a href="<?php echo $base_path; ?>admin_kategori.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-category"></i>
          <div class="text-truncate" data-i18n="Master Kategori">Master Kategori</div>
        </a>
      </li>

      <li class="menu-item <?php echo sidebar_active('manajemen_mahasiswa', $active_page); ?>">
        <a href="<?php echo $base_path; ?>manajemen_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div class="text-truncate" data-i18n="Master Mahasiswa">Master Mahasiswa</div>
        </a>
      </li>

    <?php else: ?>

      <!-- ── Student Menu Items ─────────────────── -->
      <li class="menu-item <?php echo sidebar_active('dashboard_mahasiswa', $active_page); ?>">
        <a href="<?php echo $base_path; ?>dashboard_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-smile"></i>
          <div class="text-truncate" data-i18n="Beranda">Beranda</div>
        </a>
      </li>

      <li class="menu-item <?php echo sidebar_active('buat_tiket', $active_page); ?>">
        <a href="<?php echo $base_path; ?>buat_tiket.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-edit"></i>
          <div class="text-truncate" data-i18n="Buat Tiket">Buat Tiket</div>
        </a>
      </li>

      <li class="menu-item <?php echo sidebar_active('riwayat_mahasiswa', $active_page); ?>">
        <a href="<?php echo $base_path; ?>riwayat_mahasiswa.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-history"></i>
          <div class="text-truncate" data-i18n="Riwayat Pengaduan">Riwayat Pengaduan</div>
        </a>
      </li>

    <?php endif; ?>

  </ul>
</aside>
