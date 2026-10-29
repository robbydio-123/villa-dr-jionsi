/* ==========================================================================
   public/assets/js/refund-tamu.js
   Halaman: Pengajuan Refund (sisi tamu) -> public/refund.php
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

    // Penghitung karakter keterangan
    const ket = document.getElementById('rfKet');
    const cnt = document.getElementById('rfKetCount');
    if (ket && cnt) ket.addEventListener('input', () => { cnt.textContent = ket.value.length; });

    // Nama file lampiran + batas ukuran 2 MB
    const file   = document.getElementById('rfFile');
    const nameEl = document.getElementById('rfFileName');
    if (file && nameEl) {
        file.addEventListener('change', () => {
            const f = file.files[0];
            if (!f) return;
            if (f.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2 MB.');
                file.value = '';
                return;
            }
            nameEl.textContent = f.name;
        });
    }

    // Salin nomor pengajuan
    document.querySelectorAll('[data-copy]').forEach(b => b.addEventListener('click', () => {
        navigator.clipboard?.writeText(b.dataset.copy);
        const i = b.querySelector('i');
        i.className = 'bi bi-check2';
        setTimeout(() => { i.className = 'bi bi-files'; }, 1200);
    }));
});
