{{-- Gaya kaca untuk panel dropdown dan scrollbar daftar, dipakai di seluruh halaman --}}
<style>
    /* Panel dropdown bergaya kaca */
    .dropdown-kaca {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(16px) saturate(160%);
        -webkit-backdrop-filter: blur(16px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 12px 32px rgba(31, 38, 135, 0.15);
    }

    /* Scrollbar kaca biru (Chrome, Edge, Safari) */
    .scroll-kaca::-webkit-scrollbar { width: 6px; }
    .scroll-kaca::-webkit-scrollbar-button { display: none; height: 0; }
    .scroll-kaca::-webkit-scrollbar-track { background: transparent; margin: 4px 0; }
    .scroll-kaca::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.30);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 10px;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    .scroll-kaca::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.50); }

    /* Firefox tidak mendukung gaya di atas, jadi memakai pengaturan standar.
       Dibatasi ke Firefox karena di Chrome baru pengaturan ini menimpa gaya kaca dan memunculkan panah. */
    @supports (-moz-appearance: none) {
        .scroll-kaca { scrollbar-width: thin; scrollbar-color: rgba(59, 130, 246, 0.40) transparent; }
    }
</style>
