<div class="modal fade" id="deleteListModal" tabindex="-1" aria-labelledby="deleteListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger-subtle border-bottom border-danger-subtle py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
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
                            <li class="mb-1">Seluruh <strong>Task / Tugas</strong> di dalam list ini akan dihapus permanen.</li>
                            <li class="mb-1">Seluruh <strong>Akses Kolaborator</strong> pada task terkait akan dicabut.</li>
                            <li>Seluruh <strong>Catatan Progres & Linimasa Aktivitas</strong> akan dibersihkan.</li>
                        </ul>
                    </div>

                    <div class="form-check bg-light p-3 rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="confirmCheckbox" required>
                        <label class="form-check-label text-muted small fw-medium" for="confirmCheckbox">
                            Saya memahami bahwa proses ini bersifat atomik dan seluruh data terkait akan dihapus permanen (SRS-F-18).
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
    
    if (form && targetNameSpan && checkbox && btn) {
        form.action = "{{ url('lists') }}/" + listId;
        targetNameSpan.textContent = '"' + listName + '"';
        checkbox.checked = false;
        btn.disabled = true;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteListModal'));
        modal.show();
    }
}
</script>
