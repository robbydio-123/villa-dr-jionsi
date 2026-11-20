(function () {
    'use strict';

    var pollInterval = 20000;
    var reservationsUrl = 'admin_reservasi.php?filter=menunggu';
    var refundUrl = 'admin_refund.php?status=Diajukan';
    var storageKeyPayment = 'adminLatestPendingPaymentId';
    var storageKeyRefund = 'adminLatestRefundId';
    var badge = document.getElementById('adminPendingBadge');
    var refundBadge = document.getElementById('adminRefundBadge');
    var dashboardCount = document.getElementById('dashboardPendingCount');
    var dashboardRefundCount = document.getElementById('dashboardRefundCount');
    var permissionButton = document.getElementById('adminEnableNotifications');
    var last = { payment: null, refund: null };
    var initialized = false;

    var TEXT = {
        payment: {
            title: 'Pembayaran menunggu konfirmasi',
            body: 'Ada bukti pembayaran baru yang perlu diperiksa.',
            link: 'Lihat reservasi',
            url: reservationsUrl
        },
        refund: {
            title: 'Pengajuan refund baru',
            body: 'Ada pengajuan refund dari tamu yang perlu diputuskan.',
            link: 'Tinjau refund',
            url: refundUrl
        }
    };

    function setBadge(el, count, label) {
        if (!el) return;
        el.textContent = count > 99 ? '99+' : String(count);
        el.classList.toggle('d-none', count < 1);
        el.setAttribute('aria-label', count + ' ' + label);
    }

    function updateCounts(paymentCount, refundCount) {
        setBadge(badge, paymentCount, 'pembayaran menunggu konfirmasi');
        setBadge(refundBadge, refundCount, 'pengajuan refund menunggu tinjauan');
        if (dashboardCount) dashboardCount.textContent = String(paymentCount);
        if (dashboardRefundCount) dashboardRefundCount.textContent = String(refundCount);
    }

    function showToast(kind) {
        if (!window.bootstrap || !window.bootstrap.Toast) return;
        var t = TEXT[kind];

        var container = document.getElementById('adminNotificationToasts');
        if (!container) {
            container = document.createElement('div');
            container.id = 'adminNotificationToasts';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.setAttribute('aria-atomic', 'true');

        var header = document.createElement('div');
        header.className = 'toast-header';
        var title = document.createElement('strong');
        title.className = 'me-auto';
        title.textContent = t.title;
        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'toast');
        close.setAttribute('aria-label', 'Tutup');
        header.append(title, close);

        var body = document.createElement('div');
        body.className = 'toast-body';
        var message = document.createElement('p');
        message.className = 'mb-2';
        message.textContent = t.body;
        var link = document.createElement('a');
        link.href = t.url;
        link.className = 'btn btn-sm btn-brown';
        link.textContent = t.link;
        body.append(message, link);
        toast.append(header, body);
        container.appendChild(toast);

        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
        new window.bootstrap.Toast(toast, { delay: 10000 }).show();
    }

    function showBrowserNotification(kind) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        var t = TEXT[kind];

        var notification = new Notification(t.title, {
            body: t.body,
            icon: 'assets/img/logo%20dr-jionsi.jpeg'
        });
        notification.onclick = function () {
            window.focus();
            window.location.href = t.url;
        };
    }

    function notify(kind) {
        showToast(kind);
        showBrowserNotification(kind);
    }

    function readStored(key) {
        try {
            var v = sessionStorage.getItem(key);
            return v === null ? null : (Number(v) || 0);
        } catch (error) {
            return null;
        }
    }

    function writeStored(key, value) {
        try {
            sessionStorage.setItem(key, String(value));
        } catch (error) {
            // Polling tetap bekerja bila penyimpanan sesi tidak tersedia.
        }
    }

    function track(kind, key, latest) {
        if (!initialized) {
            var stored = readStored(key);
            if (stored === null) {
                last[kind] = latest;
            } else {
                last[kind] = stored;
                if (latest > stored) notify(kind);
            }
        } else if (latest > last[kind]) {
            notify(kind);
        }
        last[kind] = Math.max(last[kind] || 0, latest);
        writeStored(key, last[kind]);
    }

    function pollNotifications() {
        fetch('admin_notifikasi.php', {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'Accept': 'application/json' }
        })
            .then(function (response) {
                if (!response.ok) throw new Error('Notifikasi admin gagal dimuat.');
                return response.json();
            })
            .then(function (data) {
                updateCounts(Number(data.pending_count) || 0, Number(data.refund_count) || 0);
                track('payment', storageKeyPayment, Number(data.latest_payment_id) || 0);
                track('refund', storageKeyRefund, Number(data.latest_refund_id) || 0);
                initialized = true;
            })
            .catch(function () {
                // Kegagalan sementara tidak menghentikan polling berikutnya.
            });
    }

    if (permissionButton && 'Notification' in window && Notification.permission === 'default') {
        permissionButton.classList.remove('d-none');
        permissionButton.addEventListener('click', function () {
            Notification.requestPermission().then(function (permission) {
                permissionButton.classList.add('d-none');
                if (permission === 'granted') {
                    showToast('payment');
                }
            });
        });
    }

    pollNotifications();
    window.setInterval(pollNotifications, pollInterval);
})();