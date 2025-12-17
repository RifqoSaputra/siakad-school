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
