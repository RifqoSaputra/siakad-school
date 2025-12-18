import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';

import { initFilters } from './modules/filters';
import { initGuruStatus } from './modules/guruStatus';
import { initAnnouncementForm } from './modules/announcementForm';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initGuruStatus();
    initAnnouncementForm();
});
