export function initGuruStatus() {
    const statusModal = document.getElementById('modal-guru-status');
    const statusName = document.getElementById('guru-status-name');
    const statusAvatar = document.getElementById('guru-status-avatar');
    const reasonChips = document.querySelectorAll('#status-reasons .ann-chip');
    const noteInput = document.getElementById('status-note');
    const noteCount = document.getElementById('status-note-count');
    const btnSaveStatus = document.getElementById('btn-save-guru-status');
    const guruDetailModal = document.getElementById('modal-guru-detail');
    const guruDetailFields = {
        name: document.getElementById('guru-detail-name'),
        id: document.getElementById('guru-detail-id'),
        kode: document.getElementById('guru-detail-kode'),
        gender: document.getElementById('guru-detail-gender'),
        email: document.getElementById('guru-detail-email'),
        status: document.getElementById('guru-detail-status'),
    };
    let selectedReason = null;
    let currentGuru = null;

    function updateSaveStatusState() {
        const enabled = !!selectedReason;
        if (btnSaveStatus) {
            btnSaveStatus.disabled = !enabled;
        }
    }

    function openStatusModal(guruId, guruName, dropdownEl) {
        currentGuru = { guruId, guruName, dropdownEl };
        statusName.textContent = guruName;
        const initial = (guruName || 'G')[0] || 'G';
        statusAvatar.textContent = initial.toUpperCase();
        selectedReason = null;
        noteInput.value = '';
        noteCount.textContent = '0/2000';
        reasonChips.forEach((chip) => chip.classList.remove('active'));
        updateSaveStatusState();
        statusModal?.classList.add('show');
    }

    function closeStatusModal() {
        statusModal?.classList.remove('show');
    }

    function updateStatusPill(drop, statusLabel) {
        const btn = drop?.querySelector('.ann-status');
        if (!btn) return;
        const labelEl = btn.querySelector('.ann-status__label');
        if (labelEl) labelEl.textContent = statusLabel;
        btn.classList.toggle('ann-status--active', statusLabel === 'Aktif');
        btn.classList.toggle('ann-status--inactive', statusLabel === 'Nonaktif');
        drop.dataset.currentStatus = statusLabel;
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `ann-toast ann-toast--${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    function closeAllStatusMenus() {
        document.querySelectorAll('[data-status-dropdown].open').forEach((el) => el.classList.remove('open'));
    }

    document.querySelectorAll('[data-status-dropdown]').forEach((drop) => {
        const trigger = drop.querySelector('[data-status-trigger]');
        const menu = drop.querySelector('.ann-status-menu');

        trigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = drop.classList.contains('open');
            closeAllStatusMenus();
            if (!isOpen) drop.classList.add('open');
        });

        menu?.querySelectorAll('[data-status-option]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const chosen = opt.dataset.statusOption;
                closeAllStatusMenus();
                if (chosen === 'Nonaktif') {
                    openStatusModal(drop.dataset.guruId || '', drop.dataset.guruName || '', drop);
                } else {
                    updateStatusPill(drop, 'Aktif');
                    showToast('Status guru diubah ke Aktif', 'success');
                }
            });
        });
    });

    reasonChips.forEach((chip) =>
        chip.addEventListener('click', () => {
            selectedReason = chip.dataset.reason;
            reasonChips.forEach((c) => c.classList.toggle('active', c === chip));
            updateSaveStatusState();
        }),
    );

    noteInput?.addEventListener('input', () => {
        const maxLen = 300;
        if (noteInput.value.length > maxLen) {
            noteInput.value = noteInput.value.slice(0, maxLen);
        }
        noteCount.textContent = `${noteInput.value.length}/${maxLen}`;
    });

    function openGuruDetail(row) {
        if (!row || !guruDetailModal) return;
        const get = (attr) => row.dataset[attr] || '-';
        guruDetailFields.name.textContent = get('guruName');
        guruDetailFields.id.textContent = get('guruId');
        guruDetailFields.kode.textContent = get('guruKode') || get('guruNip') || '-';
        guruDetailFields.gender.textContent = get('guruGender') || '-';
        guruDetailFields.email.textContent = get('guruEmail') || '-';
        guruDetailFields.status.textContent = get('guruStatus') || '-';
        guruDetailModal.classList.add('show');
    }

    document.querySelectorAll('.ann-table--guru .ann-row--clickable').forEach((row) => {
        row.addEventListener('click', (e) => {
            if (e.target.closest('.ann-status-dropdown')) return;
            openGuruDetail(row);
        });
    });

    document.querySelectorAll('[data-close="guru-detail"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            guruDetailModal?.classList.remove('show');
        });
    });

    document.querySelectorAll('[data-close="guru-status"]').forEach((btn) => {
        btn.addEventListener('click', closeStatusModal);
    });

    document.addEventListener('click', () => closeAllStatusMenus());

    btnSaveStatus?.addEventListener('click', () => {
        if (!selectedReason || !currentGuru) return;
        if (currentGuru.dropdownEl) {
            updateStatusPill(currentGuru.dropdownEl, 'Nonaktif');
        }
        showToast(`Status ${currentGuru.guruName || ''} diubah ke Nonaktif (${selectedReason})`, 'success');
        closeStatusModal();
    });
}
