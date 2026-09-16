# List Lifecycle (SRS-F-17 & SRS-F-18) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengimplementasikan fitur pembuatan daftar tugas dengan kepemilikan otomatis (SRS-F-17) dan penghapusan daftar tugas kaskade atomik dengan mekanisme rollback penuh (SRS-F-18) berbasis MySQL, lengkap dengan alur percabangan Git branch per fitur untuk penilaian praktikum.

**Architecture:** Menggunakan Laravel DB Facade / Query Builder dengan kueri berparameter (*parameterized binding*) dan blok transaksi atomik eksplisit (`DB::beginTransaction()`, `commit()`, `rollBack()`). Antarmuka menggunakan Laravel Blade + Bootstrap 5.3 dengan badge penanda status kepemilikan (*Owner*) di sidebar, modal konfirmasi bahaya (*danger safeguard*), dan penanganan kondisi *empty state* saat 0 list.

**Tech Stack:** Laravel 12, PHP 8.2+, MySQL 8.0 (`jara_db`), Bootstrap 5.3, Bootstrap Icons, Git.

**Spec:** [SRS_Jara.md](file:///C:/Menza/Praktikum%20PPK/jara/SRS_Jara.md)

## Global Constraints

- Setiap fitur wajib dikerjakan pada Git branch terpisah:
  - Fitur SRS-F-17: `feature/SRS-F-17-auto-ownership`
  - Fitur SRS-F-18: `feature/SRS-F-18-atomic-cascade-deletion`
- Seluruh manipulasi data wajib terhubung langsung ke basis data MySQL `jara_db` (bukan mock session).
- Seluruh kueri wajib menggunakan *parameterized binding* (`?` atau `:param`).
- Transaksi penghapusan list wajib memenuhi prinsip ACID: jika salah satu langkah gagal, seluruh perubahan di-rollback tanpa meninggalkan data yatim (*no orphan records*).
- Penghapusan list hingga 0 diperbolehkan dengan menampilkan *Empty State* yang ramah pada antarmuka.

---

## Struktur Berkas yang Terlibat

```text
jara/
├── app/Http/Controllers/
│   ├── ListController.php                      # Controller utama manipulasi list (Create & Atomic Delete)
│   └── TaskController.php                      # Penyesuaian pembacaan list dari database MySQL
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php                       # Sidebar navigasi list, badge Owner, & modal trigger
│   └── tasks/
│       ├── index.blade.php                     # Penanganan empty state jika 0 list
│       └── partials/
│           ├── modal-create-list.blade.php     # Modal pembuatan list baru (SRS-F-17)
│           └── modal-delete-list.blade.php     # Modal safeguard konfirmasi hapus kaskade (SRS-F-18)
└── docs/superpowers/plans/
    └── 2026-09-16-list-lifecycle-srs-f17-f18.md
```

---

## Bagian 1: Implementasi Fitur SRS-F-17 (Auto-Ownership List)

### Task 1: Persiapan Branch Git & Sinkronisasi Pembacaan List dari MySQL

**Files:**
- Modify: `app/Http/Controllers/TaskController.php`
- Modify: `app/Http/Controllers/ListController.php`

**Interfaces:**
- Consumes: Tabel `lists` dan `users` di MySQL `jara_db`.
- Produces: Data list dengan atribut `owner_id`, `is_owner`, dan `name` dikirim ke view Blade.

- [ ] **Step 1: Buat dan checkout ke branch `feature/SRS-F-17-auto-ownership`**

Run:
```bash
git checkout -b feature/SRS-F-17-auto-ownership
```

- [ ] **Step 2: Sesuaikan pembacaan list pada `TaskController` dan `ListController` agar mengambil langsung dari tabel MySQL `lists`**

Ubah pengambilan data `$lists` agar mengambil dari database MySQL dan menandai apakah list tersebut milik user yang sedang aktif (`auth_user_id`):

```php
// Mengambil list dari database MySQL menggunakan query berparameter
$currentUser = DB::table('users')->where('id', session('jara_current_user_id', 1))->first();
$lists = DB::table('lists')
    ->select('lists.*', 'users.name as owner_name')
    ->leftJoin('users', 'lists.owner_id', '=', 'users.id')
    ->get()
    ->map(function ($list) use ($currentUser) {
        $list->is_owner = ($list->owner_id == $currentUser->id);
        return (array) $list;
    })
    ->keyBy('id')
    ->toArray();
```

- [ ] **Step 3: Implementasikan logika simpan list baru dengan kepemilikan otomatis (`owner_id = auth_id`) pada `ListController::store`**

Pada `app/Http/Controllers/ListController.php`:
```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
    ]);

    $currentUserId = session('jara_current_user_id', 1);

    // Kueri berparameter untuk menyimpan list baru dengan auto-ownership
    $listId = DB::table('lists')->insertGetId([
        'name' => trim($request->input('name')),
        'owner_id' => $currentUserId,
        'team_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('tasks.index', ['list_id' => $listId])
        ->with('success', "List '{$request->input('name')}' berhasil dibuat sebagai milik Anda!");
}
```

- [ ] **Step 4: Commit checkpoint fitur backend SRS-F-17**

```bash
git add app/Http/Controllers/ListController.php app/Http/Controllers/TaskController.php
git commit -m "feat(list): integrasikan pembacaan list dari MySQL dan simpan auto-ownership"
```

---

### Task 2: Antarmuka Form Modal Buat List & Visual Badge Owner di Sidebar

**Files:**
- Create: `resources/views/tasks/partials/modal-create-list.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

**Interfaces:**
- Consumes: `$lists` array dengan key `is_owner`.
- Produces: Sidebar dengan indikator badge `Owner` / `Shared` dan modal form penambahan list baru.

- [ ] **Step 1: Buat berkas komponen `resources/views/tasks/partials/modal-create-list.blade.php`**

Komponen modal form dengan input nama list dan keterangan kepemilikan otomatis:
```blade
<div class="modal fade" id="createListModal" tabindex="-1" aria-labelledby="createListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom pb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-folder-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="createListModalLabel">Buat List Baru</h5>
                        <small class="text-muted">Kelompokkan task dalam daftar baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lists.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="listName" class="form-label fw-semibold text-dark">Nama List <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg rounded-3 fs-6" id="listName" name="name" placeholder="Misal: Sprint 3 - Integrasi API" required maxlength="100" autofocus>
                        <div class="form-text mt-2 text-muted small">
                            <i class="bi bi-shield-check text-primary me-1"></i> Anda secara otomatis akan terdaftar sebagai <strong>Pemilik (Owner)</strong> atas list ini.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium">
                        <i class="bi bi-plus-circle me-1"></i> Buat List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Perbarui sidebar di `resources/views/layouts/app.blade.php` untuk menampilkan Badge Status Kepemilikan**

Tampilkan badge penanda kepemilikan pada baris list sidebar:
```blade
<!-- List Item dengan Badge Owner vs Member -->
<div class="d-flex align-items-center justify-content-between list-nav-item mb-1">
    <a class="nav-link-custom flex-grow-1 text-truncate {{ ($currentListId ?? 1) == $list['id'] ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $list['id']]) }}">
        <i class="bi bi-folder2{{ ($currentListId ?? 1) == $list['id'] ? '-open text-primary' : '' }}"></i>
        <span class="text-truncate">{{ $list['name'] }}</span>
    </a>
    
    <div class="d-flex align-items-center gap-1 ms-2">
        @if(!empty($list['is_owner']))
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1 py-0 rounded" style="font-size: 0.65rem;" title="Anda adalah Pemilik List ini">
                <i class="bi bi-star-fill text-warning me-1"></i>Owner
            </span>
        @else
            <span class="badge bg-light text-muted border px-1 py-0 rounded" style="font-size: 0.65rem;" title="List dibagikan kepada Anda">
                Member
            </span>
        @endif
    </div>
</div>
```
Dan pastikan `@include('tasks.partials.modal-create-list')` dipanggil di dalam layout sebelum tag `</body>`.

- [ ] **Step 3: Uji coba manual SRS-F-17 di browser & verifikasi basis data MySQL**

1. Buka `http://localhost:8000`.
2. Klik tombol **"+ Tambah"** pada bagian Daftar / List di sidebar.
3. Masukkan nama list: `Sprint 3 - Penilaian PPK`, klik **"Buat List"**.
4. Verifikasi bahwa list baru muncul di sidebar dengan badge **Owner**.
5. Verifikasi di MySQL: jalankan `SELECT id, name, owner_id FROM lists WHERE name = 'Sprint 3 - Penilaian PPK';` pastikan `owner_id` terisi ID user aktif.

- [ ] **Step 4: Commit dan push branch `feature/SRS-F-17-auto-ownership`**

```bash
git add resources/views/tasks/partials/modal-create-list.blade.php resources/views/layouts/app.blade.php
git commit -m "feat(ui): tambahkan modal buat list dan visual badge owner di sidebar (SRS-F-17)"
git push origin feature/SRS-F-17-auto-ownership
```

- [ ] **Step 5: Merge branch `feature/SRS-F-17-auto-ownership` ke branch `main`**

```bash
git checkout main
git merge feature/SRS-F-17-auto-ownership --no-ff -m "merge: gabungkan fitur SRS-F-17 auto-ownership ke main"
git push origin main
```

---

## Bagian 2: Implementasi Fitur SRS-F-18 (Penghapusan Kaskade Atomik & Rollback)

### Task 3: Pembuatan Antarmuka Modal Safeguard Bahaya Hapus List & Empty State

**Files:**
- Create: `resources/views/tasks/partials/modal-delete-list.blade.php`
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/tasks/index.blade.php`

**Interfaces:**
- Consumes: `$currentListId`, `$lists`, dan jumlah task/kolaborator pada list aktif.
- Produces: Modal konfirmasi merah peringatan bahaya kaskade dan tampilan *empty state* saat 0 list.

- [ ] **Step 1: Buat dan checkout ke branch `feature/SRS-F-18-atomic-cascade-deletion`**

Run:
```bash
git checkout -b feature/SRS-F-18-atomic-cascade-deletion
```

- [ ] **Step 2: Buat komponen modal konfirmasi kaskade `resources/views/tasks/partials/modal-delete-list.blade.php`**

Komponen ini menghitung dan menampilkan secara eksplisit jumlah data yang akan terhapus:
```blade
<div class="modal fade" id="deleteListModal" tabindex="-1" aria-labelledby="deleteListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger-subtle border-bottom border-danger-subtle py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-danger mb-0" id="deleteListModalLabel">Hapus List Secara Kaskade?</h5>
                        <small class="text-danger-emphasis">Operasi atomik bersyarat ini tidak dapat dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="deleteListForm" action="" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="modal-body p-4">
                    <p class="text-dark mb-3">
                        Anda akan menghapus list <strong id="deleteTargetListName" class="text-danger"></strong> beserta seluruh isi di dalamnya:
                    </p>
                    
                    <div class="alert alert-danger border-danger-subtle rounded-3 p-3 mb-3">
                        <h6 class="fw-bold text-danger mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-x"></i> Dampak Penghapusan Kaskade:
                        </h6>
                        <ul class="mb-0 text-dark small ps-3">
                            <li>Seluruh <strong>Task</strong> di dalam list ini akan dihapus permanen.</li>
                            <li>Seluruh <strong>Akses Kolaborator</strong> pada task terkait akan dicabut.</li>
                            <li>Seluruh <strong>Catatan Progres & Linimasa Aktivitas</strong> akan dibersihkan.</li>
                        </ul>
                    </div>

                    <div class="form-check bg-light p-3 rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="confirmCheckbox" required>
                        <label class="form-check-label text-muted small fw-medium" for="confirmCheckbox">
                            Saya memahami bahwa proses ini bersifat atomik dan seluruh data terkait akan dihapus permanen.
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer border-top pt-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold" id="confirmDeleteBtn" disabled>
                        <i class="bi bi-trash-fill me-1"></i> Ya, Hapus Permanen Beserta Isinya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmCheckbox && confirmDeleteBtn) {
        confirmCheckbox.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });
    }
});

