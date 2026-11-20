</main> <!-- Penutup admin-main -->
</div> <!-- Penutup admin-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin_notifications.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/admin_notifications.js') ?>"></script>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }

    // Tutup sidebar jika area gelap diklik
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            adminSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
</script>
</body>
</html>