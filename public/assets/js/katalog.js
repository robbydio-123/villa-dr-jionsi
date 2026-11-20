
document.addEventListener('DOMContentLoaded', function () {
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroIndicators = document.querySelectorAll('.hero-indicator');
    if (heroSlides.length > 1) {
        let activeSlide = 0;
        window.setInterval(function () {
            heroSlides[activeSlide].classList.remove('is-active');
            heroIndicators[activeSlide]?.classList.remove('is-active');
            activeSlide = (activeSlide + 1) % heroSlides.length;
            heroSlides[activeSlide].classList.add('is-active');
            heroIndicators[activeSlide]?.classList.add('is-active');
        }, 4000);
    }

    const checkinInput   = document.getElementById('waktuCheckin');
    const durasiSelect   = document.getElementById('durasiSewaJam');
    const checkoutInput  = document.getElementById('waktuCheckout');
    const btnMinus       = document.getElementById('btnMinusTamu');
    const btnPlus        = document.getElementById('btnPlusTamu');
    const labelKapasitas = document.getElementById('labelKapasitas');
    const inputKapasitas = document.getElementById('inputKapasitas');
    const selectUrutkan  = document.getElementById('selectUrutkan');
    const facilityChecks = document.querySelectorAll('.filter-facility-check');
    const roomCards      = document.querySelectorAll('.room-card-item');

    function formatDateTimeLocal(date) {
        const pad = (n) => String(n).padStart(2, '0');
        const year    = date.getFullYear();
        const month   = pad(date.getMonth() + 1);
        const day     = pad(date.getDate());
        const hours   = pad(date.getHours());
        const minutes = pad(date.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    if (checkinInput) {
        const now = new Date();
        checkinInput.min = formatDateTimeLocal(now);
    }

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

    if (selectUrutkan) {
        selectUrutkan.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

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

                const match = selected.every(sel => cardFacilities.some(cf => cf.includes(sel)));
                card.style.display = match ? '' : 'none';
            });
        }

        facilityChecks.forEach(cb => {
            cb.addEventListener('change', filterByFacilities);
        });
    }

    if (checkinInput && durasiSelect && checkinInput.value && durasiSelect.value) {
        hitungCheckout();
    }
});