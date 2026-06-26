<?php
// Siapkan data identitas pengguna dari sesi
$nama_user  = 'Pengguna';
$role_user  = '';
$nrp_user   = '';
$email_user = '';
$nohp_user  = '';
$foto_user  = '';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'mahasiswa') {
        $nama_user  = $_SESSION['nama_mahasiswa'] ?? 'Mahasiswa';
        $role_user  = 'Mahasiswa';
        $nrp_user   = $_SESSION['nrp']          ?? '';
        $email_user = $_SESSION['email']         ?? '';
        $nohp_user  = $_SESSION['no_hp']         ?? '';
        $foto_user  = $_SESSION['foto_profil']   ?? '';
    } elseif ($_SESSION['role'] === 'admin') {
        $nama_user  = $_SESSION['nama_admin'] ?? 'Admin';
        $role_user  = 'Admin';
    }
}

// Ambil inisial nama untuk avatar teks
$initial  = strtoupper(substr($nama_user, 0, 1));
$is_mhs   = (($_SESSION['role'] ?? '') === 'mahasiswa');

// Tentukan URL foto profil jika file ada di server
$foto_url = ($foto_user && file_exists(__DIR__ . '/uploads/avatars/' . $foto_user))
            ? 'uploads/avatars/' . htmlspecialchars($foto_user)
            : null;
?>

<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
     id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

            <!-- Dropdown Avatar Pengguna -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <?php if ($foto_url): ?>
                            <img src="<?php echo $foto_url; ?>" alt="Avatar"
                                 class="rounded-circle object-fit-cover"
                                 style="width:40px; height:40px;">
                        <?php else: ?>
                            <span class="avatar-initial rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                  style="width:40px; height:40px; color:#fff; font-weight:bold;">
                                <?php echo $initial; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- Info nama pengguna di dropdown -->
                    <li>
                        <a class="dropdown-item" href="javascript:void(0)"
                           data-bs-toggle="modal" data-bs-target="#modalProfil">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <?php if ($foto_url): ?>
                                            <img src="<?php echo $foto_url; ?>" alt="Avatar"
                                                 class="rounded-circle object-fit-cover"
                                                 style="width:40px; height:40px;">
                                        <?php else: ?>
                                            <span class="avatar-initial rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                  style="width:40px; height:40px; color:#fff; font-weight:bold;">
                                                <?php echo $initial; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($nama_user); ?></h6>
                                    <small class="text-body-secondary"><?php echo htmlspecialchars($role_user); ?></small>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li><div class="dropdown-divider my-1"></div></li>

                    <li>
                        <a class="dropdown-item" href="javascript:void(0)"
                           data-bs-toggle="modal" data-bs-target="#modalProfil">
                            <i class="icon-base bx bx-user icon-md me-3"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>

                    <li><div class="dropdown-divider my-1"></div></li>

                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                            <span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- /Dropdown Avatar Pengguna -->

        </ul>
    </div>
</nav>


