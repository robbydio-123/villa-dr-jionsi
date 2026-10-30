<section class="container mt-4 mb-5" style="max-width: 700px;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <a href="hasil_status.php?kode=<?= rawurlencode($booking['kode_booking']) ?>" class="small text-muted">← Kembali ke status reservasi</a>
            <h2 class="fw-bolder mt-3">Ajukan Refund</h2>
            <p class="text-muted">Booking <strong><?= h($booking['kode_booking']) ?></strong> · Kamar <?= h($booking['nomor_kamar']) ?></p>
            <div class="alert alert-info">
                Isi alasan pengajuan. Pengembalian dana diproses manual oleh pengelola.
                Batas pengajuan: <strong><?= h($refundDeadline->format('d M Y H:i')) ?></strong>.
            </div>
            <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
            <form method="POST" action="refund.php?kode=<?= rawurlencode($booking['kode_booking']) ?>">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="kode" value="<?= h($booking['kode_booking']) ?>">
                <label for="alasan" class="form-label fw-semibold">Alasan refund</label>
                <textarea id="alasan" name="alasan" class="form-control mb-3" rows="5" minlength="5" maxlength="2000" required><?= h($_POST['alasan'] ?? '') ?></textarea>
                <button class="btn btn-danger w-100 fw-bold py-2" type="submit">Kirim Pengajuan Refund</button>
            </form>
        </div>
    </div>
</section>
