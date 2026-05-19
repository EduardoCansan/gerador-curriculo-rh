<div class="page-header">
    <h1>Editar Currículo</h1>
    <a href="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>">

            <div class="form-group">
                <label for="nome_candidato">Nome do Candidato *</label>
                <input
                    type="text"
                    id="nome_candidato"
                    name="nome_candidato"
                    class="form-control <?= isset($errors['nome_candidato']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($curriculo['nome_candidato'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['nome_candidato'])): ?>
                    <span class="field-error"><?= $errors['nome_candidato'][0] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="texto_curriculo">Texto do Currículo *</label>
                <textarea
                    id="texto_curriculo"
                    name="texto_curriculo"
                    class="form-control <?= isset($errors['texto_curriculo']) ? 'is-invalid' : '' ?>"
                    rows="16"
                ><?= htmlspecialchars($curriculo['texto_original'] ?? '') ?></textarea>
                <?php if (isset($errors['texto_curriculo'])): ?>
                    <span class="field-error"><?= $errors['texto_curriculo'][0] ?></span>
                <?php endif; ?>
                <span class="field-hint">⚠ Salvar irá resetar o status para "Pendente" — reprocesse com IA após editar.</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
                <a href="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
