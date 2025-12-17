<div class="ann-modal" id="modal-guru-status">
    <div class="ann-modal__overlay" data-close="guru-status"></div>
    <div class="ann-modal__card ann-modal__card--md ann-modal--guru-status">
        <div class="ann-modal__header">
            <div class="ann-modal__title">Ubah Status Guru</div>
            <button type="button" class="ann-icon-btn" data-close="guru-status">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="ann-modal__body">
            <div class="guru-status__card" id="guru-status-target">
                <div class="guru-status__avatar" id="guru-status-avatar">G</div>
                <div class="guru-status__name" id="guru-status-name"></div>
            </div>

            <div class="guru-status__section">
                <div class="ann-modal__label">Alasan dinon-aktifkan</div>
                <div class="ann-chip-group" id="status-reasons">
                    <button type="button" class="ann-chip" data-reason="Cuti">Cuti</button>
                    <button type="button" class="ann-chip" data-reason="Izin">Izin</button>
                    <button type="button" class="ann-chip" data-reason="Berhenti">Berhenti</button>
                </div>
            </div>

            <div class="guru-status__section">
                <div class="ann-modal__label row-between">
                    <span>Isi Pengumuman</span>
                    <span id="status-note-count">0/300</span>
                </div>
                <textarea id="status-note" class="ann-textarea ann-textarea--large" maxlength="300" rows="6"
                    placeholder="Tambahkan pengumuman atau catatan untuk guru ini"></textarea>
            </div>
        </div>
        <div class="ann-modal__footer ann-modal__footer--stacked">
            <button type="button" class="ann-btn ann-btn--primary ann-btn--block" id="btn-save-guru-status">Simpan</button>
        </div>
    </div>
</div>
