document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('rfApp');
    if (!app) return;

    const STATUS = JSON.parse(app.dataset.status);
    const ACTION = JSON.parse(app.dataset.action);

    // Pindahkan modal & toast ke <body> agar backdrop tidak tertutup layout admin
    document.querySelectorAll('.rf-modal').forEach(m => document.body.appendChild(m));
    const toastEl = document.getElementById('rfToast');
    document.body.appendChild(toastEl.parentElement);

    const toast = (msg) => {
        document.getElementById('rfToastBody').textContent = msg;
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3200 }).show();
    };
    const nowStr = () =>
        new Date().toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
            .replace(',', '') + ' WIB';

    const badgeHtml = (s) =>
        `<span class="rf-badge ${STATUS[s].cls}"><i class="bi ${STATUS[s].icon}"></i> ${STATUS[s].label}</span>`;

    function recount() {
        document.getElementById('statMenunggu').textContent =
            document.querySelectorAll('#rfBody tr[data-status="menunggu"]').length;
    }

    // ---- Ubah status (tabel + modal + riwayat) ----
    function setStatus(modal, status, logText, logType) {
        const id  = modal.dataset.rf;
        const row = document.querySelector(`[data-rf-row="${id}"]`);
        modal.dataset.status = status;

        modal.querySelector('[data-rf-modal-badge]').outerHTML =
            badgeHtml(status).replace('<span class="rf-badge', '<span data-rf-modal-badge class="rf-badge me-3');
        modal.querySelectorAll('.rf-pane').forEach(p => p.classList.toggle('show', p.dataset.pane === status));

        if (row) {
            row.dataset.status = status;
            row.querySelector('[data-cell="status"]').innerHTML = badgeHtml(status);
            row.querySelector('[data-cell="aksi"]').innerHTML =
                `<button type="button" class="${ACTION[status].cls}" style="font-size:0.7rem;" ` +
                `data-bs-toggle="modal" data-bs-target="#rfModal-${id}">${ACTION[status].html}</button>`;
            const idEl = row.querySelector('td strong');
            idEl.classList.toggle('text-danger', status === 'menunggu');
            idEl.classList.toggle('text-dark', status !== 'menunggu');
        }
        modal.querySelector('[data-rf-log]')
            .insertAdjacentHTML('afterbegin', `<li class="${logType}">${logText}<small>${nowStr()}</small></li>`);
        recount();
    }

    // ---- Aksi di dalam modal ----
    document.querySelectorAll('.rf-modal').forEach(modal => {
        const stepPilih = modal.querySelector('[data-step="pilih"]');
        const stepTolak = modal.querySelector('[data-step="tolak"]');

        modal.querySelector('[data-rf-open-reject]')?.addEventListener('click', () => {
            stepPilih.classList.add('d-none');
            stepTolak.classList.remove('d-none');
        });
        modal.querySelector('[data-rf-close-reject]')?.addEventListener('click', () => {
            stepTolak.classList.add('d-none');
            stepPilih.classList.remove('d-none');
        });

        modal.querySelectorAll('[data-rf-action]').forEach(btn => btn.addEventListener('click', () => {
            const act = btn.dataset.rfAction;

            if (act === 'setuju') {
                const note = modal.querySelector('[data-note]').value.trim();
                setStatus(modal, 'disetujui', 'Disetujui oleh Pengelola Vila.' + (note ? ' Catatan: ' + note : ''), 't-ok');
                toast('Refund disetujui. Lanjutkan dengan transfer dana ke tamu.');

            } else if (act === 'tolak') {
                const sel = modal.querySelector('[data-reject-reason]');
                if (!sel.value) { sel.classList.add('is-invalid'); sel.focus(); return; }
                sel.classList.remove('is-invalid');
                const extra = modal.querySelector('[data-reject-note]').value.trim();
                const text  = sel.value + (extra ? ' — ' + extra : '');
                modal.querySelector('[data-reject-view]').textContent = text;
                setStatus(modal, 'ditolak', 'Ditolak oleh Pengelola Vila. Alasan: ' + text, 't-no');
                toast('Pengajuan refund ditolak.');

            } else if (act === 'transfer') {
                const ref = modal.querySelector('[data-ref]');
                if (!ref.value.trim()) { ref.classList.add('is-invalid'); ref.focus(); return; }
                ref.classList.remove('is-invalid');
                modal.querySelector('[data-ref-view]').textContent = ref.value.trim();
                setStatus(modal, 'selesai', 'Dana ditransfer. Ref: ' + ref.value.trim() + '.', 't-ok');
                toast('Refund ditandai selesai ditransfer.');
            }
        }));
    });

    // ---- Salin nomor rekening ----
    document.querySelectorAll('[data-copy]').forEach(b => b.addEventListener('click', () => {
        navigator.clipboard?.writeText(b.dataset.copy).then(() => toast('Nomor rekening disalin.'));
    }));

    // ---- Pencarian & filter tabel ----
    const q = document.getElementById('rfSearch');
    const f = document.getElementById('rfFilter');
    function applyFilter() {
        const term = q.value.trim().toLowerCase();
        let shown = 0;
        document.querySelectorAll('#rfBody tr[data-rf-row]').forEach(tr => {
            const ok = (!f.value || tr.dataset.status === f.value) && (!term || tr.dataset.search.includes(term));
            tr.classList.toggle('d-none', !ok);
            if (ok) shown++;
        });
        document.getElementById('rfEmpty').classList.toggle('d-none', shown > 0);
    }
    q.addEventListener('input', applyFilter);
    f.addEventListener('change', applyFilter);
    document.getElementById('rfReset').addEventListener('click', () => { q.value = ''; f.value = ''; applyFilter(); });
});