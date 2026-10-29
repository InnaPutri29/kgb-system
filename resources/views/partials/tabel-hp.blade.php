{{-- Tabel bertanda .tabel-hp tampil sebagai kartu di layar HP (< 640px), agar semua kolom dan tombol aksi terlihat tanpa digeser.
     Nama kolom diambil otomatis dari judul tabel (thead) dan ditampilkan di sisi kiri setiap nilai. --}}
<style>
    @media (max-width: 639px) {
        table.tabel-hp thead { display: none; }
        table.tabel-hp, table.tabel-hp tbody, table.tabel-hp tr, table.tabel-hp td { display: block; width: 100%; }
        table.tabel-hp tr { padding: 0.75rem 1rem; border-bottom: 1px solid rgba(148, 163, 184, 0.25); }
        /* Dua kolom: judul di kiri, isi di kanan. Judul yang panjang ikut menambah tinggi baris, tidak menimpa baris berikutnya */
        table.tabel-hp td {
            display: grid;
            grid-template-columns: 35% 1fr;
            column-gap: 3%;
            align-items: start;
            padding: 0.3rem 0 !important;
            text-align: left !important;
            border: 0 !important;
            white-space: normal !important;
        }
        table.tabel-hp td > * { grid-column: 2; justify-self: start; }
        table.tabel-hp td::before {
            content: attr(data-label);
            grid-column: 1;
            grid-row: 1 / span 20;
            padding-top: 0.15rem;
            font-size: 0.68rem;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #1e3a8a;
            font-family: var(--font-sans, ui-sans-serif, system-ui, sans-serif);
        }
        /* Sel tanpa judul kolom (mis. pesan "data kosong") memakai lebar penuh */
        table.tabel-hp td:not([data-label])::before, table.tabel-hp td[data-label=""]::before { display: none; }
        table.tabel-hp td:not([data-label]), table.tabel-hp td[data-label=""] { display: block; text-align: center !important; }
        /* Isi sel yang berupa deretan tombol tetap rata kiri */
        table.tabel-hp td > div, table.tabel-hp td > form { justify-content: flex-start !important; }
    }
</style>
<script>
    (function () {
        function beriLabel() {
            document.querySelectorAll('table.tabel-hp').forEach(function (tabel) {
                const judul = [];
                tabel.querySelectorAll('thead tr:last-child th').forEach(function (th) {
                    const lebar = parseInt(th.getAttribute('colspan') || '1', 10);
                    for (let i = 0; i < lebar; i++) judul.push(th.textContent.replace(/\s+/g, ' ').trim());
                });
                tabel.querySelectorAll('tbody tr').forEach(function (tr) {
                    let kolom = 0;
                    tr.querySelectorAll(':scope > td').forEach(function (td) {
                        const lebar = parseInt(td.getAttribute('colspan') || '1', 10);
                        if (lebar === 1 && !td.hasAttribute('data-label')) td.setAttribute('data-label', judul[kolom] || '');
                        kolom += lebar;
                    });
                });
            });
        }
        document.addEventListener('DOMContentLoaded', beriLabel);
        document.addEventListener('alpine:initialized', () => setTimeout(beriLabel, 0));
    })();
</script>
