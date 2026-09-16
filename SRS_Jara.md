# Software Requirements Specification (SRS)
## Jara — Aplikasi Manajemen Task (Individual & Tim)
### Edisi: Atomic Cascade Deletion, Authorization Guard & Prepared Statements

**Versi:** 1.0 (Revisi Pertemuan 3)  
**Mata Kuliah:** Praktikum Pemrograman Platform Khusus (PPK)  
**Status:** Disetujui untuk Implementasi  
**Target Pengembang:** 2 Orang Anggota Tim  

---

## 1. Pendahuluan

### 1.1 Tujuan
Dokumen ini mendefinisikan spesifikasi kebutuhan perangkat lunak untuk aplikasi **Jara**, sebuah sistem manajemen task berbasis web kolaboratif. Dokumen ini diperbarui secara khusus untuk mengakomodasi tiga kebutuhan inti baru:
1. Pembuatan list dengan penugasan kepemilikan otomatis (*Auto-Ownership*) dan penghapusan list secara menyeluruh beserta seluruh relasi tugasnya secara atomik (*Atomic Cascade Deletion with Rollback*).
2. Penolakan terhadap setiap request dari pengguna yang tidak berwenang (*Strict Authorization & HTTP 403 Forbidden*).
3. Penerapan query parameter binding menggunakan **Prepared Statement** pada seluruh operasi basis data untuk pencegahan SQL Injection.

### 1.2 Ruang Lingkup
Aplikasi Jara memfasilitasi manajemen task individual dan tim. Ruang lingkup revisi ini mencakup:
- Pembuatan dan kepemilikan daftar tugas (*List*).
- Mekanisme penghapusan kaskade yang bersifat *all-or-nothing* (ACID Transaction).
- Kontrol akses berbasis peran dan kepemilikan data (*Role & Ownership-Based Access Control*).
- Persistensi basis data yang aman dari eksploitasi injeksi SQL.

### 1.3 Definisi dan Istilah
| Istilah | Definisi |
|---|---|
| **List (Daftar Tugas)** | Wadah pengelompokan satu atau lebih task yang dibuat oleh pengguna. |
| **Owner (Pemilik List)** | Pengguna yang membuat list tersebut dan memiliki wewenang penuh atas modifikasi dan penghapusan list. |
| **Atomic Deletion** | Operasi penghapusan kaskade bersyarat di mana seluruh entitas anak (task, kolaborator, riwayat catatan) terhapus bersamaan, atau dibatalkan sepenuhnya jika terjadi kegagalan. |
| **Rollback** | Perintah pengembalian status basis data ke kondisi sebelum transaksi dimulai saat terjadi exception atau error. |
| **Prepared Statement** | Teknik eksekusi kueri SQL di mana template kueri dikompilasi terlebih dahulu oleh DBMS dan parameter data dikirim secara terpisah untuk mencegah SQL Injection. |
| **403 Forbidden** | Status kode HTTP yang menandakan bahwa server menolak mengeksekusi request karena klien tidak memiliki izin akses atas sumber daya tersebut. |

---

## 2. Deskripsi Umum

### 2.1 Perspektif Produk & Arsitektur
Jara dikembangkan dengan arsitektur Model-View-Controller (MVC) menggunakan **Laravel (Blade Template + Bootstrap 5.3)** di sisi Front-End dan Database Engine relasional (MySQL / SQLite / MariaDB) di sisi Back-End.

### 2.2 Karakteristik Pengguna & Hak Akses
1. **List Owner:**
   - Membuat list baru (otomatis menjadi owner).
   - Menambah, mengubah, dan memindahkan task di dalam list miliknya.
   - Menghapus list miliknya (menghapus seluruh task, kolaborator, dan catatan di dalamnya secara atomik).
2. **Collaborator / Member:**
   - Melihat list dan task yang dibagikan kepadanya.
   - Mengubah status task dan menambahkan catatan progres jika diberi hak Editor.
   - **Dilarang keras:** Menghapus list, mengubah nama list milik orang lain, atau menghapus task yang bukan miliknya.
3. **Unauthorized User:**
   - Pengguna luar atau pengguna yang tidak terdaftar dalam kepemilikan/kolaborasi list. Seluruh request dari pengguna ini **wajib ditolak (HTTP 403)**.

---

