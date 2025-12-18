# Design System quick notes

- **Entry point:** `resources/css/design-system.css` (re-exports `announcement.css` styles). Load this once from `layouts.template` to avoid per-view `@vite` calls.
- **Generic class aliases:** You can use prefix-less classes anywhere:
  - Buttons: `btn`, `btn--primary`, `btn--secondary` (40px high, 14px bold), `btn--danger` (46px square).
  - Toolbar: `toolbar__row toolbar__standalone toolbar--inline` (horizontal, nowrap, 24px gap), `toolbar__chunk--search|filters`.
  - Select/dropdown: wrap with `select`, input with `input--select`, optional caret icon using `select__icon material-symbols-rounded`.
  - Table: `table table--nilai-harian` (No divider full height), `table__head`, `table__body`, `cell--no`, `cell--truncate`, `cell--actions`, status pills reuse `.ann-status--active|inactive|pending`.
- **Migration tip:** Start switching new pages to the generic classes; old `ann-*` remain supported for backward compatibility. When ready, include `@vite(['resources/css/design-system.css'])` in the main layout and remove per-page `announcement.css` imports.
- **Reusable snippets to consider next:** dropdown filter component, status pill, toolbar header with actions, table head/row partials. These reduce copy-paste and keep spacing consistent.

Use this as the reference to standardize components on both Admin Pengumuman and Guru Nilai Harian without editing each widget repeatedly.***

## Reuse playbook (ambil dari halaman Pengumuman)

- **CSS/JS yang wajib dipasang di layout utama**  
  - CSS: `@vite(['resources/css/design-system.css','resources/css/components.css'])`  
  - JS: `@vite(['resources/js/app-ui.js'])` (sudah bundling modul dropdown, modal, datetime, dsb).

- **Struktur partial Blade yang disarankan** (bisa dibuat di `resources/views/components/ui/`):
  - `filter-bar.blade.php`: wrapper row (gap 16px) berisi tombol filter/dropdown dan reset.  
  - `filter-button.blade.php`: tombol dropdown + slot konten menu.  
  - `table-shell.blade.php`: membungkus header + body (grid No/Waktu/Penerima/Subjek/Status).  
  - `modal-shell.blade.php`: overlay + card + header/body/footer slot.  
  - `form-field.blade.php`: label + counter + error + input/textarea slot.  
  - `status-pill.blade.php`: badge untuk status `draft/scheduled/sent`.

- **Contoh markup yang siap dipakai**

Header + filter bar:
```blade
<div class="page-container">
  <div class="toolbar__row toolbar__standalone">
    <h1 class="page-title">Judul Halaman</h1>
    <div class="filters">
      <button class="filter__btn" type="button">
        <span>Filter A</span>
        <span class="material-symbols-rounded">expand_more</span>
      </button>
      <button class="filter__btn" type="button">Filter B</button>
      <button class="reset show" type="button">
        <span class="material-symbols-rounded">refresh</span> Reset
      </button>
    </div>
    <button class="btn btn--primary" type="button">Aksi</button>
  </div>
```

Dropdown menu:
```html
<div class="filter open">
  <button class="filter__btn" type="button">
    <span>Status</span><span class="material-symbols-rounded">expand_more</span>
  </button>
  <div class="filter__menu">
    <button class="filter__option">
      <span class="radio active"></span><span>Semua</span>
    </button>
    <button class="filter__option">
      <span class="radio"></span><span>Draf</span>
    </button>
  </div>
</div>
```

Table shell (grid sama dengan Pengumuman):
```blade
<div class="ann-card ann-card--table">
  <div class="ann-table">
    <div class="ann-table__head">
      <div class="cell cell--no">No.</div>
      <div class="cell">Waktu</div>
      <div class="cell">Penerima</div>
      <div class="cell">Subjek</div>
      <div class="cell cell--status">Status</div>
    </div>
    <div class="ann-table__body">
      <div class="ann-row">
        <div class="cell cell--no">1</div>
        <div class="cell">19 Des</div>
        <div class="cell">Semua</div>
        <div class="cell cell--truncate">Judul...</div>
        <div class="cell cell--status">
          <span class="ann-status ann-status--scheduled">
            <span class="material-symbols-rounded">schedule</span>
            Dijadwalkan
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
```

Modal shell (pakai class umum):
```html
<div class="ann-modal" id="modal-example">
  <div class="ann-modal__overlay"></div>
  <div class="ann-modal__card ann-modal__card--form">
    <div class="ann-modal__header">
      <h2 class="ann-modal__label">Judul Modal</h2>
      <button type="button" class="ann-icon-btn" data-close>
        <span class="material-symbols-rounded">close</span>
      </button>
    </div>
    <div class="ann-modal__body">
      <!-- isi form atau detail -->
    </div>
    <div class="ann-modal__footer">
      <button class="btn btn--secondary" type="button">Batal</button>
      <button class="btn btn--primary" type="button">Simpan</button>
    </div>
  </div>
  <div class="ann-modal__backdrop"></div>
</div>
```

Form field + radio:
```html
<label class="ann-field">
  <span class="ann-field__label">Subjek <span class="ann-counter">0/200</span></span>
  <input class="ann-input" type="text" placeholder="Masukkan subjek">
</label>

<div class="recipient">
  <label class="recipient__option">
    <input type="radio" name="target_role" value="all" checked>
    <span class="radio active"></span><span>Semua</span>
  </label>
  <label class="recipient__option">
    <input type="radio" name="target_role" value="guru">
    <span class="radio"></span><span>Guru</span>
  </label>
</div>
```

- **Spacing & grid yang perlu konsisten**  
  - `page-container` padding: 0 top, 24px kiri/kanan/bawah.  
  - Row/gap utama: 16px (filter bar, table columns).  
  - Modal card radius 16px, shadow sama dengan pengumuman.  
  - Table No. kolom lebar 52px dengan divider; status rata kiri.

- **Checklist saat menerapkan ke halaman baru**
  1) Sertakan CSS/JS di layout.  
  2) Gunakan partial/filter/table/modal di atas; hindari class baru jika yang lama sudah ada.  
  3) Pastikan state default (radio “Semua” checked, hover/focus ring muncul).  
  4) Uji interaksi: buka/tutup modal, dropdown muncul dari atas, focus tidak menggeser konten.

Ini jadi panduan singkat untuk copy/paste dan membuat partial Blade supaya halaman lain langsung mengikuti gaya Pengumuman tanpa re-desain manual.

## Blade components siap pakai (folder `resources/views/components/ui/`)

- `filter-bar` — wrapper untuk kumpulan filter (`filters` class). Slot default untuk item, slot opsional `actions`.
- `filter-dropdown` — satu dropdown filter; props: `label`, `value`, `filter` (isi data-filter), `buttonId` opsional. Slot untuk menu.
- `filter-option` — item menu; props: `value`, `active`. Klik akan membaca `data-value` (JS filters).
- `table-shell` — kerangka tabel; slot `head` untuk header cells, slot default untuk rows.
- `modal-shell` — modal generik; props: `id`, `title`, `cardClass` (default `ann-modal__card--form`); slot `heading` opsional, slot `footer` opsional.
- `form-field` — label + counter + error; props: `label`, `counter`, `error`.
- `status-pill` — badge status; props: `status` (`sent|scheduled|draft|published`), `icon`, `label`.
