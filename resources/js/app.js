import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    flatpickr('input[type="date"]', {
        locale: Indonesian,
        altInput: true,
        altFormat: "d F Y",
        dateFormat: "Y-m-d",
    });
});
