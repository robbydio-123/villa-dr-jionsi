(function () {
    'use strict';

    var TZ = 'Asia/Jakarta';
    var HARI = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var mulai = Date.now();
    var sudahReload = false;

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function waktuJakarta() {
        var fmt = new Intl.DateTimeFormat('en-GB', {
            timeZone: TZ, year: 'numeric', month: 'numeric', day: 'numeric',
            hour: 'numeric', minute: 'numeric', hourCycle: 'h23'
        });
        var o = {};
        fmt.formatToParts(new Date()).forEach(function (p) { o[p.type] = parseInt(p.value, 10); });
        o.hari = new Date(Date.UTC(o.year, o.month - 1, o.day)).getUTCDay();
        return o;
    }

    function sapaan(jam) {
        if (jam < 11) return 'pagi';
        if (jam < 15) return 'siang';
        return jam < 18 ? 'sore' : 'malam';
    }

    function setText(id, teks) {
        var el = document.getElementById(id);
        if (el && el.textContent !== teks) el.textContent = teks;
    }

    function tickJam() {
        var t = waktuJakarta();
        setText('dashTanggal', HARI[t.hari] + ', ' + t.day + ' ' + BULAN[t.month - 1] + ' ' + t.year);
        setText('dashJam', pad(t.hour) + '.' + pad(t.minute));
        setText('dashSapaan', sapaan(t.hour));
    }

    function tickTimer() {
        var lewat = Math.floor((Date.now() - mulai) / 1000);
        var habis = false;
        document.querySelectorAll('[data-sisa]').forEach(function (el) {
            var sisa = parseInt(el.getAttribute('data-sisa'), 10) - lewat;
            if (sisa <= 0) { habis = true; sisa = 0; }
            if (el.getAttribute('data-format') === 'menit') {
                el.textContent = 'Sisa waktu ' + Math.max(1, Math.ceil(sisa / 60)) + ' mnt';
            } else {
                el.textContent = pad(Math.floor(sisa / 60)) + ':' + pad(sisa % 60);
            }
        });
        // Keep habis: muat ulang sekali agar status dan daftar diperbarui dari server
        if (habis && !sudahReload) {
            sudahReload = true;
            setTimeout(function () { window.location.reload(); }, 1500);
        }
    }

    function setVal(id, v) { var el = document.getElementById(id); if (el) el.value = v; }

    document.addEventListener('DOMContentLoaded', function () {
        tickJam();
        tickTimer();
        setInterval(function () { tickJam(); tickTimer(); }, 1000);

        var modal = document.getElementById('modalBukti');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (ev) {
            var b = ev.relatedTarget;
            if (!b) return;
            var d = b.dataset;
            setVal('bReservasi', d.reservasi);
            setVal('bPembayaran', d.pembayaran);
            setText('bKode', d.kode);
            setText('bNama', d.nama);
            setText('bKamar', d.kamar);
            setText('bMenginap', d.menginap);
            setText('bTagihan', d.tagihan);
            setText('bNominal', d.nominal);

            var img = document.getElementById('bImg');
            var gagal = document.getElementById('bGagal');
            gagal.classList.add('d-none');
            img.classList.remove('d-none');
            img.onerror = function () { img.classList.add('d-none'); gagal.classList.remove('d-none'); };
            img.src = d.bukti;
            document.getElementById('bLink').href = d.bukti;
        });
    });
})();