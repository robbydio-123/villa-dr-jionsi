/* ==========================================================================
   public/assets/js/refund-tamu.js
   Halaman: Pengajuan Refund (sisi tamu) -> public/refund.php
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

    // Penghitung karakter keterangan
    const ket = document.getElementById('rfKet');
    const cnt = document.getElementById('rfKetCount');
    if (ket && cnt) {
        const updateCount = () => { cnt.textContent = Array.from(ket.value).length; };
        updateCount();
        ket.addEventListener('input', updateCount);
    }

    // Rekening: hanya angka, dan kolom "nama bank" muncul bila memilih Lainnya
    const bank = document.getElementById('rfBank');
    const bankLainWrap = document.getElementById('rfBankLainWrap');
    const bankLain = document.getElementById('rfBankLain');
    if (bank && bankLainWrap) {
        const toggleBankLain = () => {
            const lain = bank.value === 'Lainnya';
            bankLainWrap.classList.toggle('d-none', !lain);
            if (bankLain) bankLain.required = lain;
        };
        toggleBankLain();
        bank.addEventListener('change', toggleBankLain);
    }
    const noRek = document.getElementById('rfNoRek');
    if (noRek) {
        noRek.addEventListener('input', () => {
            noRek.value = noRek.value.replace(/\D+/g, '');
        });
    }

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