## 3. Kebutuhan Fungsional (Functional Requirements)

### 3.1 Modul List & Kepemilikan (List Ownership)
| ID | Deskripsi Kebutuhan | Penanggung Jawab |
|---|---|---|
| **SRS-F-01** | Pengguna yang sedang login dapat membuat daftar tugas (List) baru dengan memasukkan nama list. | Anggota 1 (UI) & Anggota 2 (Backend) |
| **SRS-F-02** | Sistem secara otomatis menetapkan ID pengguna yang sedang aktif sebagai `owner_id` dari list yang baru dibuat. | Anggota 2 (Backend Logic) |
| **SRS-F-03** | Tampilan antarmuka sidebar wajib membedakan secara visual antara List yang dimiliki pengguna (*Owner*) dan List yang dibagikan kepadanya (*Collaborator*). | Anggota 1 (Front-End) |

### 3.2 Modul Penghapusan Kaskade Atomik (Atomic Cascade Deletion)
| ID | Deskripsi Kebutuhan | Penanggung Jawab |
|---|---|---|
| **SRS-F-04** | Hanya pemilik list (*Owner*) yang memiliki akses dan hak untuk menghapus list tersebut. | Anggota 2 (Backend Auth) |
| **SRS-F-05** | Sistem wajib menyediakan modal dialog konfirmasi penghapusan list yang memberikan peringatan tegas mengenai jumlah task dan kolaborator yang akan ikut terhapus. | Anggota 1 (Front-End UX) |
| **SRS-F-06** | Saat list dihapus, sistem wajib menghapus seluruh entitas terkait secara berurutan: **Catatan Progres $\to$ Kolaborator Task $\to$ Task $\to$ List**. | Anggota 2 (Backend DB) |
| **SRS-F-07** | Seluruh proses penghapusan pada SRS-F-06 wajib dibungkus dalam **Database Transaction**. Jika salah satu langkah penghapusan gagal, sistem wajib melakukan **ROLLBACK** penuh sehingga tidak ada data yang terhapus sebagian. | Anggota 2 (Backend Integrity) |
| **SRS-F-08** | Sistem harus memberikan feedback antarmuka (Alert/Toast) yang jelas jika operasi penghapusan berhasil atau jika terjadi kegagalan/rollback. | Anggota 1 (Front-End UI) |

### 3.3 Modul Otorisasi & Penolakan Akses (Authorization & Error 403)
| ID | Deskripsi Kebutuhan | Penanggung Jawab |
|---|---|---|
| **SRS-F-09** | Antarmuka pengguna wajib menyembunyikan (*hide*) atau menonaktifkan (*disable*) tombol "Hapus List" jika pengguna yang sedang melihat bukan pemilik sah list tersebut. | Anggota 1 (Front-End UI Guard) |
| **SRS-F-10** | Sistem di sisi server wajib memvalidasi kepemilikan pada setiap HTTP Request (POST, PUT, DELETE) yang ditujukan ke sumber daya list/task. | Anggota 2 (Middleware / Policy) |
| **SRS-F-11** | Setiap request dari pengguna yang tidak berwenang wajib ditolak langsung dengan respon **HTTP 403 Forbidden**. | Anggota 2 (Backend Controller) |
| **SRS-F-12** | Sistem menyediakan halaman khusus penanganan error 403 (`resources/views/errors/403.blade.php`) yang ramah pengguna dan dilengkapi tombol navigasi kembali ke dashboard. | Anggota 1 (Front-End Error Page) |

### 3.4 Modul Keamanan Basis Data (Prepared Statements)
| ID | Deskripsi Kebutuhan | Penanggung Jawab |
|---|---|---|
| **SRS-F-13** | Seluruh kueri basis data (Create, Read, Update, Delete) pada modul List dan Task wajib menggunakan **Prepared Statement** dengan parameter binding (`?` atau named parameters `:param`). | Anggota 2 (Data Layer) |
| **SRS-F-14** | Form input pada antarmuka pengguna wajib dilengkapi validasi sisi klien untuk memastikan integritas tipe dan panjang karakter sebelum dikirim ke server. | Anggota 1 (Front-End Validation) |

