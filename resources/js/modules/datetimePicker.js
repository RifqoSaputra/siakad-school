export function createDatetimePicker(options) {
    const {
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
        onDirty = () => {},
        onSubmitStateChange = () => {},
        DATE_ERROR_TEXT,
        TIME_ERROR_TEXT,
        INVALID_SCHEDULE_TEXT,
    } = options;

    let dtpSelected = scheduledInput?.value ? new Date(scheduledInput.value) : new Date();
    if (!(dtpSelected instanceof Date) || isNaN(dtpSelected)) dtpSelected = new Date();
    let dtpViewMonth = dtpSelected ? new Date(dtpSelected) : new Date();
    dtpSelected.setSeconds(0, 0);

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

    function formatDisplay(dateObj) {
        if (!dateObj) return '';
        const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
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
            januari: 0,
            jan: 0,
            january: 0,
            februari: 1,
            feb: 1,
            february: 1,
            maret: 2,
            mar: 2,
            march: 2,
            april: 3,
            apr: 3,
            mei: 4,
            may: 4,
            juni: 5,
            jun: 5,
            june: 5,
            juli: 6,
            jul: 6,
            july: 6,
            agustus: 7,
            agu: 7,
            agt: 7,
            aug: 7,
            august: 7,
            september: 8,
            sep: 8,
            oktober: 9,
            okt: 9,
            oct: 9,
            october: 9,
            november: 10,
            nov: 10,
            desember: 11,
            des: 11,
            dec: 11,
            december: 11,
        };
        const slash = clean.match(/^(\d{1,2})[/-](\d{1,2})[/-](\d{2,4})$/);
        if (slash) {
            let [, d, m, y] = slash;
            d = Number(d);
            m = Number(m) - 1;
            y = Number(y);
            if (y < 100) y += 2000;
            const dt = new Date(y, m, d);
            return isNaN(dt) ? null : dt;
        }
        const nameMatch = clean.match(/^(\d{1,2})\s+([A-Za-z.]+)\s+(\d{2,4})$/);
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
        const american = clean.match(/^([A-Za-z.]+)\s+(\d{1,2}),\s*(\d{2,4})$/);
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
        const parts = clean.split(/\s+/);
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
        dtError.style.display = dateError || timeError ? 'block' : 'none';
        onSubmitStateChange();
    }

    function renderCalendar() {
        if (!dtpDays || !dtpMonthLabel) return;
        const view = new Date(dtpViewMonth.getFullYear(), dtpViewMonth.getMonth(), 1);
        const month = view.getMonth();
        const year = view.getFullYear();
        const monthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
                    dtpSelected = new Date(
                        year,
                        month - 1,
                        prevDayNum,
                        dtpSelected.getHours(),
                        dtpSelected.getMinutes(),
                    );
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

        const totalCells = startDay + daysInMonth;
        const nextDaysToShow = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= nextDaysToShow; i++) {
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'ann-datetime__day ann-datetime__day--empty';
            nextBtn.textContent = i;
            nextBtn.addEventListener('click', () => {
                dtpSelected = new Date(
                    year,
                    month + 1,
                    i,
                    dtpSelected.getHours(),
                    dtpSelected.getMinutes(),
                );
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
        onSubmitStateChange();
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

    function isCompleteDateInput(str) {
        const val = str.trim();
        if (!val) return false;
        if (/^\d{1,2}[/-]\d{1,2}[/-]\d{2,4}$/.test(val)) return true;
        if (/^\d{1,2}\s+[A-Za-z.]+\s+\d{2,4}$/.test(val)) return true;
        if (/^[A-Za-z.]+\s+\d{1,2},\s*\d{2,4}$/.test(val)) return true;
        return /\d{1,2}\s+[A-Za-z]{3,}\s+\d{4}$/.test(val);
    }

    function bindEvents() {
        dtpDisplay?.addEventListener('click', (e) => {
            e.stopPropagation();
            openPicker();
            dtpDisplay.classList.add('is-open');
            dtpDisplayText?.focus();
        });
        dtpPopover?.addEventListener('mousedown', () => {
            dtpDisplay?.classList.add('is-open');
        });
        dtpDisplayText?.addEventListener('input', () => {
            dtpDisplayText.value = dtpDisplayText.value.replace(/[^0-9A-Za-z\s/.,-]/g, '');
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
                    onSubmitStateChange();
                    return;
                }
                dtpSelected = parsedDate;
                dtpViewMonth = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
                updateDatetimeValue(false);
                renderCalendar();
                dtpDisplay?.classList.remove('is-error');
                syncErrorMessage();
                onSubmitStateChange();
                onDirty();
            } else {
                const empty = raw.length === 0;
                const partial = /^(\d{1,2})\s+[A-Za-z]{2,}$/.test(raw);
                if (empty || partial || !isCompleteDateInput(raw)) {
                    dtpDisplay?.classList.remove('is-error');
                    if (prevSelected) dtpSelected = prevSelected;
                    syncErrorMessage();
                    onSubmitStateChange();
                } else {
                    dtpDisplay?.classList.add('is-error');
                    syncErrorMessage();
                    onSubmitStateChange();
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
                    onSubmitStateChange();
                } else {
                    dtpSelected = parsedDate;
                    dtpViewMonth = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
                    updateDatetimeValue(true);
                    renderCalendar();
                    dtpDisplayText.value = formatDisplay(dtpSelected);
                    dtpDisplay?.classList.remove('is-error');
                    syncErrorMessage();
                    onSubmitStateChange();
                    onDirty();
                }
            } else if (!dtpDisplayText.value.trim()) {
                dtpDisplay?.classList.remove('is-error');
                syncErrorMessage();
                onSubmitStateChange();
            }
            setTimeout(() => {
                const activeEl = document.activeElement;
                const stillInside = dtpDisplay?.contains(activeEl) || dtpPopover?.classList.contains('show');
                if (stillInside) {
                    dtpDisplay?.classList.add('is-open');
                } else {
                    dtpDisplay?.classList.remove('is-open');
                }
            }, 0);
        });
        dtpPopover?.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => closePicker());
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePicker();
        });

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
                onSubmitStateChange();
                return;
            }

            if (invalidHourOver24 || invalidHourOver23 || invalidMinute) {
                if (timeDisplay) {
                    timeDisplay.classList.add('is-error');
                    timeDisplay.dataset.errorMessage = invalidHourOver24
                        ? INVALID_SCHEDULE_TEXT
                        : TIME_ERROR_TEXT;
                }
                syncErrorMessage();
                onSubmitStateChange();
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
                onSubmitStateChange();
                onDirty();
                return;
            }

            if (timeDisplay) {
                timeDisplay.classList.remove('is-error');
                timeDisplay.dataset.errorMessage = '';
            }
            if (!hoursComplete || !minutesComplete) {
                syncErrorMessage();
                onSubmitStateChange();
                return;
            }

            dtpSelected.setHours(hourValue || 0);
            dtpSelected.setMinutes(minuteValue || 0);
            updateDatetimeValue();
            const past = isDateTimePast();
            dtpDisplay?.classList.toggle('is-error', past);
            renderCalendar();
            syncErrorMessage();
            onSubmitStateChange();
            onDirty();
        });
    }

    function primeDateTime(value) {
        if (!scheduledInput) return;
        const nowLocal = toLocalInputValue();
        scheduledInput.min = nowLocal;
        if (value) {
            scheduledInput.value = value;
        }
        if (scheduledInput.value && scheduledHidden) {
            scheduledHidden.value = scheduledInput.value;
        }
    }

    function setSelected(dateVal) {
        const parsed = dateVal ? new Date(dateVal) : null;
        if (parsed && !isNaN(parsed)) {
            dtpSelected = parsed;
            dtpSelected.setSeconds(0, 0);
            dtpViewMonth = new Date(parsed);
            updateDatetimeValue(true);
        }
    }

    function resetToNow() {
        dtpSelected = new Date();
        dtpViewMonth = new Date(dtpSelected);
        updateDatetimeValue();
        primeDateTime(dtpSelected.toISOString());
    }

    bindEvents();

    return {
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
        getSelected: () => dtpSelected,
        toLocalInputValue,
    };
}
