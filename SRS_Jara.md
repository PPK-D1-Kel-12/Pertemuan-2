# Spesifikasi Kebutuhan Sistem (SRS)
## Aplikasi Jara — Task & Team Management

---

## 1. Kebutuhan Fungsional Minggu Lalu (Pertemuan 2 — Baseline)

Berikut adalah rekapitulasi kebutuhan fungsional dari iterasi minggu lalu yang dikelompokkan berdasarkan status implementasinya pada sistem:

### 1.1 Fitur yang Sudah Dikerjakan (Implemented)

| ID | Modul | Deskripsi Kebutuhan Fungsional | Status |
|---|---|---|:---:|
| **SRS-F-01** | Task Management | Sistem memungkinkan pengguna membuat task baru dengan atribut: judul, deskripsi, prioritas, deadline, dan status. | **Selesai** |
| **SRS-F-02** | Task Management | Sistem memungkinkan pengguna mengedit dan menghapus task miliknya melalui antarmuka drawer detail task. | **Selesai** |
| **SRS-F-03** | List Management | Sistem memungkinkan pengguna mengelompokkan task ke dalam daftar (list) serta membuat list baru. | **Selesai** |
| **SRS-F-04** | List Management | Sistem memungkinkan pengguna memindahkan task antar list melalui menu dropdown detail task. | **Selesai** |
| **SRS-F-05** | Task Attributes | Sistem menyediakan 3 tingkat prioritas task: *Tinggi*, *Sedang*, dan *Rendah* dengan visualisasi badge warna. | **Selesai** |
| **SRS-F-06** | Task Attributes | Sistem memungkinkan pengguna menetapkan tanggal deadline pada task dan menampilkan indikator jatuh tempo. | **Selesai** |
| **SRS-F-07** | Task Attributes | Sistem menyediakan penandaan status task (*To Do*, *In Progress*, *Selesai*) beserta pengelompokan baris task otomatis. | **Selesai** |
| **SRS-F-08** | Kolaborasi | Sistem memungkinkan pemilik task menambahkan kolaborator ke sebuah task dengan role *Editor* atau *Viewer*. | **Selesai** |
| **SRS-F-09** | Kolaborasi | Sistem memungkinkan pemilik task mengeluarkan/menghapus kolaborator dari task, serta kolaborator dapat keluar mandiri. | **Selesai** |
| **SRS-F-11** | Pemantauan Progres | Sistem mencatat linimasa aktivitas perubahan status, penambahan anggota, dan penambahan catatan progres kronologis. | **Selesai** |
| **SRS-F-14** | Admin Panel | Sistem menyediakan panel admin untuk menambahkan akun pengguna baru ke dalam sistem. | **Selesai** |
| **SRS-F-15** | Admin Panel | Sistem menyediakan fitur bagi admin untuk menonaktifkan (*deactivate*) atau menghapus akun pengguna. | **Selesai** |
| **SRS-F-16** | Admin Panel | Sistem mencatat dan menampilkan log audit aktivitas admin terkait manipulasi akun pengguna. | **Selesai** |

### 1.2 Fitur yang Belum Dikerjakan (Pending / Backlog)

| ID | Modul | Deskripsi Kebutuhan Fungsional | Status | Catatan Evaluasi |
|---|---|---|:---:|---|
| **SRS-F-10** | Notifikasi | Sistem mengirimkan notifikasi real-time / email kepada kolaborator saat ditambahkan ke dalam task. | **Belum Dikerjakan** | Belum diimplementasikan karena fokus awal pada struktur CRUD dan linimasa aktivitas lokal. |
| **SRS-F-12** | Tim Dinamis | Sistem memungkinkan pembuatan dan pengelolaan struktur tim baru secara dinamis (beserta pergantian ketua tim). | **Belum Dikerjakan** | Masih menggunakan data mock statis tim ("Tim Praktikum PPK"), belum ada antarmuka kelola tim. |
| **SRS-F-13** | Task Tim Dinamis | Sistem menyaring visibilitas task tim secara dinamis berdasarkan keanggotaan tim pengguna yang login. | **Belum Dikerjakan** | Filter saat ini baru sebatas *Dibuat oleh Saya* dan *Saya sebagai Kolaborator*. |

---

## 2. Kebutuhan Fungsional Minggu Ini (Pertemuan 3 — Added Features)

