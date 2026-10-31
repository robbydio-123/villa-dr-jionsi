<?php

$pickerSmall       = !empty($pickerSmall);
$pickerPlaceholder = !empty($pickerPlaceholder);
$duration          = (int)$duration;
$isPackage         = in_array($duration, DURATION_PACKAGES, true);
$isNights          = !$isPackage && $duration > 24;
$nightsValue       = $isNights ? intdiv($duration, 24) : 2;
$sizeSelect        = $pickerSmall ? ' form-select-sm' : '';
$sizeGroup         = $pickerSmall ? ' input-group-sm' : '';
$labels            = [3 => '3 Jam', 6 => '6 Jam', 12 => '12 Jam', 24 => '24 Jam'];
?>
<div class="drj-duration">
    <select id="<?= h($pickerId) ?>Paket" class="form-select<?= $sizeSelect ?>" aria-label="Durasi sewa">
        <?php if ($pickerPlaceholder && $duration === 0): ?>
            <option value="" selected disabled>Pilih Jam</option>
        <?php endif; ?>
        <?php foreach ($labels as $hours => $label): ?>
            <option value="<?= $hours ?>" <?= $duration === $hours ? 'selected' : '' ?>><?= h($label) ?></option>
        <?php endforeach; ?>
        <option value="malam" <?= $isNights ? 'selected' : '' ?>>Lebih dari 24 jam (Menginap)</option>
    </select>

    <div id="<?= h($pickerId) ?>MalamWrap" class="input-group<?= $sizeGroup ?> mt-2<?= $isNights ? '' : ' d-none' ?>">
        <input type="number" id="<?= h($pickerId) ?>Malam" class="form-control"
               min="2" max="<?= MAX_BOOKING_NIGHTS ?>" step="1" value="<?= $nightsValue ?>" aria-label="Jumlah malam">
        <span class="input-group-text">malam</span>
    </div>

    <input type="hidden" id="<?= h($pickerId) ?>Jam" name="<?= h($pickerName) ?>" value="<?= $duration > 0 ? $duration : '' ?>">
</div>
<script>
(function () {
    const id = <?= json_encode($pickerId) ?>;
    const paket = document.getElementById(id + 'Paket');
    const wrap  = document.getElementById(id + 'MalamWrap');
    const malam = document.getElementById(id + 'Malam');
    const jam   = document.getElementById(id + 'Jam');
    const MIN_NIGHTS = 2, MAX_NIGHTS = <?= MAX_BOOKING_NIGHTS ?>;
    if (!paket || !wrap || !malam || !jam) return;

    function clampNights() {
        let n = parseInt(malam.value, 10);
        if (Number.isNaN(n)) n = MIN_NIGHTS;
        return Math.max(MIN_NIGHTS, Math.min(MAX_NIGHTS, n));
    }

    function sync(fixNightsField) {
        let hours;
        if (paket.value === 'malam') {
            wrap.classList.remove('d-none');
            const n = clampNights();
            if (fixNightsField) malam.value = n;
            hours = n * 24;
        } else {
            wrap.classList.add('d-none');
            hours = parseInt(paket.value, 10);
        }
        jam.value = Number.isNaN(hours) ? '' : hours;

        jam.dispatchEvent(new Event('change', { bubbles: true }));
    }

    paket.addEventListener('change', function () { sync(true); });
    malam.addEventListener('input',  function () { sync(false); });
    malam.addEventListener('change', function () { sync(true); });
})();
</script>