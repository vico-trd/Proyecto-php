<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="alert alert-warning" role="alert">
            Panel de administracion: aqui puedes crear usuarios y definir su rol.
        </div>

        <h2>Crear usuario</h2>

        <form action="/Proyecto-php/public/admin/users/create" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <small class="text-muted">Introduce un email con formato valido (ejemplo@dominio.com).</small>
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
                <small class="text-muted">Minimo 8 caracteres, con letras, numeros y al menos un simbolo.</small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)"><i class="bi bi-eye"></i></button>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rol</label>
                <select name="role" id="role" class="form-select">
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Crear usuario</button>
        </form>
    </div>
</div>
