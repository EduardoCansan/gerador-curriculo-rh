<div class="page-header">
    <h1>Dashboard</h1>
    <p class="page-subtitle">Bem-vindo, <?= htmlspecialchars(Auth::user()['name']) ?>!</p>
</div>

<!-- CARDS DE ESTATÍSTICAS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📁</div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['total'] ?></span>
            <span class="stat-label">Total de Currículos</span>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['processados'] ?></span>
            <span class="stat-label">Processados pela IA</span>
        </div>
    </div>
    <div class="stat-card stat-card--warning">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['pendentes'] ?></span>
            <span class="stat-label">Pendentes</span>
        </div>
    </div>
    <div class="stat-card stat-card--error">
        <div class="stat-icon">❌</div>
        <div class="stat-info">
            <span class="stat-value"><?= $stats['erro'] ?></span>
            <span class="stat-label">Com Erro</span>
        </div>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <div class="stat-card stat-card--blue">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <span class="stat-value"><?= $totalUsers ?></span>
            <span class="stat-label">Usuários Cadastrados</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- AÇÕES RÁPIDAS -->
<div class="quick-actions">
    <a href="<?= APP_URL ?>/curriculos/novo" class="btn btn-primary">
        ➕ Novo Currículo
    </a>
    <a href="<?= APP_URL ?>/curriculos" class="btn btn-secondary">
        📁 Ver Todos
    </a>
</div>

<!-- ÚLTIMOS CURRÍCULOS -->
<div class="card mt-24">
    <div class="card-header">
        <h2>Últimos Currículos</h2>
    </div>
    <div class="card-body">
        <?php if (empty($ultimos)): ?>
            <p class="text-muted text-center">Nenhum currículo cadastrado ainda.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Recrutador</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimos as $cv): ?>
                    <tr>
                        <td><?= htmlspecialchars($cv['nome_candidato']) ?></td>
                        <td><?= htmlspecialchars($cv['recrutador_nome'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-<?= $cv['status'] ?>">
                                <?= match($cv['status']) {
                                    'processado' => '✅ Processado',
                                    'pendente'   => '⏳ Pendente',
                                    'erro'       => '❌ Erro',
                                    default      => $cv['status']
                                } ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($cv['created_at'])) ?></td>
                        <td>
                            <a href="<?= APP_URL ?>/curriculos/<?= $cv['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