function openDeleteListModal(listId, listName) {
    const form = document.getElementById('deleteListForm');
    const targetNameSpan = document.getElementById('deleteTargetListName');
    const checkbox = document.getElementById('confirmCheckbox');
    const btn = document.getElementById('confirmDeleteBtn');
    
    form.action = "{{ url('lists') }}/" + listId;
    targetNameSpan.textContent = '"' + listName + '"';
    checkbox.checked = false;
    btn.disabled = true;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteListModal'));
    modal.show();
}
</script>
```

- [ ] **Step 3: Tambahkan tombol trigger Hapus List pada Sidebar / Header List (`app.blade.php`)**

Hubungkan tombol hapus list dengan modal safeguard kaskade di atas:
```blade
<button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none opacity-50 hover-opacity-100" 
        onclick="openDeleteListModal({{ $list['id'] }}, '{{ addslashes($list['name']) }}')" 
        title="Hapus List Beserta Isinya">
    <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
</button>
```

- [ ] **Step 4: Buat Empty State di `resources/views/tasks/index.blade.php` untuk kondisi 0 List**

Tambahkan blok kondisional jika tidak ada list yang tersisa:
```blade
@if(empty($lists) || count($lists) === 0)
    <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4">
        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
            <i class="bi bi-folder-x fs-2"></i>
        </div>
        <h4 class="fw-bold text-dark mb-1">Belum Ada List Tugas</h4>
        <p class="text-muted mx-auto" style="max-width: 450px;">
            Seluruh list telah dihapus. Buat list tugas pertama Anda untuk mulai mengelola task dan berkolaborasi bersama tim.
        </p>
        <div>
            <button class="btn btn-primary px-4 py-2 rounded-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createListModal">
                <i class="bi bi-plus-circle me-1"></i> Buat List Baru
            </button>
        </div>
    </div>
