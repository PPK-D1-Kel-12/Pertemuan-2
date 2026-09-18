<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="createTaskModalLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-1"></i> Buat Task Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Judul Task -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul Task <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Contoh: Implementasi Form Tambah Kolaborator" required minlength="1" maxlength="255" autofocus>
                        <small class="text-muted" style="font-size: 0.7rem;">Maksimal 255 karakter.</small>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" maxlength="1000" placeholder="Jelaskan detail pekerjaan atau spesifikasi teknis (maks. 1000 karakter)..."></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <!-- List Tujuan (SRS-F-03) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">List / Kategori <span class="text-danger">*</span></label>
                            <select name="list_id" class="form-select form-select-sm" required>
                                @foreach($lists ?? [] as $list)
                                    <option value="{{ $list['id'] }}" {{ ($currentListId ?? 1) == $list['id'] ? 'selected' : '' }}>
                                        {{ $list['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prioritas (SRS-F-05) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select form-select-sm" required>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Sedang" selected>Sedang</option>
                                <option value="Rendah">Rendah</option>
                            </select>
                        </div>
                    </div>

                    <!-- Deadline (SRS-F-06) -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deadline (Batas Waktu) <span class="text-danger">*</span></label>
                        <input type="date" name="deadline" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+3 days')) }}" min="2020-01-01" max="2099-12-31" required>
                    </div>

                    <div class="p-2 bg-light rounded-2 border small text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle text-primary me-1"></i> Anda akan menjadi <strong>Pemilik Task (Owner)</strong> dan dapat menambahkan kolaborator setelah task dibuat.
                    </div>
                </div>

                <div class="modal-footer border-top py-2 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-check2 me-1"></i> Simpan Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

