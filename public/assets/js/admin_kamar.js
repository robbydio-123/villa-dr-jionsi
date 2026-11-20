document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var FASILITAS = [
        { icon: 'bi-droplet-half', label: 'Air Panas',     re: /air panas/i },
        { icon: 'bi-tv',           label: 'Smart TV',      re: /smart tv/i },
        { icon: 'bi-wifi',         label: 'Wi-Fi',         re: /wi-?fi/i },
        { icon: 'bi-door-closed',  label: 'KM Dalam',      re: /kamar mandi dalam|km dalam/i },
        { icon: 'bi-cup-hot',      label: 'Dapur Bersama', re: /dapur bersama|dapur/i },
        { icon: 'bi-image',        label: 'Pemandangan',   re: /pemandangan|pegunungan|gunung/i }
    ];

    var modalEl = document.getElementById('modalKamar');
    var hapusEl = document.getElementById('modalHapus');
    var filterForm = document.getElementById('formFilterKamar');

    if (filterForm) {
        filterForm.querySelectorAll('[data-autosubmit]').forEach(function (el) {
            el.addEventListener('change', function () { filterForm.submit(); });
        });
    }

    if (hapusEl) {
        hapusEl.addEventListener('show.bs.modal', function (ev) {
            var btn = ev.relatedTarget;
            if (!btn) { return; }
            document.getElementById('hapusId').value = btn.getAttribute('data-id') || '';
            document.getElementById('hapusNomor').textContent = btn.getAttribute('data-nomor') || '';
            document.getElementById('hapusTipe').textContent = 'Kamar ' + (btn.getAttribute('data-nomor') || '') +
                ' (' + (btn.getAttribute('data-tipe') || '') + ')';
        });
    }

    if (!modalEl) { return; }

    var el = function (id) { return document.getElementById(id); };
    var fNomor = el('kamarNomor'), fTipe = el('kamarTipe'), fLantai = el('kamarLantai'),
        fKapasitas = el('kamarKapasitas'), fHarga = el('kamarHarga'), fStatus = el('kamarStatus'),
        fCatatan = el('kamarCatatan'), fDeskripsi = el('kamarDeskripsi'),
        wrapCatatan = el('kamarCatatanWrap'), boxFasilitas = el('kamarFasilitasPreview');

    var wrapOffline = el('kamarOfflineWrap'), optLama = el('kamarStatusLama');
    var offIds = ['offCheckin', 'offCheckout', 'offNama', 'offWa', 'offTamu'];

    function tampilStatus() {
        wrapCatatan.classList.toggle('d-none', fStatus.value !== 'Perbaikan');
        wrapOffline.classList.toggle('d-none', fStatus.value !== 'Dipesan');
    }

    function isiOffline(d) {
        d = d || {};
        el('offCheckin').value = d.off_checkin || '';
        el('offCheckout').value = d.off_checkout || '';
        el('offNama').value = d.off_nama || '';
        el('offWa').value = d.off_whatsapp || '';
        el('offTamu').value = d.off_tamu || 1;
    }

    var MAKS_FOTO = 10, MAKS_BYTE = 2 * 1024 * 1024;
    var boxLama = el('kamarFotoLama'), boxBaru = el('kamarFotoBaru'), btnTambahFoto = el('kamarTambahFoto');

    function buat(tag, kelas) {
        var e = document.createElement(tag);
        if (kelas) { e.className = kelas; }
        return e;
    }

    function hitungFoto() {
        var sisa = boxLama.querySelectorAll('input[name="hapus_foto_id[]"]:not(:checked)').length;
        var total = sisa + boxBaru.children.length;
        btnTambahFoto.disabled = total >= MAKS_FOTO;
        el('kamarFotoInfo').textContent = total + ' / ' + MAKS_FOTO + ' foto';
    }

    function renderFotoLama(daftar) {
        boxLama.innerHTML = '';
        daftar.forEach(function (f, i) {
            var col = buat('div', 'kamar-foto-item');
            var kartu = buat('div', 'kamar-foto-card');
            var img = buat('img');
            img.src = f.url;
            img.alt = f.label || 'Foto kamar';

            var label = buat('input', 'form-control form-control-sm mt-2');
            label.type = 'text';
            label.name = 'label_lama[' + f.id + ']';
            label.value = f.label || '';
            label.maxLength = 50;
            label.placeholder = 'Label foto';

            var barisSampul = buat('div', 'form-check mt-2 small');
            var radio = buat('input', 'form-check-input');
            radio.type = 'radio';
            radio.name = 'sampul';
            radio.value = f.id;
            radio.id = 'sampul' + f.id;
            radio.checked = (i === 0);
            var lblRadio = buat('label', 'form-check-label');
            lblRadio.htmlFor = radio.id;
            lblRadio.textContent = 'Foto utama';
            barisSampul.appendChild(radio);
            barisSampul.appendChild(lblRadio);

            var barisHapus = buat('div', 'form-check small text-danger');
            var cb = buat('input', 'form-check-input');
            cb.type = 'checkbox';
            cb.name = 'hapus_foto_id[]';
            cb.value = f.id;
            cb.id = 'hapus' + f.id;
            var lblCb = buat('label', 'form-check-label');
            lblCb.htmlFor = cb.id;
            lblCb.textContent = 'Hapus';
            barisHapus.appendChild(cb);
            barisHapus.appendChild(lblCb);
            cb.addEventListener('change', function () {
                kartu.classList.toggle('is-hapus', cb.checked);
                hitungFoto();
            });

            kartu.appendChild(img);
            kartu.appendChild(label);
            kartu.appendChild(barisSampul);
            kartu.appendChild(barisHapus);
            col.appendChild(kartu);
            boxLama.appendChild(col);
        });
        hitungFoto();
    }

    function tambahBarisFoto() {
        var baris = buat('div', 'kamar-foto-upload-row row g-2 align-items-center');

        var cFile = buat('div', 'col-md-6');
        var file = buat('input', 'form-control form-control-sm');
        file.type = 'file';
        file.name = 'foto[]';
        file.accept = 'image/jpeg,image/png,image/webp';
        file.addEventListener('change', function () {
            if (file.files[0] && file.files[0].size > MAKS_BYTE) {
                alert('Ukuran foto terlalu besar (maksimal 2 MB).');
                file.value = '';
            }
        });
        cFile.appendChild(file);

        var cLabel = buat('div', 'col-9 col-md-5');
        var label = buat('input', 'form-control form-control-sm');
        label.type = 'text';
        label.name = 'label_baru[]';
        label.maxLength = 50;
        label.placeholder = 'Label (mis. Kamar Mandi)';
        cLabel.appendChild(label);

        var cBtn = buat('div', 'col-3 col-md-1 text-end');
        var btn = buat('button', 'btn btn-sm btn-outline-danger');
        btn.type = 'button';
        btn.title = 'Buang baris ini';
        btn.innerHTML = '<i class="bi bi-x-lg"></i>';
        btn.addEventListener('click', function () { baris.remove(); hitungFoto(); });
        cBtn.appendChild(btn);

        baris.appendChild(cFile);
        baris.appendChild(cLabel);
        baris.appendChild(cBtn);
        boxBaru.appendChild(baris);
        hitungFoto();
    }
    btnTambahFoto.addEventListener('click', tambahBarisFoto);

    function tampilFasilitas() {
        var teks = fDeskripsi.value;
        var ketemu = FASILITAS.filter(function (f) { return f.re.test(teks); });
        var bawaan = false;
        if (!ketemu.length) {
            bawaan = true;
            ketemu = [FASILITAS[0], FASILITAS[1]];
        }
        boxFasilitas.innerHTML = '';
        ketemu.forEach(function (f) {
            var b = document.createElement('span');
            b.className = 'drj-badge';
            var i = document.createElement('i');
            i.className = 'bi ' + f.icon + ' me-1';
            b.appendChild(i);
            b.appendChild(document.createTextNode(f.label));
            boxFasilitas.appendChild(b);
        });
        if (bawaan) {
            var n = document.createElement('small');
            n.className = 'text-muted align-self-center';
            n.textContent = '(bawaan, karena belum ada kata kunci fasilitas di deskripsi)';
            boxFasilitas.appendChild(n);
        }
    }

    function isi(data, mode) {
        el('kamarAksi').value = mode;
        el('kamarId').value = data.id || 0;
        el('judulModalKamar').textContent = mode === 'ubah' ? 'Ubah Kamar ' + (data.nomor_kamar || '') : 'Tambah Kamar';
        el('kamarSimpan').querySelector('span').textContent = mode === 'ubah' ? 'Simpan Perubahan' : 'Tambah Kamar';

        fNomor.value = data.nomor_kamar || '';
        fTipe.value = data.tipe_kamar || '';
        fLantai.value = data.lantai || 1;
        fKapasitas.value = data.kapasitas || 2;
        fHarga.value = data.harga_per_malam !== undefined ? data.harga_per_malam : '';
        optLama.hidden = (data.status !== 'Terisi');
        fStatus.value = data.status || 'Tersedia';
        isiOffline(null);
        fCatatan.value = data.catatan_perbaikan || '';
        fDeskripsi.value = data.deskripsi || '';

        boxBaru.innerHTML = '';
        renderFotoLama(data.fotos || []);
        if (!(data.fotos || []).length) { tambahBarisFoto(); }
        tampilStatus();
        tampilFasilitas();
    }

    modalEl.addEventListener('show.bs.modal', function (ev) {
        var btn = ev.relatedTarget;
        if (!btn) {
            try { renderFotoLama(JSON.parse(modalEl.getAttribute('data-fotos') || '[]')); } catch (e) { renderFotoLama([]); }
            tampilStatus(); tampilFasilitas(); return;
        }
        var mode = btn.getAttribute('data-mode') === 'ubah' ? 'ubah' : 'tambah';
        var data = {};
        if (mode === 'ubah') {
            try { data = JSON.parse(btn.getAttribute('data-room') || '{}'); } catch (e) { data = {}; }
        }
        isi(data, mode);
    });

    modalEl.addEventListener('shown.bs.modal', function () {
        if (!fNomor.value) { fNomor.focus(); } else { fTipe.focus(); }
    });

    fStatus.addEventListener('change', tampilStatus);
    fDeskripsi.addEventListener('input', tampilFasilitas);

    tampilStatus();
    tampilFasilitas();

    if (modalEl.getAttribute('data-open') === '1' && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
});