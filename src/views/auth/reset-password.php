<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <h2>Nueva contraseña</h2>
        <p class="text-muted">Introduce y confirma tu nueva contraseña.</p>

        <form action="<?= BASE_URL ?>reset-password" method="POST">
            <input type="hidden" name="token" value="<?= $token ?? '' ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Nueva contraseña</label>
                <div class="input-group">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                        required
                    >
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
                <small class="text-muted">Mínimo 8 caracteres.</small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                        required
                    >
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar contraseña</button>
        </form>
    </div>
</div>
