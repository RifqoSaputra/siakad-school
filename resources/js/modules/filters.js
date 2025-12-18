export function initFilters() {
    const filters = document.querySelectorAll('.filter, .ann-filter');
    const resetFilters = document.getElementById('filter-reset');

    function closeAllFilters() {
        filters.forEach((filter) => filter.classList.remove('open'));
    }

    filters.forEach((filter) => {
        const btn = filter.querySelector('.filter__btn, .ann-filter__btn');
        const options = filter.querySelectorAll('.filter__option, .ann-filter__option');
        btn?.addEventListener('click', (e) => {
            e.stopPropagation();
            const wasOpen = filter.classList.contains('open');
            closeAllFilters();
            if (!wasOpen) {
                filter.classList.add('open');
            }
        });
        options.forEach((opt) => {
            opt.addEventListener('click', () => {
                const params = new URLSearchParams(window.location.search);
                const key = filter.dataset.filter;
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

    document.querySelectorAll('[data-nav-url]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.navUrl;
            if (url) window.location.href = url;
        });
    });
}
