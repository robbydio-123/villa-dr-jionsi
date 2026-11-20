document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var el = function (id) { return document.getElementById(id); };
    var modalEl = el('modalFasilitas');
    var hapusEl = el('modalHapusFasilitas');
    var filterForm = el('formFilterFasilitas');

    if (filterForm) {
        filterForm.querySelectorAll('[data-autosubmit]').forEach(function (s) {
            s.addEventListener('change', function () { filterForm.submit(); });
        });
    }

    if (hapusEl) {
        hapusEl.addEventListener('show.bs.modal', function (ev) {
            var btn = ev.relatedTarget;
            if (!btn) { return; }
            el('hapusFasId').value = btn.getAttribute('data-id') || '';
            el('hapusFasNama').textContent = (btn.getAttribute('data-nama') || '') +
                ' (' + (btn.getAttribute('data-kategori') || '') + ')';
        });
    }

    if (!modalEl) { return; }

    var fNama = el('fasNama'), fKategori = el('fasKategori'), fStatus = el('fasStatus'),
        fDeskripsi = el('fasDeskripsi'), grid = el('fasIkonGrid'),
        pratinjau = el('fasIkonPratinjau'), namaIkon = el('fasIkonNama');

    function tampilIkon() {
        var r = grid.querySelector('input[name="ikon"]:checked');
        if (!r) { return; }
        pratinjau.className = 'bi ' + r.value;
        namaIkon.textContent = r.getAttribute('data-nama') || '';
    }

    function pilihIkon(kelas) {
        var target = grid.querySelector('input[name="ikon"][value="' + kelas + '"]') ||
                     grid.querySelector('input[name="ikon"][value="bi-stars"]');
        if (target) { target.checked = true; }
        tampilIkon();
    }

    function isi(data, mode) {
        el('fasAksi').value = mode;
        el('fasId').value = data.id || 0;
        el('judulModalFasilitas').textContent = mode === 'ubah' ? 'Ubah ' + (data.nama || 'Fasilitas') : 'Tambah Fasilitas';
        el('fasSimpan').querySelector('span').textContent = mode === 'ubah' ? 'Simpan Perubahan' : 'Tambah Fasilitas';

        fNama.value = data.nama || '';
        fKategori.value = data.kategori || fKategori.options[0].value;
        fStatus.value = data.status || 'Aktif';
        fDeskripsi.value = data.deskripsi || '';
        pilihIkon(data.ikon || 'bi-stars');

        var alert = modalEl.querySelector('.alert-danger');
        if (alert) { alert.remove(); }
    }

    modalEl.addEventListener('show.bs.modal', function (ev) {
        var btn = ev.relatedTarget;
        if (!btn) { tampilIkon(); return; }
        var mode = btn.getAttribute('data-mode') === 'ubah' ? 'ubah' : 'tambah';
        var data = {};
        if (mode === 'ubah') {
            try { data = JSON.parse(btn.getAttribute('data-fasilitas') || '{}'); } catch (e) { data = {}; }
        }
        isi(data, mode);
    });

    modalEl.addEventListener('shown.bs.modal', function () { fNama.focus(); });
    grid.addEventListener('change', tampilIkon);

    tampilIkon();

    if (modalEl.getAttribute('data-open') === '1' && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
});