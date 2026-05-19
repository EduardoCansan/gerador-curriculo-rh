<div class="page-header">
    <h1>Novo Currículo</h1>
    <a href="<?= APP_URL ?>/curriculos" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/curriculos" enctype="multipart/form-data" id="formCurriculo">

            <!-- Nome do candidato -->
            <div class="form-group">
                <label for="nome_candidato">Nome do Candidato *</label>
                <input
                    type="text"
                    id="nome_candidato"
                    name="nome_candidato"
                    class="form-control <?= isset($errors['nome_candidato']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($old['nome_candidato'] ?? '') ?>"
                    placeholder="Ex: João da Silva"
                    required
                >
                <?php if (isset($errors['nome_candidato'])): ?>
                    <span class="field-error"><?= $errors['nome_candidato'][0] ?></span>
                <?php endif; ?>
            </div>

            <!-- Tipo de entrada -->
            <div class="form-group">
                <label>Forma de Inserção do Currículo *</label>
                <div class="radio-tabs">
                    <label class="radio-tab">
                        <input type="radio" name="tipo_entrada" value="texto" checked id="tipoTexto">
                        <span>📝 Colar Texto</span>
                    </label>
                    <label class="radio-tab">
                        <input type="radio" name="tipo_entrada" value="arquivo" id="tipoArquivo">
                        <span>📎 Upload PDF/DOCX</span>
                    </label>
                </div>
            </div>

            <!-- Seção de texto -->
            <div id="secaoTexto" class="form-group">
                <label for="texto_curriculo">Texto do Currículo *</label>
                <textarea
                    id="texto_curriculo"
                    name="texto_curriculo"
                    class="form-control <?= isset($errors['texto_curriculo']) ? 'is-invalid' : '' ?>"
                    rows="14"
                    placeholder="Cole aqui o currículo completo..."
                ><?= htmlspecialchars($old['texto_curriculo'] ?? '') ?></textarea>
                <?php if (isset($errors['texto_curriculo'])): ?>
                    <span class="field-error"><?= $errors['texto_curriculo'][0] ?></span>
                <?php endif; ?>
            </div>

            <!-- Seção de upload -->
            <div id="secaoArquivo" class="form-group" style="display:none;">
                <label for="arquivo">Arquivo (PDF ou DOCX, máx. 5MB)</label>
                <div class="file-drop" id="fileDrop">
                    <input type="file" id="arquivo" name="arquivo" accept=".pdf,.docx" class="file-input">
                    <div class="file-drop-content">
                        <span class="file-drop-icon">📂</span>
                        <p>Clique ou arraste o arquivo aqui</p>
                        <small>PDF ou DOCX até 5MB</small>
                    </div>
                    <div class="file-selected" id="fileSelected" style="display:none;"></div>
                </div>
                <?php if (isset($errors['arquivo'])): ?>
                    <span class="field-error"><?= $errors['arquivo'][0] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    💾 Salvar Currículo
                </button>
                <a href="<?= APP_URL ?>/curriculos" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>
</div>
