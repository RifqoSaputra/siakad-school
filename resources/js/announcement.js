document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll('.ann-filter');
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
    const resetFilters = document.getElementById('filter-reset');
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
    let datePickerInstance = null;
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
    let attachmentFiles = [];
    let existingAttachments = [];
    let removedAttachments = [];
    const removedInputContainer = document.getElementById('removed_attachments');
    const MAX_ATTACH_TOTAL = 10 * 1024 * 1024;
    let hasDirtyForm = false;
    let initialFormState = null;
    let currentEditId = null;
    const DATE_ERROR_TEXT = 'Tanggal telah berlalu. Silahkan pilih jadwal lain.';
    const TIME_ERROR_TEXT = 'Jam telah berlalu. Silahkan pilih jadwal lain.';
    const INVALID_SCHEDULE_TEXT = 'Jadwal tidak valid. Silahkan pilih jadwal lain.';
    const TITLE_MAX = 200;
    const BODY_MAX = 2000;
    const titleError = document.getElementById('title-error');
    const bodyError = document.getElementById('body-error');

    function closeAllFilters() { filters.forEach(f => f.classList.remove('open')); }

    filters.forEach(f => {
        const btn = f.querySelector('.ann-filter__btn');
        const options = f.querySelectorAll('.ann-filter__option');
        btn?.addEventListener('click', (e) => {
            e.stopPropagation();
            const wasOpen = f.classList.contains('open');
            closeAllFilters();
            if (!wasOpen) {
                f.classList.add('open');
            }
        });
        options.forEach(opt => {
            opt.addEventListener('click', () => {
                const params = new URLSearchParams(window.location.search);
                const key = f.dataset.filter;
                const val = opt.dataset.value;
                const current = params.get(key);
                if (current === val) {
                    params.delete(key);
                } else {
                    params.set(key, val);
                }
                params.delete('page');
                const query = params.toString();
                const baseUrl = window.location.origin + window.location.pathname;
                window.location.href = query ? `${baseUrl}?${query}` : baseUrl;
            });
        });
    });

    document.addEventListener('click', closeAllFilters);

    resetFilters?.addEventListener('click', () => {
        const baseUrl = window.location.origin + window.location.pathname;
        window.location.href = baseUrl;
    });

    document.querySelectorAll('[data-nav-url]').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.navUrl;
            if (url) window.location.href = url;
        });
    });

    function openModal(modal) { modal?.classList.add('show'); }
    function closeModal(modal) { modal?.classList.remove('show'); }
    function getSendMode() {
        const checked = Array.from(sendModeRadios).find(r => r.checked);
        return checked ? checked.value : 'now';
    }

    function syncSendModeRadios() {
        sendModeRadios.forEach(r => {
            const dot = r.nextElementSibling;
            if (dot?.classList.contains('ann-radio')) {
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
            const option = r.closest('.ann-recipient__option');
            if (dot?.classList.contains('ann-radio')) {
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

    function resetAttachments() {
        attachmentFiles = [];
        if (attachmentsInput) attachmentsInput.value = '';
        renderAttachments();
    }

    function formatBytes(size = 0) {
        if (!size) return '0 B';
        const units = ['B','KB','MB','GB'];
        const idx = Math.min(Math.floor(Math.log(size) / Math.log(1024)), units.length - 1);
        return `${(size / Math.pow(1024, idx)).toFixed(idx === 0 ? 0 : 1)} ${units[idx]}`;
    }

    function syncAttachmentInput() {
        if (!attachmentsInput) return;
        if (!canUseDataTransfer) return;
        const dt = new DataTransfer();
        attachmentFiles.forEach(f => dt.items.add(f));
        attachmentsInput.files = dt.files;
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
        const overLimit = totalBytes > MAX_ATTACH_TOTAL;
        if (attachmentsError) attachmentsError.style.display = overLimit ? 'block' : 'none';
        const attachWrap = attachmentsInput?.closest('.ann-attach');
        if (attachWrap) {
            attachWrap.classList.toggle('is-error', overLimit);
        }
        // Existing (persisted) attachments - read-only display
        existingAttachments.forEach((att, idx) => {
            const iconName = att.icon || 'attach_file';
            const isPdf = iconName === 'picture_as_pdf';
            const isImage = iconName === 'image';
            const iconClass = isPdf ? 'pdf-icon' : (isImage ? 'img-icon' : '');
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
            const iconName = isPdf ? 'picture_as_pdf' : (isImage ? 'image' : 'attach_file');
            const iconClass = isPdf ? 'pdf-icon' : (isImage ? 'img-icon' : '');
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
        attachmentsList.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.remove);
                attachmentFiles.splice(idx, 1);
                syncAttachmentInput();
                renderAttachments();
                markDirty();
                updateSubmitState();
            });
        });
        attachmentsList.querySelectorAll('[data-remove-existing]').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.removeExisting);
                const removed = existingAttachments.splice(idx, 1)[0];
                if (removed?.id) removedAttachments.push(removed.id);
                syncRemovedInputs();
                renderAttachments();
                markDirty();
                updateSubmitState();
            });
        });
        syncAttachmentInput();
    }

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

    attachmentsTrigger?.addEventListener('click', () => attachmentsInput?.click());
    attachmentsInput?.addEventListener('change', (e) => {
        const files = Array.from(e.target.files || []);
        const existingSize = existingAttachments.reduce((acc, att) => acc + (Number(att.size) || 0), 0);
        let currentSize = existingSize + attachmentFiles.reduce((acc, f) => acc + f.size, 0);
        let skipped = false;
        files.forEach(file => {
            if (currentSize + file.size <= MAX_ATTACH_TOTAL) {
                attachmentFiles.push(file);
                currentSize += file.size;
            } else {
                skipped = true;
            }
        });
        renderAttachments();
        markDirty();
        updateSubmitState();
        // Jangan clear input; biarkan file tetap terlampir
        const attachWrap = attachmentsInput?.closest('.ann-attach');
        if (skipped) {
            if (attachmentsError) {
                attachmentsError.textContent = 'File tambahan melewati batas total 10MB sehingga tidak diunggah.';
                attachmentsError.style.display = 'block';
            }
            attachWrap?.classList.add('is-error');
        } else if (attachmentsError && currentSize <= MAX_ATTACH_TOTAL) {
            attachmentsError.textContent = attachmentsErrorDefault;
            attachmentsError.style.display = 'none';
            attachWrap?.classList.remove('is-error');
        }
    });

    function syncRemovedInputs() {
        if (!removedInputContainer) return;
        removedInputContainer.innerHTML = '';
        removedAttachments.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_attachments[]';
            input.value = id;
            removedInputContainer.appendChild(input);
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

    function toLocalInputValue(dateObj = new Date()) {
        const d = new Date(dateObj);
        d.setSeconds(0, 0);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hh}:${mm}`;
    }

    function isScheduleMode() {
        return getSendMode() === 'schedule';
    }

    function isDateTimePast() {
        if (!isScheduleMode() || !dtpSelected) return false;
        const now = new Date();
        return dtpSelected < now;
    }

    function hasDateTimeError() {
        const dateError = dtpDisplay?.classList.contains('is-error');
        const timeError = dtpTimeInput?.closest('.ann-datetime__display')?.classList.contains('is-error');
        return !!(dateError || timeError);
    }

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

    function updateCounters() {
        if (titleCount) titleCount.textContent = `${(titleInput?.value || '').length}/${TITLE_MAX}`;
        if (bodyCount) bodyCount.textContent = `${(bodyInput?.value || '').length}/${BODY_MAX}`;
        updateFieldErrors();
    }
    function isOverLimit() {
        const titleLen = (titleInput?.value || '').length;
        const bodyLen = (bodyInput?.value || '').length;
        return titleLen > TITLE_MAX || bodyLen > BODY_MAX;
    }

    function updateFieldErrors() {
        const titleLen = (titleInput?.value || '').length;
        const bodyLen = (bodyInput?.value || '').length;
        const titleOver = titleLen > TITLE_MAX;
        const bodyOver = bodyLen > BODY_MAX;
        if (titleError) {
            titleError.textContent = titleOver ? 'Karakter melebihi batas, sesuaikan isi teks.' : '';
            titleError.style.display = titleOver ? 'block' : 'none';
        }
        if (bodyError) {
            bodyError.textContent = bodyOver ? 'Karakter melebihi batas, sesuaikan isi teks.' : '';
            bodyError.style.display = bodyOver ? 'block' : 'none';
        }
        titleInput?.closest('.ann-field')?.classList.toggle('is-error', titleOver);
        bodyInput?.closest('.ann-field')?.classList.toggle('is-error', bodyOver);
    }

    function getRecipientValue() {
        const checked = Array.from(recipientRadios).find(r => r.checked);
        return checked ? checked.value : '';
    }

    function snapshotForm() {
        return {
            title: titleInput?.value || '',
            body: bodyInput?.value || '',
            sendMode: getSendMode(),
            recipient: getRecipientValue(),
            scheduled: scheduledInput?.value || scheduledHidden?.value || '',
            time: dtpTimeInput?.value || '',
            attachments: attachmentFiles.length,
            removed: removedAttachments.length,
        };
    }

    function hasFormChanged() {
        const current = snapshotForm();
        if (!initialFormState) {
            return !!(current.title || current.body || current.attachments);
        }
        return JSON.stringify(current) !== JSON.stringify(initialFormState);
    }

    function markDirty() { hasDirtyForm = true; }

    function handleFormInputChange() {
        updateCounters();
        updateSubmitState();
        markDirty();
    }

    function syncErrorMessage() {
        if (!dtError) return;
        const dateError = dtpDisplay?.classList.contains('is-error');
        const timeDisplay = dtpTimeInput?.closest('.ann-datetime__display');
        const timeError = timeDisplay?.classList.contains('is-error');
        if (dateError) {
            dtError.textContent = DATE_ERROR_TEXT;
        } else if (timeError) {
            dtError.textContent = timeDisplay?.dataset.errorMessage || TIME_ERROR_TEXT;
        }
        dtError.style.display = (dateError || timeError) ? 'block' : 'none';
        updateSubmitState();
    }

    function formatDisplay(dateObj) {
        if (!dateObj) return '';
        const monthsShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const m = monthsShort[dateObj.getMonth()];
        const d = dateObj.getDate();
        const y = dateObj.getFullYear();
        return `${d} ${m} ${y}`;
    }

    function parseDateInput(str) {
        if (!str) return null;
        const clean = str.trim();
        if (!clean) return null;
        const monthMap = {
            januari:0, jan:0, january:0,
            februari:1, feb:1, february:1,
            maret:2, mar:2, march:2,
            april:3, apr:3,
            mei:4, may:4,
            juni:5, jun:5, june:5,
            juli:6, jul:6, july:6,
            agustus:7, agu:7, agt:7, aug:7, august:7,
            september:8, sep:8,
            oktober:9, okt:9, oct:9, october:9,
            november:10, nov:10,
            desember:11, des:11, dec:11, december:11
        };
        const slash = clean.match(/^(\d{1,2})[\\/-](\d{1,2})[\\/-](\d{2,4})$/);
        if (slash) {
            let [, d, m, y] = slash;
            d = Number(d); m = Number(m) - 1; y = Number(y);
            if (y < 100) y += 2000;
            const dt = new Date(y, m, d);
            return isNaN(dt) ? null : dt;
        }
        const nameMatch = clean.match(/^(\d{1,2})\s+([A-Za-z\.]+)\s+(\d{2,4})$/);
        if (nameMatch) {
            let [, dStr, monthStr, yStr] = nameMatch;
            const key = monthStr.replace('.', '').toLowerCase();
            const m = monthMap[key];
            let y = Number(yStr);
            if (y < 100) y += 2000;
            const d = Number(dStr);
            if (!isNaN(d) && m !== undefined && !isNaN(y)) {
                const dt = new Date(y, m, d);
                return isNaN(dt) ? null : dt;
            }
        }
        const american = clean.match(/^([A-Za-z\.]+)\s+(\d{1,2}),\s*(\d{2,4})$/);
        if (american) {
            let [, monthStr, dStr, yStr] = american;
            const key = monthStr.replace('.', '').toLowerCase();
            const m = monthMap[key];
            let y = Number(yStr);
            if (y < 100) y += 2000;
            const d = Number(dStr);
            if (!isNaN(d) && m !== undefined && !isNaN(y)) {
                const dt = new Date(y, m, d);
                return isNaN(dt) ? null : dt;
            }
        }
        const parts = clean.split(/\\s+/);
        if (parts.length >= 3) {
            const d = Number(parts[0]);
            const monthName = parts[1].toLowerCase();
            const yRaw = parts[2];
            const m = monthMap[monthName];
            let y = Number(yRaw);
            if (!isNaN(y)) {
                if (y < 100) y += 2000;
                if (!isNaN(d) && m !== undefined) {
                    const dt = new Date(y, m, d);
                    return isNaN(dt) ? null : dt;
                }
            }
        }
        const parsed = Date.parse(clean);
        if (isNaN(parsed)) return null;
        const dtParsed = new Date(parsed);
        return new Date(dtParsed.getFullYear(), dtParsed.getMonth(), dtParsed.getDate());
    }

    let dtpSelected = scheduledInput?.value ? new Date(scheduledInput.value) : new Date();
    if (!(dtpSelected instanceof Date) || isNaN(dtpSelected)) dtpSelected = new Date();
    let dtpViewMonth = dtpSelected ? new Date(dtpSelected) : new Date();
    dtpSelected.setSeconds(0, 0);

    function renderCalendar() {
        if (!dtpDays || !dtpMonthLabel) return;
        const view = new Date(dtpViewMonth.getFullYear(), dtpViewMonth.getMonth(), 1);
        const month = view.getMonth();
        const year = view.getFullYear();
        const monthsFull = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        dtpMonthLabel.textContent = `${monthsFull[month]} ${year}`;
        dtpDays.innerHTML = '';
        const now = new Date();
        const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        const startDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        for (let i = 0; i < startDay; i++) {
            const prevDayNum = prevMonthDays - startDay + i + 1;
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'ann-datetime__day ann-datetime__day--empty';
            prevBtn.textContent = prevDayNum;
            const candidate = new Date(year, month - 1, prevDayNum);
            if (candidate < todayStart) {
                prevBtn.classList.add('is-disabled');
            } else {
                prevBtn.addEventListener('click', () => {
                    dtpSelected = new Date(year, month - 1, prevDayNum, dtpSelected.getHours(), dtpSelected.getMinutes());
                    dtpViewMonth = new Date(year, month - 1, 1);
                    updateDatetimeValue();
                    renderCalendar();
                });
            }
            dtpDays.appendChild(prevBtn);
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ann-datetime__day';
            btn.textContent = d;
            const candidate = new Date(year, month, d, dtpSelected.getHours(), dtpSelected.getMinutes());
            if (dtpSelected && candidate.toDateString() === dtpSelected.toDateString()) {
                btn.classList.add('is-selected');
            }
            if (candidate < todayStart) {
                btn.classList.add('is-disabled');
            } else {
                btn.addEventListener('click', () => {
                    dtpSelected = candidate;
                    if (candidate.toDateString() === now.toDateString() && dtpSelected < now) {
                        dtpSelected = now;
                    }
                    updateDatetimeValue();
                    renderCalendar();
                });
            }
            dtpDays.appendChild(btn);
        }

        // Next month leading days to fill grid if needed
        const totalCells = startDay + daysInMonth;
        const nextDaysToShow = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= nextDaysToShow; i++) {
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'ann-datetime__day ann-datetime__day--empty';
            nextBtn.textContent = i;
            nextBtn.addEventListener('click', () => {
                dtpSelected = new Date(year, month + 1, i, dtpSelected.getHours(), dtpSelected.getMinutes());
                dtpViewMonth = new Date(year, month + 1, 1);
                updateDatetimeValue();
                renderCalendar();
            });
            dtpDays.appendChild(nextBtn);
        }
    }

    function updateDatetimeValue(updateDisplay = true) {
        if (!dtpSelected || !scheduledInput) return;
        const isoLocal = toLocalInputValue(dtpSelected);
        scheduledInput.value = isoLocal;
        if (updateDisplay && dtpDisplayText) dtpDisplayText.value = formatDisplay(dtpSelected);
        if (dtpTimeInput) {
            const hh = String(dtpSelected.getHours()).padStart(2, '0');
            const mm = String(dtpSelected.getMinutes()).padStart(2, '0');
            dtpTimeInput.value = `${hh}:${mm}`;
        }
        primeDateTime(isoLocal);
        const isPast = isDateTimePast();
        dtpDisplay?.classList.toggle('is-error', isPast);
        syncErrorMessage();
        updateSubmitState();
    }

    function openPicker() {
        if (dtpPopover) {
            dtpPopover.style.top = '64px';
            dtpPopover.style.left = '0px';
            dtpPopover.style.minWidth = '230px';
        }
        dtpDisplay?.classList.add('is-open');
        dtpPopover?.classList.add('show');
        renderCalendar();
    }
    function closePicker() {
        dtpPopover?.classList.remove('show');
        dtpDisplay?.classList.remove('is-open');
    }

    function isFormFilled() {
        const hasTitle = !!titleInput?.value.trim();
        const hasBody = !!bodyInput?.value.trim();
        const hasDate = !!dtpDisplayText?.value.trim();
        const hasTime = !!dtpTimeInput?.value.trim();
        const hasAttach = attachmentFiles.length > 0;
        return hasTitle || hasBody || hasDate || hasTime || hasAttach;
    }

    function isCompleteDateInput(str) {
        const val = str.trim();
        if (!val) return false;
        if (/^\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}$/.test(val)) return true;
        if (/^\d{1,2}\s+[A-Za-z\.]+\s+\d{2,4}$/.test(val)) return true;
        if (/^[A-Za-z\.]+\s+\d{1,2},\s*\d{2,4}$/.test(val)) return true;
        return /\d{1,2}\s+[A-Za-z]{3,}\s+\d{4}$/.test(val);
    }

    dtpDisplay?.addEventListener('click', (e) => { e.stopPropagation(); openPicker(); dtpDisplay.classList.add('is-open'); dtpDisplayText?.focus(); });
    dtpPopover?.addEventListener('mousedown', () => {
        // Pastikan highlight fokus tetap aktif selama popover terbuka.
        dtpDisplay?.classList.add('is-open');
    });
    dtpDisplayText?.addEventListener('input', () => {
        // Batasi karakter hanya huruf, angka, spasi, / - , .
        dtpDisplayText.value = dtpDisplayText.value.replace(/[^0-9A-Za-z\s\/\-\.,]/g, '');
        const raw = dtpDisplayText.value.trim();
        const parsedDate = parseDateInput(raw);
        const prevSelected = dtpSelected ? new Date(dtpSelected) : null;
        const now = new Date();
        const baseHours = dtpSelected ? dtpSelected.getHours() : now.getHours();
            const baseMinutes = dtpSelected ? dtpSelected.getMinutes() : now.getMinutes();

        if (parsedDate) {
            parsedDate.setHours(baseHours, baseMinutes, 0, 0);
            if (isScheduleMode() && parsedDate < now) {
                dtpDisplay?.classList.add('is-error');
                syncErrorMessage();
                updateSubmitState();
                return;
            }
            dtpSelected = parsedDate;
            dtpViewMonth = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
            updateDatetimeValue(false);
            renderCalendar();
            dtpDisplay?.classList.remove('is-error');
            syncErrorMessage();
            updateSubmitState();
            markDirty();
        } else {
            const empty = raw.length === 0;
            const partial = /^(\d{1,2})\s+[A-Za-z]{2,}$/.test(raw);
            if (empty || partial || !isCompleteDateInput(raw)) {
                dtpDisplay?.classList.remove('is-error');
                if (prevSelected) dtpSelected = prevSelected;
                syncErrorMessage();
                updateSubmitState();
            } else {
                dtpDisplay?.classList.add('is-error');
                syncErrorMessage();
                updateSubmitState();
            }
        }
    });
    dtpDisplayText?.addEventListener('blur', () => {
        const parsedDate = parseDateInput(dtpDisplayText.value);
        if (parsedDate) {
            const now = new Date();
            const baseHours = dtpSelected ? dtpSelected.getHours() : now.getHours();
            const baseMinutes = dtpSelected ? dtpSelected.getMinutes() : now.getMinutes();
            parsedDate.setHours(baseHours, baseMinutes, 0, 0);
            if (isScheduleMode() && parsedDate < now) {
                dtpDisplay?.classList.add('is-error');
                syncErrorMessage();
                updateSubmitState();
            } else {
                dtpSelected = parsedDate;
                dtpViewMonth = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
                updateDatetimeValue(true);
                renderCalendar();
                dtpDisplayText.value = formatDisplay(dtpSelected);
                dtpDisplay?.classList.remove('is-error');
                syncErrorMessage();
                updateSubmitState();
                markDirty();
            }
        } else if (!dtpDisplayText.value.trim()) {
            dtpDisplay?.classList.remove('is-error');
            syncErrorMessage();
            updateSubmitState();
        }
        setTimeout(() => {
            const activeEl = document.activeElement;
            const stillInside = (dtpDisplay?.contains(activeEl) || dtpPopover?.classList.contains('show'));
            if (stillInside) {
                dtpDisplay?.classList.add('is-open');
            } else {
                dtpDisplay?.classList.remove('is-open');
            }
        }, 0);
    });
    dtpPopover?.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', () => closePicker());
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePicker(); });

    dtpPopover?.querySelector('[data-month-nav="prev"]')?.addEventListener('click', (e) => {
        e.stopPropagation();
        dtpViewMonth = new Date(dtpViewMonth.getFullYear(), dtpViewMonth.getMonth() - 1, 1);
        renderCalendar();
    });
    dtpPopover?.querySelector('[data-month-nav="next"]')?.addEventListener('click', (e) => {
        e.stopPropagation();
        dtpViewMonth = new Date(dtpViewMonth.getFullYear(), dtpViewMonth.getMonth() + 1, 1);
        renderCalendar();
    });

    dtpTimeInput?.addEventListener('input', () => {
        if (!dtpSelected) return;
        const timeDisplay = dtpTimeInput.closest('.ann-datetime__display');
        const prevValue = dtpTimeInput.value;
        const prevPos = dtpTimeInput.selectionStart || 0;
        const digitsBeforeCaret = (() => {
            let count = 0;
            for (let i = 0; i < prevPos; i++) {
                if (/\d/.test(prevValue[i])) count++;
            }
            return count;
        })();

        let digits = prevValue.replace(/[^0-9]/g, '');
        if (digits.length > 4) digits = digits.slice(0, 4);

        const hoursPart = digits.slice(0, 2);
        const minutesPart = digits.slice(2, 4);
        const hoursComplete = hoursPart.length === 2;
        const minutesComplete = minutesPart.length === 2;

        const newValue = digits.length <= 2 ? hoursPart : `${hoursPart}:${minutesPart}`;
        dtpTimeInput.value = newValue;

        if (document.activeElement === dtpTimeInput) {
            const caretPos = (() => {
                if (digitsBeforeCaret === 0) return 0;
                let digitCount = 0;
                for (let i = 0; i < newValue.length; i++) {
                    if (/\d/.test(newValue[i])) {
                        digitCount++;
                        if (digitCount === digitsBeforeCaret) return i + 1;
                    }
                }
                return newValue.length;
            })();
            dtpTimeInput.setSelectionRange(caretPos, caretPos);
        }

        const hourValue = hoursComplete ? Number(hoursPart) : NaN;
        const minuteValue = minutesComplete ? Number(minutesPart) : NaN;
        const isSpecialMidnight = hoursComplete && minutesComplete && hourValue === 24 && minuteValue === 0;
        const invalidHourOver24 = hoursComplete && hourValue > 24;
        const invalidHourOver23 = hoursComplete && hourValue > 23 && !isSpecialMidnight;
        const invalidMinute = minutesComplete && minuteValue > 59;

        if (digits.length === 0) {
            if (timeDisplay) {
                timeDisplay.classList.remove('is-error');
                timeDisplay.dataset.errorMessage = '';
            }
            syncErrorMessage();
            updateSubmitState();
            return;
        }

        if (invalidHourOver24 || invalidHourOver23 || invalidMinute) {
            if (timeDisplay) {
                timeDisplay.classList.add('is-error');
                timeDisplay.dataset.errorMessage = invalidHourOver24 ? INVALID_SCHEDULE_TEXT : TIME_ERROR_TEXT;
            }
            syncErrorMessage();
            updateSubmitState();
            return;
        }

        if (isSpecialMidnight) {
            dtpTimeInput.value = '00:00';
            if (document.activeElement === dtpTimeInput) {
                const pos = dtpTimeInput.value.length;
                dtpTimeInput.setSelectionRange(pos, pos);
            }
            if (timeDisplay) {
                timeDisplay.classList.remove('is-error');
                timeDisplay.dataset.errorMessage = '';
            }
            dtpSelected.setHours(0);
            dtpSelected.setMinutes(0);
            updateDatetimeValue();
            renderCalendar();
            dtpDisplay?.classList.remove('is-error');
            syncErrorMessage();
            updateSubmitState();
            markDirty();
            return;
        }

        if (timeDisplay) {
            timeDisplay.classList.remove('is-error');
            timeDisplay.dataset.errorMessage = '';
        }
        if (!hoursComplete || !minutesComplete) {
            syncErrorMessage();
            updateSubmitState();
            return;
        }

        dtpSelected.setHours(hourValue || 0);
        dtpSelected.setMinutes(minuteValue || 0);
        updateDatetimeValue();
        const past = isDateTimePast();
        dtpDisplay?.classList.toggle('is-error', past);
        renderCalendar();
        syncErrorMessage();
        updateSubmitState();
        markDirty();
    });
    function primeDateTime(value) {
        if (!scheduledInput) return;
        const nowLocal = toLocalInputValue();
        scheduledInput.min = nowLocal;
        if (value) {
            scheduledInput.value = value;
        }
        if (datePickerInstance && scheduledInput.value) {
            datePickerInstance.setDate(scheduledInput.value, false);
        }
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
        dtpSelected = new Date();
        dtpViewMonth = new Date(dtpSelected);
        updateDatetimeValue();
        primeDateTime(dtpSelected.toISOString());
        updateCounters();
        updateSubmitState();
        sendModeRadios.forEach(r => { r.checked = r.value === 'now'; });
        applySendMode('now');
        setRecipientValue('all');
        existingAttachments = [];
        removedAttachments = [];
        syncRemovedInputs();
        resetAttachments();
        initialFormState = snapshotForm();
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
                const parsed = new Date(scheduleVal);
                if (!isNaN(parsed)) {
                    dtpSelected = parsed;
                    dtpSelected.setSeconds(0, 0);
                    dtpViewMonth = new Date(parsed);
                    updateDatetimeValue(true);
                }
            }
            const mode = scheduleVal ? 'schedule' : 'now';
            sendModeRadios.forEach(r => { r.checked = r.value === mode; });
            applySendMode(mode);
            updateCounters();
            updateSubmitState();
            existingAttachments = data.attachments || [];
            removedAttachments = [];
            syncRemovedInputs();
            resetAttachments();
            openModal(formModal);
            const deletable = data.status === 'draft' || data.status === 'scheduled';
            if (btnDelete) btnDelete.style.display = deletable ? 'inline-flex' : 'none';
            if (deleteForm && deletable) {
                deleteForm.action = form.dataset.destroyTemplate?.replace('__ID__', data.id) || '';
            }
            initialFormState = snapshotForm();
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
    syncRecipientRadios();
    resetAttachments();
    updateCounters();
    updateSubmitState();
});
