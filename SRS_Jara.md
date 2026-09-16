# Spesifikasi Kebutuhan Sistem (SRS)
## Aplikasi Jara — Task & Team Management

---

## 1. Kebutuhan Fungsional Minggu Lalu (Pertemuan 2 — Baseline)

Berikut adalah rekapitulasi kebutuhan fungsional iterasi minggu lalu yang dikelompokkan berdasarkan status implementasinya:

### 1.1 Fitur yang Sudah Dikerjakan (Implemented)

| ID | Modul | Deskripsi Kebutuhan Fungsional | Status |
|---|---|---|:---:|
| **SRS-F-01** | Task Management | Sistem memungkinkan pengguna membuat task baru dengan atribut: judul, deskripsi, prioritas, deadline, dan status. | **Selesai** |
| **SRS-F-02** | Task Management | Sistem memungkinkan pengguna mengedit dan menghapus task miliknya melalui drawer detail task. | **Selesai** |
| **SRS-F-03** | List Management | Sistem memungkinkan pengguna mengelompokkan task ke dalam daftar (list) serta membuat list baru. | **Selesai** |
| **SRS-F-04** | List Management | Sistem memungkinkan pengguna memindahkan task antar list melalui menu dropdown detail task. | **Selesai** |
| **SRS-F-05** | Task Attributes | Sistem menyediakan 3 tingkat prioritas task: *Tinggi*, *Sedang*, dan *Rendah* dengan visualisasi badge warna. | **Selesai** |
| **SRS-F-06** | Task Attributes | Sistem memungkinkan pengguna menetapkan tanggal deadline pada task dan menampilkan indikator jatuh tempo. | **Selesai** |
| **SRS-F-07** | Task Attributes | Sistem menyediakan penandaan status task (*To Do*, *In Progress*, *Selesai*) beserta pengelompokan baris task otomatis. | **Selesai** |
| **SRS-F-08** | Kolaborasi | Sistem memungkinkan pemilik task menambahkan kolaborator ke sebuah task dengan role *Editor* atau *Viewer*. | **Selesai** |
| **SRS-F-09** | Kolaborasi | Sistem memungkinkan pemilik task mengeluarkan kolaborator dari task, serta kolaborator dapat keluar mandiri. | **Selesai** |
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

Iterasi minggu ini berfokus pada 3 User Story baru yang dirangkum menjadi **4 SRS Utama**, dengan pembagian masing-masing anggota tim memegang penuh **2 SRS** secara menyeluruh (mencakup antarmuka dan logikanya):

- **User Story 1** $\rightarrow$ dipecah menjadi **SRS-F-17** dan **SRS-F-18** (Dikelola oleh **Anggota 1**).
- **User Story 2** $\rightarrow$ **SRS-F-19** (Dikelola oleh **Anggota 2**).
- **User Story 3** $\rightarrow$ **SRS-F-20** (Dikelola oleh **Anggota 2**).

### 2.1 Matriks 4 SRS Utama & Penanggung Jawab (2 SRS per Orang)