@else
    <!-- Konten task normal -->
@endif
```

- [ ] **Step 5: Commit checkpoint UI modal safeguard**

```bash
git add resources/views/tasks/partials/modal-delete-list.blade.php resources/views/layouts/app.blade.php resources/views/tasks/index.blade.php
git commit -m "feat(ui): tambahkan modal safeguard bahaya hapus kaskade dan tampilan empty state (SRS-F-18)"
```

---

### Task 4: Implementasi Transaksi Atomik Kaskade & Rollback di Backend (`ListController`)

**Files:**
- Modify: `app/Http/Controllers/ListController.php`

**Interfaces:**
- Consumes: HTTP DELETE `/lists/{id}`, session `jara_current_user_id`.
- Produces: Transaksi atomik penghapusan (`task_activities` $\to$ `task_collaborators` $\to$ `tasks` $\to$ `lists`), redirect dengan flash alert success/error.

- [ ] **Step 1: Implementasikan transaksi atomik kaskade di `ListController::destroy` menggunakan `DB::beginTransaction()`**

Buka `app/Http/Controllers/ListController.php` dan implementasikan logika atomik:
```php
public function destroy(Request $request, int $id)
{
    $currentUserId = session('jara_current_user_id', 1);

    // 1. Cek keberadaan list menggunakan kueri berparameter
    $list = DB::table('lists')->where('id', $id)->first();
    if (!$list) {
        return redirect()->route('tasks.index')->with('error', 'List tidak ditemukan.');
    }

    // 2. Eksekusi transaksi atomik
    DB::beginTransaction();
    try {
        // Ambil seluruh ID task yang ada di dalam list ini
        $taskIds = DB::table('tasks')->where('list_id', $id)->pluck('id')->toArray();

        if (!empty($taskIds)) {
            // A. Hapus seluruh catatan aktivitas / progres task terkait
            DB::table('task_activities')->whereIn('task_id', $taskIds)->delete();

            // B. Hapus seluruh relasi kolaborator pada task terkait
            DB::table('task_collaborators')->whereIn('task_id', $taskIds)->delete();

            // C. Hapus notifikasi yang terhubung ke task terkait
            DB::table('notifications')->whereIn('task_id', $taskIds)->delete();

            // D. Hapus seluruh task di dalam list
            DB::table('tasks')->where('list_id', $id)->delete();
        }

        // Simulasi error rollback jika parameter query ?simulate_fail=1 disertakan (untuk demonstrasi praktikum)
        if ($request->query('simulate_fail') == '1') {
            throw new \Exception('Simulasi kegagalan server: Transaksi penghapusan list dibatalkan (Rollback).');
        }

        // E. Hapus entitas list utama
        DB::table('lists')->where('id', $id)->delete();

        // Seluruh langkah sukses -> simpan permanen
        DB::commit();

        // Cari ID list pertama yang tersisa untuk redirect
        $nextList = DB::table('lists')->first();
        $redirectParams = $nextList ? ['list_id' => $nextList->id] : [];

        return redirect()->route('tasks.index', $redirectParams)
            ->with('success', "List '{$list->name}' beserta seluruh task dan relasinya berhasil dihapus secara atomik!");

    } catch (\Throwable $e) {
        // Terjadi kegagalan di salah satu tahap -> BATALKAN SEMUANYA
        DB::rollBack();

        return redirect()->back()->with('error', 'Penghapusan GAGAL dan di-ROLLBACK: ' . $e->getMessage());
    }
}
```

- [ ] **Step 2: Uji coba manual pembuktian Atomisitas & Rollback**

1. **Uji Kasus Berhasil (Atomic Commit):**
   - Buka list yang memiliki task dan kolaborator.
   - Klik tombol hapus list, centang konfirmasi pada modal safeguard, lalu klik **"Ya, Hapus Permanen"**.
   - Periksa database MySQL: pastikan list, task di dalamnya, data kolaborator, dan aktivitasnya terhapus bersih tanpa sisa.
2. **Uji Kasus Gagal (Atomic Rollback):**
   - Panggil penghapusan dengan menambahkan simulasi kegagalan: kirimkan request DELETE dengan parameter `?simulate_fail=1`.
   - Verifikasi bahwa sistem melempar exception, memicu `DB::rollBack()`, dan menampilkan flash banner merah *"Penghapusan GAGAL dan di-ROLLBACK"*.
   - Periksa database MySQL: pastikan tidak ada satupun task, kolaborator, atau list yang berkurang (data tetap 100% utuh).

- [ ] **Step 3: Commit dan push branch `feature/SRS-F-18-atomic-cascade-deletion`**

```bash
git add app/Http/Controllers/ListController.php
git commit -m "feat(list): implementasikan penghapusan kaskade atomik dan rollback transaksi (SRS-F-18)"
git push origin feature/SRS-F-18-atomic-cascade-deletion
```

- [ ] **Step 4: Merge branch `feature/SRS-F-18-atomic-cascade-deletion` ke branch `main`**

```bash
git checkout main
git merge feature/SRS-F-18-atomic-cascade-deletion --no-ff -m "merge: gabungkan fitur SRS-F-18 atomic cascade deletion ke main"
git push origin main
```

---

## Verifikasi Akhir Sebelum Penyelesaian

1. [ ] Jalankan `git log --graph --oneline` untuk memverifikasi riwayat commit dan merge branch `feature/SRS-F-17-auto-ownership` dan `feature/SRS-F-18-atomic-cascade-deletion`.
2. [ ] Buka aplikasi di browser `http://localhost:8000` dan pastikan seluruh fitur berjalan tanpa error.
3. [ ] Pastikan basis data MySQL `jara_db` berada dalam status konsisten.
