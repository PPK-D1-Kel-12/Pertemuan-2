# Jara — Aplikasi Manajemen Task (Individual & Tim)

Aplikasi web manajemen task modern yang mendukung penggunaan individual maupun kolaborasi tim, dibangun menggunakan **Laravel Blade**, **Bootstrap 5.3**, dan **Bootstrap Icons**.

Repositori ini merupakan baseline Front-End lengkap yang siap dijalankan dan dikembangkan lebih lanjut untuk pengumpulan tugas kelompok **PPK-D1-Kel-12 (Pertemuan 2)**.

---

## 👥 Pembagian Modul Tim

| Anggota Tim | Tanggung Jawab Modul | File Utama / Komponen |
|---|---|---|
| **Novelya** | **Manajemen Task & List** (CRUD Task dasar, Buat/Hapus List, Pindah Task antar List - SRS-F-01 s/d SRS-F-04) | `app/Http/Controllers/ListController.php`<br>`resources/views/tasks/index.blade.php`<br>`resources/views/tasks/partials/modal-create.blade.php` |
| **Joshua** | **Atribut Task** (Tingkat Prioritas Tinggi/Sedang/Rendah, Deadline, & Tag Task Selesai - SRS-F-05 s/d SRS-F-07) | `resources/views/tasks/partials/task-row.blade.php`<br>`resources/views/tasks/partials/offcanvas-detail.blade.php` |
| **Menza** | **Kolaborasi & Pemantauan Progres** (Add/Kick Collaborator, Role Viewer vs Editor, & Linimasa Progres Kronologis - SRS-F-08 s/d SRS-F-11) | `resources/views/tasks/partials/collaborators-section.blade.php`<br>`resources/views/tasks/partials/activity-timeline.blade.php`<br>`resources/views/tasks/partials/avatar-stack.blade.php` |
| **Iza** | **Panel Administrasi** (Kelola Akun Pengguna, Nonaktifkan/Hapus User, & Log Audit Admin - SRS-F-14 s/d SRS-F-16) | `app/Http/Controllers/AdminController.php`<br>`resources/views/admin/users.blade.php`<br>`resources/views/admin/logs.blade.php` |

---

## ⚡ Fitur Utama Front-End

1. **List / Table View ala Todoist & Notion**:
   - Task dikelompokkan berdasarkan status: *In Progress*, *To Do*, dan *Selesai*.
   - Filter cepat: *Semua*, *Dibuat oleh Saya*, *Saya sebagai Kolaborator*, dan *Prioritas Tinggi*.
2. **Offcanvas Drawer (Panel Samping Detail Task)**:
   - Panel detail meluncur halus dari sisi kanan tanpa me-refresh halaman.
   - Pindah list langsung via dropdown.
   - Edit judul, deskripsi, prioritas, dan deadline.
3. **Manajemen Kolaborator Lengkap (Menza)**:
   - Tambah kolaborator dengan role **Editor** (*bisa ubah status & kirim catatan*) atau **Viewer** (*hanya lihat/read-only*).
   - Tombol hapus/keluarkan kolaborator (khusus pemilik task).
   - Tombol keluar dari task (bagi kolaborator).
4. **Linimasa Progres Kronologis (Menza)**:
   - Riwayat otomatis perubahan status dan penambahan anggota.
   - Input catatan progres langsung ke linimasa.
5. **Panel Admin (Iza)**:
   - Manajemen akun pengguna: tambah user, aktivasi/deaktivasi, hapus akun.
   - Halaman pencatatan audit log aktivitas admin.
6. **Autentikasi (Login & Self-Registration)**:
   - Halaman login dengan tombol cepat demo persona akun.
   - Halaman registrasi mandiri untuk user baru.
7. **Demo Persona Switcher**:
   - Dropdown pada navbar untuk beralih instan antara **Menza (Owner)**, **Budi (Editor)**, **Siti (Viewer)**, dan **Iza (Admin)** guna menguji hak akses langsung.

---

## 🛠️ Cara Menjalankan Proyek

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/PPK-D1-Kel-12/Pertemuan-2.git
   cd Pertemuan-2
   ```

2. **Install Dependensi & Setup Environment**:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Jalankan Server**:
   ```bash
   php artisan serve
   ```
   Buka di browser: **`http://localhost:8000`**

4. **Jalankan Pengujian Otomatis**:
   ```bash
   php artisan test
   ```
