/* ==========================================================================
   galeri-kamar.js : slider foto di halaman detail kamar
   Dimuat hanya di detail.php. Tidak butuh data PHP, jadi aman jadi file sendiri.
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {
    const g = document.getElementById('rdGallery');
    if (!g) return;

    const slides   = g.querySelectorAll('.rd-slide');
    const thumbs   = document.querySelectorAll('.rd-thumb');
    const thumbBox = document.getElementById('rdThumbs');
    const label    = document.querySelector('#rdLabel span');
    const count    = document.getElementById('rdCount');
    const n = slides.length;
    let cur = 0;

    function go(i) {
        if (n < 2) return;
        cur = (i + n) % n;
        slides.forEach((s, k) => s.classList.toggle('active', k === cur));
        thumbs.forEach((t, k) => t.classList.toggle('active', k === cur));
        if (label) label.textContent = slides[cur].dataset.label || 'Kamar';
        if (count) count.textContent = (cur + 1) + ' / ' + n;
        if (thumbBox && thumbs[cur]) {
            thumbBox.scrollTo({
                left: thumbs[cur].offsetLeft - (thumbBox.clientWidth - thumbs[cur].offsetWidth) / 2,
                behavior: 'smooth'
            });
        }
        const next = slides[(cur + 1) % n];          // muat foto berikutnya lebih dulu
        if (next && next.loading === 'lazy') next.loading = 'eager';
    }

    const prev = document.getElementById('rdPrev');
    const nextBtn = document.getElementById('rdNext');
    if (prev)    prev.addEventListener('click', () => go(cur - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => go(cur + 1));
    thumbs.forEach(t => t.addEventListener('click', () => go(parseInt(t.dataset.index, 10))));

    /* Keyboard: panah kiri/kanan saat galeri aktif */
    g.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  { e.preventDefault(); go(cur - 1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); go(cur + 1); }
    });

    /* Geser (swipe) di HP */
    let sx = null, sy = null;
    g.addEventListener('touchstart', function (e) {
        sx = e.touches[0].clientX; sy = e.touches[0].clientY;
    }, { passive: true });
    g.addEventListener('touchend', function (e) {
        if (sx === null) return;
        const dx = e.changedTouches[0].clientX - sx;
        const dy = e.changedTouches[0].clientY - sy;
        if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) go(dx < 0 ? cur + 1 : cur - 1);
        sx = sy = null;
    }, { passive: true });

    /* Layar penuh (klik lagi untuk keluar) */
    const full = document.getElementById('rdFull');
    if (full) full.addEventListener('click', function () {
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            (document.exitFullscreen || document.webkitExitFullscreen).call(document);
        } else {
            const req = g.requestFullscreen || g.webkitRequestFullscreen;
            if (req) req.call(g);
            g.focus();
        }
    });
});