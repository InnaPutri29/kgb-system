{{-- Menambahkan tombol lihat/sembunyikan pada setiap kolom kata sandi --}}
<script>
    (function () {
        const ikonMata = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        const ikonMataTertutup = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';

        function pasangTombol() {
            document.querySelectorAll('input[type="password"]').forEach(function (input) {
                if (input.dataset.adaTombolLihat) return;
                input.dataset.adaTombolLihat = '1';

                // Bungkus kolom agar tombol bisa diletakkan di sisi kanan kolom
                const gayaInput = getComputedStyle(input);
                const bungkus = document.createElement('div');
                bungkus.style.position = 'relative';
                bungkus.style.marginTop = gayaInput.marginTop;
                bungkus.style.marginBottom = gayaInput.marginBottom;
                input.style.marginTop = '0';
                input.style.marginBottom = '0';
                input.parentNode.insertBefore(bungkus, input);
                bungkus.appendChild(input);
                input.style.paddingRight = '2.75rem';

                const tombol = document.createElement('button');
                tombol.type = 'button';
                tombol.tabIndex = -1;
                tombol.setAttribute('aria-label', 'Tampilkan kata sandi');
                tombol.title = 'Tampilkan kata sandi';
                tombol.innerHTML = ikonMata;
                Object.assign(tombol.style, {
                    position: 'absolute', right: '0.75rem', top: '50%', transform: 'translateY(-50%)',
                    background: 'none', border: '0', padding: '0', color: '#94a3b8', cursor: 'pointer',
                    display: 'flex', alignItems: 'center',
                });
                tombol.addEventListener('mouseenter', () => tombol.style.color = '#2563eb');
                tombol.addEventListener('mouseleave', () => tombol.style.color = '#94a3b8');
                tombol.addEventListener('click', function () {
                    const terlihat = input.type === 'text';
                    input.type = terlihat ? 'password' : 'text';
                    tombol.innerHTML = terlihat ? ikonMata : ikonMataTertutup;
                    const label = terlihat ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi';
                    tombol.setAttribute('aria-label', label);
                    tombol.title = label;
                    input.focus();
                });
                bungkus.appendChild(tombol);
            });
        }

        // Dijalankan saat halaman siap, dan setelah Alpine selesai menyusun pop-up
        document.addEventListener('DOMContentLoaded', pasangTombol);
        document.addEventListener('alpine:initialized', () => setTimeout(pasangTombol, 0));
        window.addEventListener('load', pasangTombol);
    })();
</script>
