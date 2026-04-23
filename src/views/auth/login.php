<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2>Iniciar sesión</h2>

        <form action="/Proyecto-php/public/login" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)"><i class="bi bi-eye"></i></button>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <p class="mt-3 text-center">¿No tienes cuenta? <a href="/Proyecto-php/public/register">Regístrate</a></p>
    </div>
</div>