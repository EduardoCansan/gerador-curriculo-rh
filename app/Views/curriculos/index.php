<div class="page-header">
    <h1>Currículos</h1>
    <a href="<?= APP_URL ?>/curriculos/novo" class="btn btn-primary">➕ Novo Currículo</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($curriculos)): ?>
            <div class="empty-state">
                <span class="empty-icon">📂</span>
                <p>Nenhum currículo cadastrado ainda.</p>
                <a href="<?= APP_URL ?>/curriculos/novo" class="btn btn-primary">Adicionar primeiro currículo</a>
            </div>
        <?php else: ?>
            <!-- Busca rápida client-side -->
            <input type="text" id="searchInput" class="form-control mb-16" placeholder="🔍 Filtrar por candidato ou recrutador...">

            <table class="table" id="curriculosTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Candidato</th>
                        <th>Tipo Entrada</th>
                        <th>Recrutador</th>
                        <th>Status</th>
                        <th>Cadastrado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($curriculos as $cv): ?>
                    <tr>
                        <td><?= $cv['id'] ?></td>
                        <td><strong><?= htmlspecialchars($cv['nome_candidato']) ?></strong></td>
                        <td><?= $cv['tipo_entrada'] === 'arquivo' ? '📎 Arquivo' : '📝 Texto' ?></td>
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
                        <td><?= date('d/m/Y', strtotime($cv['created_at'])) ?></td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/curriculos/<?= $cv['id'] ?>" class="btn btn-sm btn-secondary">👁 Ver</a>
                            <?php if ($cv['status'] === 'processado' && $cv['pdf_padronizado']): ?>
                                <a href="<?= APP_URL ?>/curriculos/<?= $cv['id'] ?>/download" class="btn btn-sm btn-success">⬇ PDF</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
