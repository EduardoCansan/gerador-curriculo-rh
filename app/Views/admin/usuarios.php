<div class="page-header">
    <h1>Gerenciar Usuários</h1>
    <a href="<?= APP_URL ?>/admin/usuarios/novo" class="btn btn-primary">➕ Novo Usuário</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Cadastrado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($u['name']) ?>
                        <?php if ((int)$u['id'] === Auth::id()): ?>
                            <span class="badge badge-blue" title="Você">eu</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['perfil'] === 'admin' ? 'badge-admin' : 'badge-blue' ?>">
                            <?= $u['perfil'] === 'admin' ? '👑 Admin' : '🔎 Recrutador' ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= (int)$u['ativo'] ? 'badge-processado' : 'badge-erro' ?>">
                            <?= (int)$u['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td class="actions">
                        <a href="<?= APP_URL ?>/admin/usuarios/<?= $u['id'] ?>/editar" class="btn btn-sm btn-secondary">✏️ Editar</a>

                        <?php if ((int)$u['id'] !== Auth::id()): ?>
                            <form method="POST" action="<?= APP_URL ?>/admin/usuarios/<?= $u['id'] ?>/toggle" style="display:inline;">
                                <button type="submit" class="btn btn-sm <?= (int)$u['ativo'] ? 'btn-warning' : 'btn-success' ?>">
                                    <?= (int)$u['ativo'] ? '⏸ Desativar' : '▶ Ativar' ?>
                                </button>
                            </form>

                            <form method="POST" action="<?= APP_URL ?>/admin/usuarios/<?= $u['id'] ?>/delete"
                                  style="display:inline;"
                                  onsubmit="return confirm('Remover o usuário <?= htmlspecialchars(addslashes($u['name'])) ?>?')">
                                <button type="submit" class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
