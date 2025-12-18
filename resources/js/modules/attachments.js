export function createAttachmentsManager(options) {
    const {
        attachmentsInput,
        attachmentsList,
        attachmentsTrigger,
        attachmentsTotal,
        attachmentsError,
        attachmentsErrorDefault,
        removedInputContainer,
        canUseDataTransfer,
        maxTotalBytes,
        onDirty = () => {},
        onSubmitStateChange = () => {},
    } = options;

    let attachmentFiles = [];
    let existingAttachments = [];
    let removedAttachments = [];

    function formatBytes(size = 0) {
        if (!size) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB'];
        const idx = Math.min(Math.floor(Math.log(size) / Math.log(1024)), units.length - 1);
        return `${(size / Math.pow(1024, idx)).toFixed(idx === 0 ? 0 : 1)} ${units[idx]}`;
    }

    function syncAttachmentInput() {
        if (!attachmentsInput) return;
        if (!canUseDataTransfer) return;
        const dt = new DataTransfer();
        attachmentFiles.forEach((f) => dt.items.add(f));
        attachmentsInput.files = dt.files;
    }

    function syncRemovedInputs() {
        if (!removedInputContainer) return;
        removedInputContainer.innerHTML = '';
        removedAttachments.forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_attachments[]';
            input.value = id;
            removedInputContainer.appendChild(input);
        });
    }

    function renderAttachments() {
        if (!attachmentsList) return;
        attachmentsList.innerHTML = '';
        let totalBytes = existingAttachments.reduce((acc, att) => acc + (Number(att.size) || 0), 0);
        totalBytes += attachmentFiles.reduce((acc, f) => acc + f.size, 0);
        if (attachmentsTotal) {
            const mb = totalBytes / (1024 * 1024);
            const displayMb = mb % 1 === 0 ? mb.toFixed(0) : mb.toFixed(1);
            attachmentsTotal.textContent = `${displayMb}/10MB`;
        }
        const overLimit = totalBytes > maxTotalBytes;
        if (attachmentsError) attachmentsError.style.display = overLimit ? 'block' : 'none';
        const attachWrap = attachmentsInput?.closest('.ann-attach');
        if (attachWrap) {
            attachWrap.classList.toggle('is-error', overLimit);
        }
        existingAttachments.forEach((att, idx) => {
            const iconName = att.icon || 'attach_file';
            const isPdf = iconName === 'picture_as_pdf';
            const isImage = iconName === 'image';
            const iconClass = isPdf ? 'pdf-icon' : isImage ? 'img-icon' : '';
            const chip = document.createElement('div');
            chip.className = `ann-chip ann-attach__item ${isPdf ? 'ann-chip--pdf' : ''}`;
            chip.innerHTML = `
                <span class="material-symbols-rounded ${iconClass}">${iconName}</span>
                <span class="ann-chip__text">${att.label || 'Lampiran'}</span>
                ${att.size ? `<span class="ann-attach__hint">${formatBytes(att.size)}</span>` : ''}
                <button type="button" class="ann-chip__remove" data-remove-existing="${idx}">
                    <span class="material-symbols-rounded">close</span>
                </button>
            `;
            if (att.url) {
                chip.style.cursor = 'pointer';
                chip.addEventListener('click', (e) => {
                    if (e.target.closest('.ann-chip__remove')) return;
                    window.open(att.url, '_blank');
                });
            }
            attachmentsList.appendChild(chip);
        });
        attachmentFiles.forEach((file, index) => {
            const lowerName = (file.name || '').toLowerCase();
            const mime = file.type || '';
            const isPdf = mime === 'application/pdf' || lowerName.endsWith('.pdf');
            const isImage = mime.startsWith('image/');
            const iconName = isPdf ? 'picture_as_pdf' : isImage ? 'image' : 'attach_file';
            const iconClass = isPdf ? 'pdf-icon' : isImage ? 'img-icon' : '';
            const chip = document.createElement('div');
            chip.className = 'ann-chip ann-attach__item';
            chip.innerHTML = `
                <span class="material-symbols-rounded ${iconClass}">${iconName}</span>
                <span class="ann-chip__text">${file.name}</span>
                <span class="ann-attach__hint">${formatBytes(file.size)}</span>
                <button type="button" class="ann-chip__remove" data-remove="${index}">
                    <span class="material-symbols-rounded">close</span>
                </button>
            `;
            attachmentsList.appendChild(chip);
        });
        attachmentsList.querySelectorAll('[data-remove]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.remove);
                attachmentFiles.splice(idx, 1);
                syncAttachmentInput();
                renderAttachments();
                onDirty();
                onSubmitStateChange();
            });
        });
        attachmentsList.querySelectorAll('[data-remove-existing]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.removeExisting);
                const removed = existingAttachments.splice(idx, 1)[0];
                if (removed?.id) removedAttachments.push(removed.id);
                syncRemovedInputs();
                renderAttachments();
                onDirty();
                onSubmitStateChange();
            });
        });
        syncAttachmentInput();
    }

    function resetAttachments() {
        attachmentFiles = [];
        if (attachmentsInput) attachmentsInput.value = '';
        renderAttachments();
    }

    function setExistingAttachments(list) {
        existingAttachments = Array.isArray(list) ? [...list] : [];
        removedAttachments = [];
        syncRemovedInputs();
        renderAttachments();
    }

    function attachEvents() {
        attachmentsTrigger?.addEventListener('click', () => attachmentsInput?.click());
        attachmentsInput?.addEventListener('change', (e) => {
            const files = Array.from(e.target.files || []);
            const existingSize = existingAttachments.reduce((acc, att) => acc + (Number(att.size) || 0), 0);
            let currentSize = existingSize + attachmentFiles.reduce((acc, f) => acc + f.size, 0);
            let skipped = false;
            files.forEach((file) => {
                if (currentSize + file.size <= maxTotalBytes) {
                    attachmentFiles.push(file);
                    currentSize += file.size;
                } else {
                    skipped = true;
                }
            });
            renderAttachments();
            onDirty();
            onSubmitStateChange();
            const attachWrap = attachmentsInput?.closest('.ann-attach');
            if (skipped) {
                if (attachmentsError) {
                    attachmentsError.textContent =
                        'File tambahan melewati batas total 10MB sehingga tidak diunggah.';
                    attachmentsError.style.display = 'block';
                }
                attachWrap?.classList.add('is-error');
            } else if (attachmentsError && currentSize <= maxTotalBytes) {
                attachmentsError.textContent = attachmentsErrorDefault;
                attachmentsError.style.display = 'none';
                attachWrap?.classList.remove('is-error');
            }
        });
    }

    attachEvents();

    return {
        renderAttachments,
        resetAttachments,
        syncRemovedInputs,
        setExistingAttachments,
        getAttachmentFiles: () => attachmentFiles,
        getExistingAttachments: () => existingAttachments,
        getRemovedAttachments: () => removedAttachments,
    };
}
