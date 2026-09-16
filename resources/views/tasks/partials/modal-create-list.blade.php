<div class="modal fade" id="createListModal" tabindex="-1" aria-labelledby="createListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
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
                        <div class="form-text mt-2 text-muted small d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-primary"></i> 
                            <span>Anda secara otomatis akan terdaftar sebagai <strong>Pemilik (Owner)</strong> atas list ini.</span>
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
