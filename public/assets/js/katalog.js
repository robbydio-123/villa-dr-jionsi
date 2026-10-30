/**
 * public/assets/js/katalog.js
 * Logika interaktif pencarian kamar, kalkulasi otomatis waktu check-out, sortir, dan filter fasilitas (Native JS)
 */
document.addEventListener('DOMContentLoaded', function () {
    const checkinInput   = document.getElementById('waktuCheckin');
    const durasiSelect   = document.getElementById('durasiSewa');
    const checkoutInput  = document.getElementById('waktuCheckout');
    const btnMinus       = document.getElementById('btnMinusTamu');
    const btnPlus        = document.getElementById('btnPlusTamu');
    const labelKapasitas = document.getElementById('labelKapasitas');
    const inputKapasitas = document.getElementById('inputKapasitas');
    const selectUrutkan  = document.getElementById('selectUrutkan');
    const facilityChecks = document.querySelectorAll('.filter-facility-check');
    const roomCards      = document.querySelectorAll('.room-card-item');

    /**
     * Format objek Date ke format string "YYYY-MM-DDTHH:mm" untuk <input type="datetime-local">
     */
    function formatDateTimeLocal(date) {
        const pad = (n) => String(n).padStart(2, '0');
        const year    = date.getFullYear();
        const month   = pad(date.getMonth() + 1);
        const day     = pad(date.getDate());
        const hours   = pad(date.getHours());
        const minutes = pad(date.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // 1. Batasi waktu check-in minimum ke waktu sekarang jika input masih kosong
    if (checkinInput) {
        const now = new Date();
        checkinInput.min = formatDateTimeLocal(now);
    }

    /**
     * 2. Hitung Waktu Check-out Otomatis:
     *    Waktu Check-out = Waktu Check-in + Durasi (jam)
     */
    function hitungCheckout() {
        if (!checkinInput || !durasiSelect || !checkoutInput) return;

        const checkinVal = checkinInput.value;
        const durasiJam  = parseInt(durasiSelect.value, 10);

        if (checkinVal && !isNaN(durasiJam) && durasiJam > 0) {
            const checkinDate = new Date(checkinVal);
            if (!isNaN(checkinDate.getTime())) {
                const checkoutDate = new Date(checkinDate.getTime() + (durasiJam * 60 * 60 * 1000));
                checkoutInput.value = formatDateTimeLocal(checkoutDate);
                return;
            }
        }

        // Jangan hapus jika sudah ada nilai awal dari server (misal saat reload)
        if (!checkinVal || !durasiJam) {
            checkoutInput.value = '';
        }
    }

    if (checkinInput) {
        checkinInput.addEventListener('change', hitungCheckout);
        checkinInput.addEventListener('input', hitungCheckout);
    }

    if (durasiSelect) {
        durasiSelect.addEventListener('change', hitungCheckout);
    }

    /**
     * 3. Interaksi Tombol Kapasitas Tamu (- dan +)
     */
    if (btnMinus && btnPlus && labelKapasitas && inputKapasitas) {
        const MIN_TAMU  = 1;
        const MAKS_TAMU = 10;
        let jumlah = parseInt(inputKapasitas.value, 10) || 2;

        function updateKapasitas(baru) {
            jumlah = Math.max(MIN_TAMU, Math.min(MAKS_TAMU, baru));
            inputKapasitas.value = jumlah;
            labelKapasitas.value = `${jumlah} Tamu`;

            btnMinus.disabled = (jumlah <= MIN_TAMU);
            btnPlus.disabled  = (jumlah >= MAKS_TAMU);
        }

        btnMinus.addEventListener('click', function () {
            updateKapasitas(jumlah - 1);
        });

        btnPlus.addEventListener('click', function () {
            updateKapasitas(jumlah + 1);
        });

        updateKapasitas(jumlah);
    }

    /**
     * 4. Dropdown Sortir Kamar (Otomatis reload dengan param sort)
     */
    if (selectUrutkan) {
        selectUrutkan.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

    /**
     * 5. Filter Cepat Fasilitas (Client-side instan tanpa reload)
     */
    if (facilityChecks.length > 0 && roomCards.length > 0) {
        function filterByFacilities() {
            const selected = Array.from(facilityChecks)
                .filter(cb => cb.checked)
                .map(cb => cb.value.toLowerCase());

            roomCards.forEach(card => {
                if (selected.length === 0) {
                    card.style.display = '';
                    return;
                }
                let cardFacilities = [];
                try {
                    cardFacilities = JSON.parse(card.dataset.facilities || '[]').map(f => f.toLowerCase());
                } catch (e) {}

                // Cek apakah semua fasilitas yang dicentang dimiliki kamar
                const match = selected.every(sel => cardFacilities.some(cf => cf.includes(sel)));
                card.style.display = match ? '' : 'none';
            });
        }

        facilityChecks.forEach(cb => {
            cb.addEventListener('change', filterByFacilities);
        });
    }

    // Hitung checkout jika check-in dan durasi sudah terisi
    if (checkinInput && durasiSelect && checkinInput.value && durasiSelect.value) {
        hitungCheckout();
    }
});