Iterasi minggu ini berfokus pada 3 User Story baru:
1. **User Story 1:** Pembuatan list baru dengan kepemilikan otomatis (*Auto-Owner*) serta penghapusan list kaskade bersyarat atomik (*Atomic Cascade Deletion with Rollback*).
2. **User Story 2:** Penolakan terhadap request dari pengguna yang tidak berwenang (*Unauthorized Request Rejection / HTTP 403*).
3. **User Story 3:** Keharusan menggunakan **Prepared Statement** pada kueri basis data.

### 2.1 Matriks Kebutuhan Fungsional & Pembagian Kerja (2 Orang)

| ID | User Story Terkait | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Deliverable / Komponen |
|---|---|---|:---:|---|
| **SRS-F-17** | US 1: Auto-Owner List | Pengguna dapat membuat daftar tugas (List) baru. Sistem secara otomatis menetapkan ID pembuat sebagai pemilik sah list (`owner_id = auth_id`). | **Anggota 1** (UI Form Modal)<br>**Anggota 2** (Backend Insert Logic) | • `resources/views/tasks/partials/modal-create-list.blade.php`<br>• `app/Http/Controllers/ListController.php` |
| **SRS-F-18** | US 1: Indikator Kepemilikan | Antarmuka sidebar menampilkan status pembeda visual yang jelas antara List yang dimiliki (*Badge Owner*) dan List di mana pengguna hanya sebagai anggota/kolaborator. | **Anggota 1** (Front-End) | • `resources/views/layouts/app.blade.php` |
| **SRS-F-19** | US 1: Modal Safeguard Hapus | Sistem menyediakan modal konfirmasi interaktif sebelum menghapus list, menampilkan jumlah task dan anggota di dalamnya yang akan ikut terhapus permanen. | **Anggota 1** (Front-End) | • `resources/views/tasks/partials/modal-delete-list.blade.php` |
| **SRS-F-20** | US 1: Cascade Deletion | Sistem menghapus seluruh entitas anak secara berantai saat list dihapus: **Catatan Progres $\to$ Kolaborator Task $\to$ Seluruh Task $\to$ Entitas List**. | **Anggota 2** (Back-End) | • `app/Services/ListService.php` / Query Cascade Engine |
| **SRS-F-21** | US 1: Atomic Rollback | Penghapusan list dibungkus dalam mekanisme transaksi database (`DB::beginTransaction`). Jika terjadi kegagalan/error pada salah satu langkah kaskade, sistem membatalkan seluruh operasi (`DB::rollBack()`). | **Anggota 2** (Back-End) | • `app/Http/Controllers/ListController.php` (`try-catch` rollback block) |
| **SRS-F-22** | US 1: Notifikasi Rollback UI | Antarmuka pengguna menyajikan flash alert / toast peringatan ketika terjadi rollback transaksi atau kegagalan penghapusan. | **Anggota 1** (Front-End) | • `resources/views/layouts/app.blade.php` (Flash toast component) |
| **SRS-F-23** | US 2: UI Guard (Hide/Disable) | Antarmuka pengguna otomatis menyembunyikan atau menonaktifkan tombol "Hapus List" jika pengguna yang sedang aktif bukan pemilik dari list tersebut. | **Anggota 1** (Front-End) | • `resources/views/layouts/app.blade.php` (Kondisional Blade Auth check) |
| **SRS-F-24** | US 2: Server-Side Authorization | Sistem memeriksa hak akses di sisi server untuk setiap HTTP request mutasi (POST, PUT, DELETE). Akses modifikasi/penghapusan list oleh user non-owner langsung ditolak. | **Anggota 2** (Back-End) | • `app/Http/Middleware/EnsureListOwner.php` / `ListPolicy.php` |
| **SRS-F-25** | US 2: Respon HTTP 403 | Server mengembalikan kode status **HTTP 403 Forbidden** secara konsisten jika terdeteksi request tidak berwenang. | **Anggota 2** (Back-End) | • `abort(403, 'Akses Ditolak: Anda bukan pemilik list ini.')` |
| **SRS-F-26** | US 2: Halaman Error 403 | Sistem menyediakan tampilan halaman khusus untuk error 403 yang responsif, menyajikan pesan penolakan yang jelas, dan tautan kembali ke dashboard. | **Anggota 1** (Front-End) | • `resources/views/errors/403.blade.php` |
| **SRS-F-27** | US 3: Prepared Statements DB | Seluruh operasi manipulasi basis data (SELECT, INSERT, UPDATE, DELETE) pada modul List dan Task wajib menggunakan kueri berparameter (*Parameterized Binding / Prepared Statement*) guna mencegah SQL Injection. | **Anggota 2** (Back-End) | • Database Service / Repository dengan PDO Binding (`?` atau `:param`) |
| **SRS-F-28** | US 3: Validasi Form Klien | Form input pembuatan list dan task dilengkapi validasi sisi klien (tipe data, karakter khusus, dan panjang string) untuk mencegah input berbahaya sebelum kueri dieksekusi. | **Anggota 1** (Front-End) | • Form validation attributes & JS handler di Blade |

