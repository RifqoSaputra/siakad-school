import { createAttachmentsManager } from './attachments';
import { createDatetimePicker } from './datetimePicker';
import { createFormState } from './formState';

export function initAnnouncementForm() {
    const detailModal = document.getElementById('modal-detail');
    const formModal = document.getElementById('modal-form');
    const form = document.getElementById('announcement-form');
    const actionStatus = document.getElementById('action_status');
    const scheduledInput = document.getElementById('scheduled_input');
    const scheduledHidden = document.getElementById('scheduled_input') || document.getElementById('scheduled_for');
    const titleInput = document.getElementById('title-input');
    const bodyInput = document.getElementById('body-input');
    const titleCount = document.getElementById('title-count');
    const bodyCount = document.getElementById('body-count');
    const detailTitle = document.getElementById('detail-title');
    const detailMeta = document.getElementById('detail-meta');
    const detailBody = document.getElementById('detail-body');
    const detailAttachments = document.getElementById('detail-attachments');
    const btnSend = document.getElementById('btn-send');
    const btnDraft = document.getElementById('btn-save-draft');
    const dtpDisplay = document.getElementById('scheduled_display');
    const dtpDisplayText = document.getElementById('scheduled_display_text');
    const dtpPopover = document.getElementById('scheduled_popover');
    const dtpMonthLabel = document.getElementById('scheduled_month_label');
    const dtpDays = document.getElementById('scheduled_days');
    const dtpTimeInput = document.getElementById('scheduled_time_input');
    const dtError = document.getElementById('datetime-error');
    const btnDelete = document.getElementById('btn-delete');
    const deleteForm = document.getElementById('delete-announcement-form');
    const deleteModal = document.getElementById('modal-delete');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');
    const sendModeRadios = document.querySelectorAll('input[name="send_mode"]');
    const recipientRadios = document.querySelectorAll('input[name="target_role"]');
    const scheduleSection = document.getElementById('schedule_section');
    const attachmentsInput = document.getElementById('attachments_input');
    const attachmentsList = document.getElementById('attachments_list');
    const attachmentsTrigger = document.getElementById('attachments_trigger');
    const attachmentsTotal = document.getElementById('attachments_total');
    const attachmentsError = document.getElementById('attachments-error');
    const attachmentsErrorDefault = attachmentsError?.textContent?.trim() || 'Melewati batas ukuran lampiran, silahkan sesuaikan lampiran yang ingin diunggah.';
    const canUseDataTransfer = typeof DataTransfer !== 'undefined';
    const removedInputContainer = document.getElementById('removed_attachments');
    const MAX_ATTACH_TOTAL = 10 * 1024 * 1024;
    let hasDirtyForm = false;
    let currentEditId = null;
    const DATE_ERROR_TEXT = 'Tanggal telah berlalu. Silahkan pilih jadwal lain.';
    const TIME_ERROR_TEXT = 'Jam telah berlalu. Silahkan pilih jadwal lain.';
    const INVALID_SCHEDULE_TEXT = 'Jadwal tidak valid. Silahkan pilih jadwal lain.';
    const TITLE_MAX = 200;
    const BODY_MAX = 2000;
    const titleError = document.getElementById('title-error');
    const bodyError = document.getElementById('body-error');

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('show');
    }
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
    }
    function getSendMode() {
        const checked = Array.from(sendModeRadios).find(r => r.checked);
        return checked ? checked.value : 'now';
    }

    const attachmentsManager = createAttachmentsManager({
        attachmentsInput,
        attachmentsList,
        attachmentsTrigger,
        attachmentsTotal,
        attachmentsError,
        attachmentsErrorDefault,
        removedInputContainer,
        canUseDataTransfer,
        maxTotalBytes: MAX_ATTACH_TOTAL,
        onDirty: markDirty,
        onSubmitStateChange: updateSubmitState,
    });

    function syncSendModeRadios() {
        sendModeRadios.forEach(r => {
            const dot = r.nextElementSibling;
            if (dot?.classList.contains('ann-radio') || dot?.classList.contains('radio')) {
                dot.classList.toggle('active', r.checked);
            }
        });
    }

    function applySendMode(mode) {
        const showSchedule = mode === 'schedule';
        if (scheduleSection) scheduleSection.style.display = showSchedule ? 'grid' : 'none';
        if (!showSchedule) {
            if (scheduledInput) scheduledInput.value = '';
            if (scheduledHidden) scheduledHidden.value = '';
            dtpDisplay?.classList.remove('is-error');
            dtpTimeInput?.closest('.ann-datetime__display')?.classList.remove('is-error');
            if (dtError) dtError.style.display = 'none';
        }
        syncSendModeRadios();
        if (btnSend) btnSend.textContent = mode === 'schedule' ? 'Jadwalkan' : 'Umumkan';
        updateSubmitState();
    }

    function syncRecipientRadios() {
        recipientRadios.forEach(r => {
            const dot = r.nextElementSibling;
            const option = r.closest('.recipient__option, .ann-recipient__option');
            if (dot?.classList.contains('ann-radio') || dot?.classList.contains('radio')) {
                dot.classList.toggle('active', r.checked);
            }
            option?.classList.toggle('is-active', r.checked);
        });
    }

    function setRecipientValue(val = 'all') {
        recipientRadios.forEach(r => {
            r.checked = r.value === val;
        });
        syncRecipientRadios();
    }

    const confirmModal = document.getElementById('modal-confirm-close');
    const btnConfirmDraft = document.getElementById('btn-confirm-draft');
    const btnDiscard = document.getElementById('btn-discard');

    function shouldConfirmClose() {
        return hasDirtyForm && isFormFilled();
    }

    function attemptCloseForm() {
        if (shouldConfirmClose()) {
            closeModal(formModal);
            openModal(confirmModal);
        } else {
            closeModal(formModal);
            hasDirtyForm = false;
            form?.reset();
            updateCounters();
            updateSubmitState();
        }
    }

    document.querySelectorAll('[data-close="detail"]').forEach(btn => btn.addEventListener('click', () => closeModal(detailModal)));
    document.querySelectorAll('[data-close="form"]').forEach(btn => btn.addEventListener('click', () => attemptCloseForm()));

    document.querySelectorAll('.ann-modal__card').forEach(card => {
        const footer = card.querySelector('.ann-modal__footer');
        if (!footer) return;
        const updateFooterBorder = () => {
            const atBottom = Math.ceil(card.scrollTop + card.clientHeight) >= card.scrollHeight - 1;
            footer.classList.toggle('is-at-bottom', atBottom);
        };
        card.addEventListener('scroll', updateFooterBorder);
        updateFooterBorder();
    });

    detailModal?.addEventListener('click', (e) => { if (e.target.classList.contains('ann-modal__overlay')) closeModal(detailModal); });
    formModal?.addEventListener('click', (e) => { if (e.target.classList.contains('ann-modal__overlay')) attemptCloseForm(); });
    confirmModal?.addEventListener('click', (e) => { if (e.target.classList.contains('ann-modal__overlay')) closeModal(confirmModal); });
    deleteModal?.addEventListener('click', (e) => { if (e.target.classList.contains('ann-modal__overlay')) closeModal(deleteModal); });

    sendModeRadios.forEach(r => r.addEventListener('change', () => applySendMode(getSendMode())));
    recipientRadios.forEach(r => r.addEventListener('change', syncRecipientRadios));

    const renderAttachments = () => attachmentsManager.renderAttachments();
    const resetAttachments = () => attachmentsManager.resetAttachments();
    const syncRemovedInputs = () => attachmentsManager.syncRemovedInputs();
    const getAttachmentCount = () => attachmentsManager.getAttachmentFiles().length;
    const getRemovedCount = () => attachmentsManager.getRemovedAttachments().length;

    const datePicker = createDatetimePicker({
        scheduledInput,
        scheduledHidden,
        dtpDisplay,
        dtpDisplayText,
        dtpPopover,
        dtpMonthLabel,
        dtpDays,
        dtpTimeInput,
        dtError,
        getSendMode,
        onDirty: markDirty,
        onSubmitStateChange: updateSubmitState,
        DATE_ERROR_TEXT,
        TIME_ERROR_TEXT,
        INVALID_SCHEDULE_TEXT,
    });

    const {
        updateDatetimeValue,
        primeDateTime,
        renderCalendar,
        openPicker,
        closePicker,
        isScheduleMode,
        hasDateTimeError,
        isDateTimePast,
        setSelected,
        resetToNow,
        toLocalInputValue,
    } = datePicker;

    const formState = createFormState({
        titleInput,
        bodyInput,
        titleCount,
        bodyCount,
        TITLE_MAX,
        BODY_MAX,
        titleError,
        bodyError,
        getAttachmentCount,
        getRemovedCount,
        getSendMode,
        getRecipientValue,
        scheduledInput,
        scheduledHidden,
        dtpTimeInput,
    });

    const {
        snapshotForm,
        setInitialFormState,
        hasFormChanged,
        updateFieldErrors,
        updateCounters,
        isOverLimit,
    } = formState;

    const statusClassMap = {
        sent: 'ann-status--sent',
        scheduled: 'ann-status--scheduled',
        draft: 'ann-status--draft',
        published: 'ann-status--sent',
    };

    function renderDetailMeta(data) {
        if (!detailMeta) return;
        detailMeta.innerHTML = '';

        const items = [
            { type: 'text', content: data.meta_date || '-' },
            {
                type: 'recipient',
                content: data.target_label || '-',
            },
            {
                type: 'status',
                content: data.status_label || '-',
                status: data.status,
            },
        ];

        items.forEach((item, idx) => {
            if (idx > 0) {
                const dot = document.createElement('span');
                dot.className = 'ann-meta-dot';
                detailMeta.appendChild(dot);
            }

            if (item.type === 'text') {
                const span = document.createElement('span');
                span.textContent = item.content;
                detailMeta.appendChild(span);
            } else if (item.type === 'recipient') {
                const wrap = document.createElement('span');
                wrap.className = 'ann-meta-recipient';
                wrap.innerHTML = `Diumumkan ke <strong>${item.content}</strong>`;
                detailMeta.appendChild(wrap);
            } else if (item.type === 'status') {
                const badge = document.createElement('span');
                badge.className = `ann-status ${statusClassMap[item.status] || ''}`;
                badge.innerHTML = `<span class="material-symbols-rounded">${data.status_icon || 'info'}</span>${item.content}`;
                detailMeta.appendChild(badge);
            }
        });
    }

    document.querySelectorAll('.ann-row').forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.closest('.ann-edit-btn')) return;
            const data = JSON.parse(row.dataset.detail);
            detailTitle.textContent = data.judul || '';
            renderDetailMeta(data);
            detailBody.innerHTML = data.isi_pengumuman || '';
            detailAttachments.innerHTML = '';
            (data.attachments || []).forEach(att => {
                const chip = document.createElement('span');
                const iconName = att.icon || 'attach_file';
                const isPdf = iconName === 'picture_as_pdf';
                const isImage = iconName === 'image';
                chip.className = `ann-chip ${isPdf ? 'ann-chip--pdf' : ''}`;
                const iconClass = isPdf ? 'pdf-icon' : (isImage ? 'img-icon' : '');
                chip.innerHTML = `<span class="material-symbols-rounded ${iconClass}">${iconName}</span><span class="ann-chip__text">${att.label || att.name}</span>`;
                if (att.url) {
                    chip.style.cursor = 'pointer';
                    chip.addEventListener('click', () => window.open(att.url, '_blank'));
                }
                detailAttachments.appendChild(chip);
            });
            openModal(detailModal);
        });
    });

    function updateSubmitState() {
        const hasTitle = !!titleInput?.value.trim();
        const hasBody = !!bodyInput?.value.trim();
        const scheduleError = isScheduleMode() ? hasDateTimeError() : false;
        const overLimit = isOverLimit();
        const shouldDisable = !(hasTitle && hasBody) || scheduleError || overLimit;
        if (btnSend) btnSend.disabled = shouldDisable;
        if (btnDraft) {
            const changed = hasFormChanged();
            btnDraft.disabled = !changed || scheduleError || overLimit;
        }
    }

    function getRecipientValue() {
        const checked = Array.from(recipientRadios).find(r => r.checked);
        return checked ? checked.value : '';
    }

    function markDirty() { hasDirtyForm = true; }

    function handleFormInputChange() {
        updateCounters();
        updateSubmitState();
        markDirty();
    }

    function isFormFilled() {
        const hasTitle = !!titleInput?.value.trim();
        const hasBody = !!bodyInput?.value.trim();
        const hasDate = !!dtpDisplayText?.value.trim();
        const hasTime = !!dtpTimeInput?.value.trim();
        const hasAttach = getAttachmentCount() > 0;
        return hasTitle || hasBody || hasDate || hasTime || hasAttach;
    }


    const btnCreate = document.getElementById('btn-open-create');
    btnCreate?.addEventListener('click', () => {
        form.reset();
        form.action = form.dataset.store ?? form.action;
        form.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('form-title').textContent = 'Buat Pengumuman';
        currentEditId = null;
        if (btnDelete) btnDelete.style.display = 'none';
        if (deleteForm) deleteForm.action = '';
        actionStatus.value = 'sent';
        resetToNow();
        primeDateTime();
        updateCounters();
        updateSubmitState();
        sendModeRadios.forEach(r => { r.checked = r.value === 'now'; });
        applySendMode('now');
        setRecipientValue('all');
        attachmentsManager.setExistingAttachments([]);
        resetAttachments();
        setInitialFormState(snapshotForm());
        updateSubmitState();
        openModal(formModal);
        hasDirtyForm = false;
    });

    document.querySelectorAll('.ann-edit-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const row = btn.closest('.ann-row');
            const data = JSON.parse(row.dataset.detail);
            currentEditId = data.id;
            form.action = form.dataset.updateTemplate?.replace('__ID__', data.id) || form.action.replace(/\/pengumuman\/\d+$/, `/pengumuman/${data.id}`);
            form.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('form-title').textContent = 'Buat Pengumuman';
            titleInput.value = data.judul || '';
            bodyInput.value = (data.isi_pengumuman || '').replace(/<br\s*\/?>/gi, '\n');
            const normalized = (data.target_label || '').toLowerCase();
            const targetVal = normalized === 'guru' ? 'guru' : (normalized === 'ortu' ? 'ortu' : 'all');
            setRecipientValue(targetVal);
            const scheduleVal = data.scheduled_for_iso || data.sent_at_iso;
            primeDateTime(scheduleVal);
            if (scheduleVal) {
                setSelected(scheduleVal);
            } else {
                resetToNow();
            }
            const mode = scheduleVal ? 'schedule' : 'now';
            sendModeRadios.forEach(r => { r.checked = r.value === mode; });
            applySendMode(mode);
            updateCounters();
            updateSubmitState();
            attachmentsManager.setExistingAttachments(data.attachments || []);
            resetAttachments();
            openModal(formModal);
            const deletable = data.status === 'draft' || data.status === 'scheduled';
            if (btnDelete) btnDelete.style.display = deletable ? 'inline-flex' : 'none';
            if (deleteForm && deletable) {
                deleteForm.action = form.dataset.destroyTemplate?.replace('__ID__', data.id) || '';
            }
            setInitialFormState(snapshotForm());
            updateSubmitState();
            hasDirtyForm = false;
        });
    });

    btnSend?.addEventListener('click', () => {
        const mode = getSendMode();
        const nowLocal = toLocalInputValue();
        if (mode === 'schedule') {
            actionStatus.value = 'scheduled';
            const chosen = scheduledInput?.value || nowLocal;
            if (scheduledHidden) scheduledHidden.value = chosen;
        } else {
            actionStatus.value = 'sent';
            if (scheduledHidden) scheduledHidden.value = '';
            if (scheduledInput) scheduledInput.value = '';
        }
        hasDirtyForm = false;
    });

    btnDraft?.addEventListener('click', () => {
        actionStatus.value = 'draft';
        if (scheduledHidden) scheduledHidden.value = scheduledInput?.value || '';
        hasDirtyForm = false;
        form?.submit();
    });

    btnDelete?.addEventListener('click', () => {
        if (!deleteForm || !currentEditId) return;
        const action = deleteForm.action || form.dataset.destroyTemplate?.replace('__ID__', currentEditId);
        if (!action) return;
        deleteForm.action = action;
        closeModal(formModal);
        openModal(deleteModal);
    });

    btnConfirmDelete?.addEventListener('click', () => {
        if (!deleteForm?.action) return;
        deleteForm.submit();
    });

    btnCancelDelete?.addEventListener('click', () => {
        closeModal(deleteModal);
        openModal(formModal);
    });

    btnConfirmDraft?.addEventListener('click', () => {
        closeModal(confirmModal);
        if (btnDraft && !btnDraft.disabled) {
            btnDraft.click();
        }
    });

    btnDiscard?.addEventListener('click', () => {
        closeModal(confirmModal);
        hasDirtyForm = false;
        closeModal(formModal);
        form?.reset();
        updateCounters();
        updateSubmitState();
    });

    titleInput?.addEventListener('input', handleFormInputChange);
    bodyInput?.addEventListener('input', handleFormInputChange);

    updateDatetimeValue();
    primeDateTime();
    syncSendModeRadios();
    syncRecipientRadios();
    resetAttachments();
    updateCounters();
    updateSubmitState();
}