<!-- ============================================================ -->
<!-- MODAL PROFIL PENGGUNA                                        -->
<!-- ============================================================ -->
<div class="modal fade" id="modalProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- Tampilan khusus untuk mahasiswa -->
                <?php if ($is_mhs): ?>

                <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">

                    <!-- Avatar yang bisa diklik untuk ganti foto -->
                    <div class="text-center mb-4">
                        <div id="avatarWrap" style="position:relative; display:inline-block; cursor:pointer;"
                             onclick="document.getElementById('inputFoto').click()" title="Klik untuk ganti foto">

                            <span id="avatarInitial"
                                  class="avatar-initial rounded-circle bg-primary d-flex align-items-center justify-content-center <?php echo $foto_url ? 'd-none' : ''; ?>"
                                  style="width:80px; height:80px; font-size:2rem; color:#fff; font-weight:bold; margin:0 auto;">
                                <?php echo $initial; ?>
                            </span>

                            <img id="avatarImg"
                                 src="<?php echo $foto_url ?: ''; ?>"
                                 alt="Foto Profil"
                                 class="rounded-circle object-fit-cover <?php echo $foto_url ? '' : 'd-none'; ?>"
                                 style="width:80px; height:80px;">

                            <!-- Ikon kamera di pojok avatar -->
                            <div style="position:absolute; bottom:0; right:0; background:#004b87; border-radius:50%;
                                        width:24px; height:24px; display:flex; align-items:center; justify-content:center;">
                                <i class="bx bx-camera" style="color:#fff; font-size:0.85rem;"></i>
                            </div>
                        </div>
                        <input type="file" id="inputFoto" name="foto_profil" accept="image/jpeg,image/png" class="d-none">
                        <small class="text-muted d-block mt-1">Klik foto untuk mengganti (maks. 2 MB)</small>
                    </div>

                    <!-- Data read-only (tidak bisa diubah) -->
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted mb-1">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-sm bg-light"
                                   value="<?php echo htmlspecialchars($nama_user); ?>" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted mb-1">NRP</label>
                            <input type="text" class="form-control form-control-sm bg-light"
                                   value="<?php echo htmlspecialchars($nrp_user); ?>" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Email</label>
                            <input type="text" class="form-control form-control-sm bg-light"
                                   value="<?php echo htmlspecialchars($email_user); ?>" disabled>
                        </div>
                    </div>

                    <!-- No. HP bisa diubah oleh mahasiswa -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold mb-1" for="inputNoHp">No. HP</label>
                        <input type="tel" id="inputNoHp" name="no_hp"
                               class="form-control form-control-sm"
                               value="<?php echo htmlspecialchars($nohp_user); ?>"
                               placeholder="08xxxxxxxxxx" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 mb-3">
                        <i class="bx bx-save me-1"></i> Simpan Perubahan
                    </button>
                </form>

                <?php endif; /* akhir tampilan mahasiswa */ ?>


                <!-- Ganti Password (tersedia untuk semua role) -->
                <div class="accordion accordion-flush" id="accordionPassword">
                    <div class="accordion-item border rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2 small fw-semibold rounded" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapsePassword" aria-expanded="false">
                                <i class="bx bx-lock-alt me-2 text-primary"></i> Ganti Password
                            </button>
                        </h2>
                        <div id="collapsePassword" class="accordion-collapse collapse">
                            <div class="accordion-body pt-2 pb-3">
                                <form action="proses_update_profil.php" method="POST">
                                    <input type="hidden" name="action" value="change_password">

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-1">Password Lama</label>
                                        <input type="password" name="old_password"
                                               class="form-control form-control-sm"
                                               placeholder="••••••" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-1">Password Baru</label>
                                        <input type="password" name="new_password" id="inputPasswordBaru"
                                               class="form-control form-control-sm"
                                               placeholder="Min. 6 karakter" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold mb-1">Konfirmasi Password Baru</label>
                                        <input type="password" name="confirm_password" id="inputKonfirmasiPass"
                                               class="form-control form-control-sm"
                                               placeholder="Ulangi password baru" required>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bx bx-key me-1"></i> Ubah Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /modal-body -->

            <div class="modal-footer border-0 pt-0 justify-content-between">
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bx bx-power-off me-1"></i> Log Out
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
<!-- /Modal Profil -->


<script>
// Preview foto profil saat dipilih (sebelum di-submit)
document.addEventListener('DOMContentLoaded', function () {
    var inputFoto = document.getElementById('inputFoto');
    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            // Cek ukuran maksimal 2 MB di sisi klien sebelum upload
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2 MB.');
                this.value = '';
                return;
            }

            // Tampilkan preview foto yang baru dipilih
            var reader = new FileReader();
            reader.onload = function (e) {
                var avatarImg     = document.getElementById('avatarImg');
                var avatarInitial = document.getElementById('avatarInitial');
                avatarImg.src = e.target.result;
                avatarImg.classList.remove('d-none');
                if (avatarInitial) {
                    avatarInitial.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