| ID | Asal User Story | Deskripsi Kebutuhan Fungsional | Penanggung Jawab (End-to-End) | Rincian Deliverable |
|---|---|---|:---:|---|
| **SRS-F-17** | **User Story 1** (Bagian 1) | **Pembuatan List Baru & Kepemilikan Otomatis (Auto-Ownership)**<br>Sistem memungkinkan pengguna membuat daftar tugas (List) baru dan secara otomatis menetapkan pengguna tersebut sebagai pemilik (*Owner*). Antarmuka sidebar menampilkan status visual pembeda antara list yang dimiliki (*Owner*) dan list yang dibagikan kepadanya (*Collaborator*). | **Anggota 1** | • Modal form buat list baru.<br>• Rendering badge *Owner* vs *Collaborator* di sidebar.<br>• Logika backend penetapan `owner_id = auth()->id()`. |
| **SRS-F-18** | **User Story 1** (Bagian 2) | **Penghapusan List Kaskade Atomik & Rollback (Atomic Cascade Deletion)**<br>Sistem memungkinkan pemilik list menghapus list miliknya beserta seluruh tugas (*tasks*), relasi keanggotaan (*collaborators*), dan catatan progres di dalamnya secara serempak. Operasi wajib bersifat **atomik** menggunakan transaksi basis data (`DB::beginTransaction`); jika salah satu langkah penghapusan gagal, seluruh operasi langsung di-rollback dan data dikembalikan utuh ke kondisi semula. | **Anggota 1** | • Modal safeguard konfirmasi hapus kaskade (menampilkan jumlah task & anggota yang akan terhapus).<br>• Flash toast / alert UI saat terjadi rollback.<br>• Transaksi database kaskade delete dengan blok `try-catch` rollback. |
| **SRS-F-19** | **User Story 2** | **Penolakan Request Pengguna Tidak Berwenang (Authorization & HTTP 403 Rejection)**<br>Sistem wajib menolak setiap request modifikasi atau penghapusan list dan tugas dari pengguna yang bukan pemilik sah. Penolakan dilakukan di sisi server dengan mengembalikan kode status **HTTP 403 Forbidden**, serta di sisi antarmuka dengan menyembunyikan/menonaktifkan tombol aksi terlarang dan menyediakan halaman antarmuka khusus error 403. | **Anggota 2** | • Blade UI Guard (menyembunyikan tombol hapus untuk non-owner).<br>• Desain halaman antarmuka `resources/views/errors/403.blade.php`.<br>• Middleware / Policy otorisasi di server untuk validasi kepemilikan dan reject 403. |
| **SRS-F-20** | **User Story 3** | **Penerapan Prepared Statement pada Kueri Basis Data**<br>Seluruh operasi manipulasi data (SELECT, INSERT, UPDATE, DELETE) pada sistem wajib menggunakan mekanisme **Prepared Statement** dengan parameter binding (`?` atau `:param`) untuk menjamin keamanan dari serangan SQL Injection, didukung dengan validasi input pada form antarmuka pengguna. | **Anggota 2** | • Validasi input tipe dan panjang karakter pada form antarmuka klien.<br>• Implementasi kueri basis data pada repository/service menggunakan parameterized binding PDO murni / Query Builder binding. |

---

### 2.2 Rangkuman Tanggung Jawab Tim (Setiap Orang 2 SRS)

#### 🧑‍💻 **Anggota 1 — Penanggung Jawab Fitur Siklus Hidup List (SRS-F-17 & SRS-F-18):**
1. **SRS-F-17 (Pembuatan List & Auto-Owner):**
   - Merancang form modal pembuatan list baru dan penanda visual badge *Owner* di sidebar.
   - Mengimplementasikan logika backend penyimpanan list yang otomatis mengikat `owner_id` ke ID pengguna yang login.
2. **SRS-F-18 (Penghapusan Kaskade Atomik & Rollback):**
   - Merancang modal konfirmasi safeguard kaskade yang menampilkan jumlah task dan anggota yang terdampak.
   - Mengimplementasikan eksekusi transaksi basis data atomik (`DB::beginTransaction`, penghapusan relasi catatan $\to$ kolaborator $\to$ task $\to$ list, `DB::commit`, dan `DB::rollBack` saat exception).
   - Menangani tampilan notifikasi UI jika transaksi dibatalkan (*rollback*).

#### 🛡️ **Anggota 2 — Penanggung Jawab Fitur Otorisasi & Keamanan Sistem (SRS-F-19 & SRS-F-20):**
1. **SRS-F-19 (Penolakan Request Pengguna Tidak Berwenang / HTTP 403):**
   - Mengamankan antarmuka pengguna dengan menyembunyikan atau menonaktifkan tombol hapus/ubah jika user bukan pemilik list.
   - Merancang dan membuat halaman khusus `resources/views/errors/403.blade.php`.
   - Mengimplementasikan Middleware atau Laravel Policy di server untuk memverifikasi hak kepemilikan dan mengembalikan respon HTTP 403 jika tidak berwenang.
2. **SRS-F-20 (Penerapan Prepared Statement Basis Data):**
   - Memastikan form input di sisi klien memiliki validasi yang tepat untuk mencegah data rusak/malformasi.
   - Mengimplementasikan seluruh kueri database pada modul List dan Task menggunakan Prepared Statement (parameter binding `?` atau `:param`) agar 100% aman dari SQL Injection.
