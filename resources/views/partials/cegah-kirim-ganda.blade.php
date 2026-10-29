{{-- Mencegah formulir terkirim dua kali saat tombol simpan diklik berulang --}}
<script>
    document.addEventListener('submit', function (e) {
        const form = e.target;
        // Lewati formulir yang ditangani JavaScript sendiri (fetch/Alpine) dan formulir GET (filter, ekspor)
        if (e.defaultPrevented || (form.getAttribute('method') || 'get').toLowerCase() !== 'post') return;

        if (form.dataset.sedangDikirim) {
            e.preventDefault();
            return;
        }
        form.dataset.sedangDikirim = '1';

        // Ditunda sebentar supaya data tombol yang diklik tetap ikut terkirim
        setTimeout(function () {
            form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(function (tombol) {
                tombol.disabled = true;
                tombol.classList.add('opacity-60', 'cursor-not-allowed');
                if (tombol.tagName === 'BUTTON') {
                    tombol.dataset.teksAsli = tombol.innerHTML;
                    tombol.innerHTML = 'Memproses...';
                }
            });
        }, 0);
    });

    // Aktifkan kembali tombol jika halaman dibuka lagi lewat tombol Kembali di browser
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('form[data-sedang-dikirim]').forEach(function (form) {
            delete form.dataset.sedangDikirim;
            form.querySelectorAll('button, input[type="submit"]').forEach(function (tombol) {
                tombol.disabled = false;
                tombol.classList.remove('opacity-60', 'cursor-not-allowed');
                if (tombol.dataset.teksAsli) {
                    tombol.innerHTML = tombol.dataset.teksAsli;
                    delete tombol.dataset.teksAsli;
                }
            });
        });
    });
</script>
