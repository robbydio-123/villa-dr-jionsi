<section class="container d-flex justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="card shadow-sm border-0 w-100" style="max-width: 400px; border-radius: 12px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <!-- Ikon Gembok -->
                <div class="d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px; background-color: #F6F4EE; border-radius: 50%;">
                    <i class="bi bi-shield-lock-fill fs-3" style="color: var(--drj-primary);"></i>
                </div>
                <h4 class="fw-bolder mb-1" style="color: #333333;">Portal Pengelola</h4>
                <p class="text-muted small mb-0">Masuk untuk mengelola reservasi & kamar</p>
            </div>

            <form method="post" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger small py-2"><?= h($error) ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nomor WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-muted"></i></span>
                        <input type="text" name="nomor_whatsapp" class="form-control bg-light border-start-0 shadow-none" placeholder="081233449081" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0 shadow-none" placeholder="••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-4 shadow-sm">Masuk Dashboard</button>
                
                <div class="text-center">
                    <a href="index.php" class="text-decoration-none small text-muted hover-primary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Kamar
                    </a>
                </div>
            </form>

        </div>
    </div>
</section>