<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <h2>Recuperar contraseña</h2>
        <p class="text-muted">Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>forgot-password" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
        </form>

        <p class="mt-3 text-center"><a href="<?= BASE_URL ?>login">Volver al inicio de sesión</a></p>
    </div>
</div>
