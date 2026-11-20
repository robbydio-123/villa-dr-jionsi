(function () {
    const tabel = document.getElementById('tabelReservasi');
    if (!tabel) return;

    const hapusEl = document.getElementById('modalHapusReservasi');
    if (hapusEl) {
        hapusEl.addEventListener('show.bs.modal', (ev) => {
            const btn = ev.relatedTarget;
            if (!btn) return;
            document.getElementById('hapusReservasiId').value = btn.getAttribute('data-id') || '';
            document.getElementById('hapusReservasiKode').textContent = btn.getAttribute('data-kode') || '';
            document.getElementById('hapusReservasiNama').textContent = btn.getAttribute('data-nama') || '';
        });
    }
         
    // Modal Periksa Bukti: isi data dari tombol yang diklik
    const buktiEl = document.getElementById('modalBukti');
    if (buktiEl) {
        const isi = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v || ''; };
        buktiEl.addEventListener('show.bs.modal', (ev) => {
            const b = ev.relatedTarget;
            if (!b) return;
            const d = b.dataset;
            document.getElementById('bReservasi').value = d.reservasi || '';
            document.getElementById('bPembayaran').value = d.pembayaran || '';
            isi('bKode', d.kode); isi('bNama', d.nama); isi('bKamar', d.kamar);
            isi('bMenginap', d.menginap); isi('bTagihan', d.tagihan); isi('bNominal', d.nominal);
            const img = document.getElementById('bImg');
            const gagal = document.getElementById('bGagal');
            gagal.classList.add('d-none');
            img.classList.remove('d-none');
            img.onerror = () => { img.classList.add('d-none'); gagal.classList.remove('d-none'); };
            img.src = d.bukti || '';
            document.getElementById('bLink').href = d.bukti || '#';
        });
    }

    const tbody   = tabel.tBodies[0];
    const headers = Array.from(tabel.tHead.rows[0].cells);

    // Simpan urutan asli tiap baris (dipakai oleh tombol Reset Urutan)
    Array.from(tbody.rows).forEach((tr, i) => { tr.dataset.urut = i; });

    // Filter status: klik tab -> tampilkan baris sesuai status
    const tabs        = Array.from(document.querySelectorAll('#filterStatus .admin-filter-tab'));
    const pesanKosong = document.getElementById('pesanKosong');
    let filterAktif   = 'semua';
    // Dari dashboard: admin_reservasi.php?filter=keep|menunggu|dibayar|...
    const filterUrl = new URLSearchParams(window.location.search).get('filter');
    if (filterUrl && tabs.some(tab => tab.dataset.filter === filterUrl)) filterAktif = filterUrl;

    function hitungAngkaTab() {
        const semua = Array.from(tbody.rows);
        tabs.forEach(tab => {
            const f = tab.dataset.filter;
            const jumlah = f === 'semua' ? semua.length : semua.filter(tr => tr.dataset.status === f).length;
            tab.querySelector('.badge-count').textContent = jumlah;
        });
    }

    function terapkanFilter() {
        let tampil = 0;
        Array.from(tbody.rows).forEach(tr => {
            const cocok = filterAktif === 'semua' || tr.dataset.status === filterAktif;
            tr.style.display = cocok ? '' : 'none';
            if (cocok) tampil++;
        });
        tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.filter === filterAktif));
        pesanKosong.style.display = tampil === 0 ? '' : 'none';
    }

    tabs.forEach(tab => tab.addEventListener('click', (e) => {
        e.preventDefault();
        filterAktif = tab.dataset.filter;
        terapkanFilter();
    }));

    hitungAngkaTab();
    terapkanFilter();

    let kolomAktif = -1;
    let arah = 1; // 1 = naik (A-Z), -1 = turun (Z-A)

    function nilaiSel(tr, kolom, tipe) {
        const td = tr.cells[kolom];
        const v  = (td.dataset.sort !== undefined ? td.dataset.sort : td.textContent).replace(/\s+/g, ' ').trim();
        return tipe === 'num' ? (Number(v) || 0) : v.toLowerCase();
    }

    function tandaiHeader() {
        headers.forEach((th, i) => {
            th.classList.remove('sort-asc', 'sort-desc');
            const ico = th.querySelector('.sort-ico');
            if (ico) ico.className = 'bi bi-arrow-down-up sort-ico';
            if (i === kolomAktif && ico) {
                th.classList.add(arah === 1 ? 'sort-asc' : 'sort-desc');
                ico.className = 'bi ' + (arah === 1 ? 'bi-caret-up-fill' : 'bi-caret-down-fill') + ' sort-ico';
            }
        });
    }

    // Klik judul kolom -> urutkan (klik lagi = balik arah)
    headers.forEach((th, kolom) => {
        if (!th.classList.contains('sortable')) return;
        th.addEventListener('click', () => {
            arah = (kolomAktif === kolom) ? -arah : 1;
            kolomAktif = kolom;
            const tipe = th.dataset.type || 'text';
            Array.from(tbody.rows)
                .sort((a, b) => {
                    const x = nilaiSel(a, kolom, tipe), y = nilaiSel(b, kolom, tipe);
                    return (x < y ? -1 : x > y ? 1 : 0) * arah;
                })
                .forEach(tr => tbody.appendChild(tr));
            tandaiHeader();
        });
    });

    // Tombol Reset Urutan -> kembali ke urutan awal
    document.getElementById('btnResetUrutan').addEventListener('click', () => {
        Array.from(tbody.rows)
            .sort((a, b) => a.dataset.urut - b.dataset.urut)
            .forEach(tr => tbody.appendChild(tr));
        kolomAktif = -1;
        arah = 1;
        tandaiHeader();
    });

    // Tombol Ekspor Laporan -> unduh Excel (.xlsx), mengikuti urutan & filter yang sedang tampil
    document.getElementById('btnEksporExcel').addEventListener('click', () => {
        if (typeof XLSX === 'undefined') {
            alert('Library Excel gagal dimuat. Periksa koneksi internet, lalu coba lagi.');
            return;
        }

        const teks = (el) => el ? el.textContent.replace(/\s+/g, ' ').trim() : '';
        const tgl  = (iso) => iso.split('-').reverse().join('-'); // 2026-10-26 -> 26-10-2026

        const data = [[
            'Kode Booking', 'Metode Pembayaran', 'Pemesan', 'Jumlah Tamu', 'WhatsApp',
            'Unit Kamar', 'Lokasi Unit', 'Check In', 'Check Out', 'Durasi (Malam)',
            'Total Tagihan (Rp)', 'Keep / Sisa Waktu', 'Status Booking'
        ]];

        // Hanya baris yang sedang tampil (sesuai tab filter) yang diekspor
        Array.from(tbody.rows).filter(tr => tr.style.display !== 'none').forEach(tr => {
            const c = tr.cells;
            data.push([
                teks(c[0].querySelector('strong')), teks(c[0].querySelector('small')),
                teks(c[1].querySelector('strong')), teks(c[1].querySelector('small')),
                teks(c[2]),
                teks(c[3].querySelector('strong')), teks(c[3].querySelector('small')),
                tgl(c[4].dataset.sort), tgl(c[5].dataset.sort),
                Number(tr.dataset.malam),
                Number(c[6].dataset.sort),
                teks(c[7]),
                teks(c[8])
            ]);
        });

        const ws = XLSX.utils.aoa_to_sheet(data);
        ws['!cols'] = [
            {wch: 20}, {wch: 20}, {wch: 20}, {wch: 12}, {wch: 20}, {wch: 24},
            {wch: 28}, {wch: 12}, {wch: 12}, {wch: 14}, {wch: 18}, {wch: 18}, {wch: 22}
        ];
        for (let r = 1; r < data.length; r++) {
            const sel = ws[XLSX.utils.encode_cell({ r: r, c: 10 })];
            if (sel) sel.z = '#,##0'; // format ribuan
        }

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Reservasi');

        const d = new Date();
        const p = (n) => String(n).padStart(2, '0');
        XLSX.writeFile(wb, 'laporan-reservasi-' + d.getFullYear() + p(d.getMonth() + 1) + p(d.getDate()) + '.xlsx');
    });
})();