---

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| ID | Parameter | Spesifikasi |
|---|---|---|
| **SRS-NF-01** | **Security (SQL Injection)** | Sistem 100% terlindung dari kerentanan SQL Injection melalui penerapan parameterized queries / prepared statements pada seluruh lapisan manipulasi data. |
| **SRS-NF-02** | **Security (Access Control)** | Otorisasi diverifikasi ganda: di level presentasi (Blade condition) dan di level kernel server (Route Middleware / Policy). Penolakan menghasilkan HTTP 403. |
| **SRS-NF-03** | **Data Integrity (Atomicity)** | Proses penghapusan memenuhi prinsip ACID (Atomicity, Consistency, Isolation, Durability) tanpa menyisakan data yatim (*no orphan records*). |
| **SRS-NF-04** | **Usability (UX Safeguard)** | Pencegahan salah klik aksi destruktif melalui modal konfirmasi berlapis dan pesan peringatan bahaya (*danger state*). |
| **SRS-NF-05** | **Performance** | Operasi atomik penghapusan list dengan $\le 50$ task diselesaikan dalam waktu kurang dari 1 detik pada beban server normal. |

---

## 5. Struktur Basis Data & Alur Transaksi

### 5.1 Skema Relasi Antar Entitas
```text
[users] 1 ────────────< N [lists]
   │                         │
   │ 1                       │ 1
   │                         │
   v N                       v N
[task_collaborators] >──── [tasks]
                             │ 1
                             │
                             v N
                      [progress_notes]
```

### 5.2 Algoritma Transaksi Penghapusan Atomik
```sql
START TRANSACTION;
BEGIN TRY:
    -- 1. Hapus catatan progres task terkait list ini
    DELETE FROM progress_notes WHERE task_id IN (SELECT id FROM tasks WHERE list_id = :list_id);
    
    -- 2. Hapus relasi kolaborator task terkait list ini
    DELETE FROM task_collaborators WHERE task_id IN (SELECT id FROM tasks WHERE list_id = :list_id);
    
    -- 3. Hapus seluruh task di dalam list ini
    DELETE FROM tasks WHERE list_id = :list_id;
    
    -- 4. Hapus entitas list utama (hanya jika pemilik sah)
    DELETE FROM lists WHERE id = :list_id AND owner_id = :auth_user_id;

    COMMIT;
END TRY
BEGIN CATCH:
    ROLLBACK;
    THROW EXCEPTION;
END CATCH;
```

---

## 6. Pembagian Kerja Tim (2 Orang Anggota)

Untuk menjamin efisiensi pengerjaan di mana **Front-End dikerjakan terlebih dahulu**, tanggung jawab dibagi ke dalam dua peran utama:

```
┌──────────────────────────────────────────────────────────┐
│                      PROJEK JARA                         │
├────────────────────────────┬─────────────────────────────┤
│   ANGGOTA 1 (FRONT-END)    │    ANGGOTA 2 (BACK-END)     │
│   UI/UX & Client Security  │  DB, Integrity & Server Sec │
├────────────────────────────┼─────────────────────────────┤
│ • UI List Sidebar & Owner  │ • Schema DB & Migration     │
│ • Modal Safeguard Kaskade  │ • Prepared Statements Layer │
│ • Role-based UI Guards     │ • Atomic Engine & Rollback  │
│ • Halaman Error 403 & Toast│ • Auth Middleware & 403 API │
└────────────────────────────┴─────────────────────────────┘
```

### 6.1 Matriks RACI (Responsible, Accountable, Consulted, Informed)
| Modul / Komponen Tugas | Anggota 1 (Front-End) | Anggota 2 (Back-End) |
|---|:---:|:---:|
| **SRS Bagian UI/UX & Fungsional Klien** | **R / A** | C / I |
| **SRS Bagian Database, Keamanan & Transaksi** | C / I | **R / A** |
| **Tampilan Sidebar List & Badge Kepemilikan (Owner vs Member)** | **R / A** | C |
| **Modal Konfirmasi Hapus List Kaskade (Safeguard Warning)** | **R / A** | C |
| **Halaman Error 403 Forbidden & Notifikasi UI Rollback** | **R / A** | I |
| **Skema Migrasi Database Relasional** | C | **R / A** |
| **Implementasi Prepared Statement pada Repository/Service** | I | **R / A** |
| **Mekanisme Transaksi Atomik (BeginTransaction, Commit, Rollback)** | C | **R / A** |
| **Middleware / Policy Verifikasi Otorisasi User (Tolak 403)** | C | **R / A** |
| **Integrasi Front-End & Back-End (Form Submit & HTTP Handling)** | **R** | **R** |