---

## 3. Kebutuhan Non-Fungsional Minggu Ini (Non-Functional Requirements)

| ID | Parameter | Spesifikasi | Penanggung Jawab |
|---|---|---|:---:|
| **SRS-NF-01** | **Security (SQL Injection)** | Sistem 100% terlindung dari kerentanan SQL Injection dengan melarang konkatenasi string SQL mentah dan mewajibkan pemisahan template kueri dari parameter input (*Prepared Statements*). | **Anggota 2** (Back-End) |
| **SRS-NF-02** | **Security (Access Control)** | Otorisasi hak akses divalidasi ganda: di sisi presentasi antarmuka (*Blade UI Guard*) dan di sisi server (*Route Middleware / Policy*). Setiap pelanggaran wajib mengembalikan respon HTTP 403. | **Anggota 1** (UI)<br>**Anggota 2** (Server) |
| **SRS-NF-03** | **Data Integrity (ACID Atomicity)** | Operasi penghapusan kaskade memenuhi prinsip Atomicity: seluruh entitas anak dan induk terhapus secara tuntas, atau dibatalkan seutuhnya (*Rollback*) tanpa meninggalkan data yatim (*orphan records*). | **Anggota 2** (Back-End) |
| **SRS-NF-04** | **Usability (UX Safeguard)** | Pencegahan salah klik aksi berbahaya melalui penyediaan modal dialog konfirmasi peringatan merah (*danger state*) dan indikator jumlah data terdampak. | **Anggota 1** (Front-End) |

---

## 4. Rincian Tanggung Jawab 2 Orang Anggota

### 🎨 **Anggota 1 — Lead Front-End & UI Security Specialist**
1. **Navigasi & Visual Kepemilikan (SRS-F-18):** Merancang visualisasi kepemilikan list pada sidebar (*Owner badge* vs *Collaborator*).
2. **Modal Safeguard Konfirmasi Kaskade (SRS-F-19, SRS-NF-04):** Membuat modal peringatan bahaya sebelum hapus list beserta ringkasan jumlah task/anggota yang akan terhapus.
3. **UI Guarding (SRS-F-23, SRS-NF-02):** Menyembunyikan atau menonaktifkan tombol hapus jika bukan owner list (`@if($list->owner_id === Auth::id())`).
4. **Halaman & Feedback Error (SRS-F-22, SRS-F-26):** Mendesain halaman `resources/views/errors/403.blade.php` dan komponen banner/toast saat transaksi mengalami rollback.
5. **Validasi Form Klien (SRS-F-17, SRS-F-28):** Menambahkan validasi input form pembuatan list/task di sisi antarmuka pengguna.

### 🛠️ **Anggota 2 — Lead Back-End & Data Security Specialist**
1. **Skema Basis Data & Logic Insert (SRS-F-17):** Merancang tabel relasional `lists`, `tasks`, `task_collaborators`, dan `progress_notes` dengan otomatisasi `owner_id`.
2. **Prepared Statements Layer (SRS-F-27, SRS-NF-01):** Membangun kueri database menggunakan parameter binding murni pada seluruh operasi CRUD List & Task.
3. **Engine Transaksi Atomik & Rollback (SRS-F-20, SRS-F-21, SRS-NF-03):** Mengimplementasikan blok `DB::beginTransaction()`, penghapusan kaskade berurutan, `DB::commit()`, dan `DB::rollBack()` saat terjadi kegagalan.
4. **Middleware Otorisasi & Respon 403 (SRS-F-24, SRS-F-25, SRS-NF-02):** Membangun middleware/policy pemeriksa kepemilikan list di sisi server dan mengembalikan respon HTTP 403 jika request tidak sah.
