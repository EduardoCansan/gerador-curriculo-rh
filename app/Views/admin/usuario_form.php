<div class="page-header">
    <h1><?= $pageTitle ?></h1>
    <a href="<?= APP_URL ?>/admin/usuarios" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card" style="max-width: 560px;">
    <div class="card-body">
        <form method="POST" action="<?= $isNew ? APP_URL . '/admin/usuarios' : APP_URL . '/admin/usuarios/' . ($usuario['id'] ?? '') ?>">

            <div class="form-group">
                <label for="name">Nome Completo *</label>
                <input type="text" id="name" name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($usuario['name'] ?? '') ?>"
                    required>
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= $errors['name'][0] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email"
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                    required>
                <?php if (isset($errors['email'])): ?>
                    <span class="field-error"><?= $errors['email'][0] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">
                    Senha <?= $isNew ? '*' : '(deixe em branco para não alterar)' ?>
                </label>
                <input type="password" id="password" name="password"
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    placeholder="Mínimo 8 caracteres"
                    <?= $isNew ? 'required' : '' ?>>
                <?php if (isset($errors['password'])): ?>
                    <span class="field-error"><?= $errors['password'][0] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="perfil">Perfil *</label>
                <select id="perfil" name="perfil" class="form-control <?= isset($errors['perfil']) ? 'is-invalid' : '' ?>">
                    <option value="recrutador" <?= ($usuario['perfil'] ?? '') === 'recrutador' ? 'selected' : '' ?>>🔎 Recrutador</option>
                    <option value="admin" <?= ($usuario['perfil'] ?? '') === 'admin' ? 'selected' : '' ?>>👑 Administrador</option>
                </select>
                <?php if (isset($errors['perfil'])): ?>
                    <span class="field-error"><?= $errors['perfil'][0] ?></span>
                <?php endif; ?>
            </div>

            <?php if (!$isNew && isset($usuario['id']) && (int)$usuario['id'] !== Auth::id()): ?>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="ativo" value="1" <?= (int)($usuario['ativo'] ?? 1) ? 'checked' : '' ?>>
                    Usuário ativo
                </label>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $isNew ? '✅ Criar Usuário' : '💾 Salvar Alterações' ?>
                </button>
                <a href="<?= APP_URL ?>/admin/usuarios" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