*Keterangan:*  
- **R (Responsible):** Pelaksana utama yang menulis dokumen / memprogram kode.  
- **A (Accountable):** Penanggung jawab utama keberhasilan fitur.  
- **C (Consulted):** Rekan diskusi / peninjau kode (*code reviewer*).  
- **I (Informed):** Mendapat laporan pembaruan setelah modul selesai.

### 6.2 Rincian Deliverable & Berkas yang Dikerjakan

#### **Tugas Anggota 1 (Lead Front-End & UI Security):**
1. **Dokumen SRS:** Menyusun Bab 2 (Karakteristik Pengguna), Bab 3.1 & 3.3 (Kebutuhan Fungsional UI), serta Desain Wireframe/Modal.
2. **Komponen Front-End:**
   - `resources/views/layouts/app.blade.php`: Memodifikasi navigasi sidebar list agar menampilkan badge kepemilikan (*Owner*) dan menyembunyikan tombol hapus jika bukan pemilik.
   - `resources/views/tasks/partials/modal-delete-list.blade.php`: Merancang modal konfirmasi kaskade interaktif yang mencantumkan bahaya terhapusnya seluruh task & anggota di dalamnya.
   - `resources/views/tasks/partials/modal-create-list.blade.php`: Memperbarui modal pembuatan list baru dengan penjelasan auto-ownership.
   - `resources/views/errors/403.blade.php`: Membuat halaman khusus *403 Forbidden* yang modern, responsif, dan informatif.
   - `resources/views/tasks/partials/alerts.blade.php`: Menangani pesan flash error saat transaksi dibatalkan (*rollback*).

#### **Tugas Anggota 2 (Lead Back-End & Data Security):**
1. **Dokumen SRS:** Menyusun Bab 3.2 & 3.4 (Kebutuhan Fungsional Backend & Prepared Statement), Bab 4 (Non-Fungsional), dan Bab 5 (Skema Basis Data & Transaksi).
2. **Komponen Back-End:**
   - `database/migrations/`: Menyiapkan skema tabel relasional (`lists`, `tasks`, `task_collaborators`, `progress_notes`).
   - `app/Http/Middleware/CheckListOwnership.php` atau `app/Policies/ListPolicy.php`: Mengamankan rute penghapusan dan pengubahan list agar melempar respon HTTP 403 jika user tidak berwenang.
   - `app/Services/ListService.php` / `app/Repositories/`: Mengimplementasikan parameterized queries menggunakan Prepared Statements murni.
   - `app/Http/Controllers/ListController.php`: Mengimplementasikan blok `DB::beginTransaction()`, kaskade delete, `DB::commit()`, dan `DB::rollBack()` di dalam blok `try-catch`.

---

## 7. Rencana Pengujian (Acceptance Criteria)

| Skenario Pengujian | Hasil yang Diharapkan | Penanggung Jawab Uji |
|---|---|---|
| User A membuat list baru "Sprint Final". | List terbuat dengan `owner_id = User A`. Di sidebar User A muncul badge "Pemilik". Di akun User B tidak ada tombol hapus untuk list tersebut. | Anggota 1 & Anggota 2 |
| User B mencoba menembak `DELETE /lists/{id_milik_User_A}` via Postman / URL inspect. | Server menolak request, mengembalikan HTTP 403 Forbidden, dan data list tetap utuh. | Anggota 2 |
| User A menghapus list "Sprint Final" yang memiliki 5 task, 3 kolaborator, dan 10 catatan. | Sistem menghapus seluruh 5 task, 3 kolaborator, 10 catatan, dan 1 list secara bersih tanpa sisa data. | Anggota 2 |
| Terjadi kegagalan buatan (*simulated error*) di langkah penghapusan task. | Sistem langsung mengeksekusi `rollBack()`. List, task, kolaborator, dan catatan tidak berkurang sedikitpun. UI menampilkan pesan error rollback. | Anggota 1 & Anggota 2 |
| Input nama list diisi string SQL Injection: `' OR '1'='1`. | Sistem memperlakukan input sebagai string teks biasa berkat prepared statement, tanpa eksekusi kode SQL ilegal. | Anggota 2 |
