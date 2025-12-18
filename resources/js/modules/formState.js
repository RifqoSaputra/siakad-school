export function createFormState(options) {
    const {
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
    } = options;

    let initialFormState = null;

    function snapshotForm() {
        return {
            title: titleInput?.value || '',
            body: bodyInput?.value || '',
            sendMode: getSendMode(),
            recipient: getRecipientValue(),
            scheduled: scheduledInput?.value || scheduledHidden?.value || '',
            time: dtpTimeInput?.value || '',
            attachments: getAttachmentCount(),
            removed: getRemovedCount(),
        };
    }

    function setInitialFormState(state) {
        initialFormState = state;
    }

    function hasFormChanged() {
        const current = snapshotForm();
        if (!initialFormState) {
            return !!(current.title || current.body || current.attachments);
        }
        return JSON.stringify(current) !== JSON.stringify(initialFormState);
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
        titleInput?.closest('.field, .ann-field')?.classList.toggle('is-error', titleOver);
        bodyInput?.closest('.field, .ann-field')?.classList.toggle('is-error', bodyOver);
    }

    function updateCounters() {
        if (titleCount) titleCount.textContent = `${(titleInput?.value || '').length}/${TITLE_MAX}`;
        if (bodyCount) bodyCount.textContent = `${(bodyInput?.value || '').length}/${BODY_MAX}`;
        updateFieldErrors();
    }

    return {
        snapshotForm,
        setInitialFormState,
        hasFormChanged,
        updateFieldErrors,
        updateCounters,
        isOverLimit,
        getInitialFormState: () => initialFormState,
    };
}
