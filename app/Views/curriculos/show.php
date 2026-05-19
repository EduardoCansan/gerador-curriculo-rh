<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($curriculo['nome_candidato']) ?></h1>
        <span class="badge badge-<?= $curriculo['status'] ?> badge-lg">
            <?= match($curriculo['status']) {
                'processado' => '✅ Processado pela IA',
                'pendente'   => '⏳ Aguardando processamento',
                'erro'       => '❌ Erro no processamento',
                default      => $curriculo['status']
            } ?>
        </span>
    </div>
    <div class="page-header-actions">
        <a href="<?= APP_URL ?>/curriculos" class="btn btn-secondary">← Voltar</a>
        <a href="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>/editar" class="btn btn-secondary">✏️ Editar</a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="two-columns">

    <!-- COLUNA ESQUERDA: Info + IA -->
    <div class="col-left">

        <!-- INFO BÁSICA -->
        <div class="card mb-16">
            <div class="card-header"><h3>Informações</h3></div>
            <div class="card-body">
                <dl class="info-list">
                    <dt>Recrutador</dt>
                    <dd><?= htmlspecialchars($curriculo['recrutador_nome'] ?? '-') ?></dd>
                    <dt>Tipo de entrada</dt>
                    <dd><?= $curriculo['tipo_entrada'] === 'arquivo' ? '📎 Arquivo' : '📝 Texto manual' ?></dd>
                    <dt>Cadastrado em</dt>
                    <dd><?= date('d/m/Y \à\s H:i', strtotime($curriculo['created_at'])) ?></dd>
                    <?php if ($curriculo['processado_em']): ?>
                    <dt>Processado em</dt>
                    <dd><?= date('d/m/Y \à\s H:i', strtotime($curriculo['processado_em'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- AÇÕES IA -->
        <div class="card mb-16">
            <div class="card-header"><h3>Processamento com IA</h3></div>
            <div class="card-body">
                <?php if ($curriculo['status'] === 'pendente' || $curriculo['status'] === 'erro'): ?>
                    <?php if ($curriculo['status'] === 'erro'): ?>
                        <div class="alert alert-error mb-12">
                            <strong>Último erro:</strong> <?= htmlspecialchars($curriculo['erro_mensagem'] ?? '') ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>/processar" id="formProcessar">
                        <button type="submit" class="btn btn-primary btn-block" id="btnProcessar">
                            🤖 Processar com IA (Claude)
                        </button>
                    </form>
                    <p class="text-muted text-sm mt-8">Isso extrai dados e gera um PDF padronizado automaticamente.</p>
                <?php elseif ($curriculo['status'] === 'processado'): ?>
                    <div class="success-box">
                        <span class="success-icon">✅</span>
                        <p>Currículo padronizado com sucesso!</p>
                    </div>
                    <a href="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>/download" class="btn btn-success btn-block mt-12">
                        ⬇ Baixar PDF Padronizado
                    </a>
                    <form method="POST" action="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>/processar" class="mt-8">
                        <button type="submit" class="btn btn-secondary btn-block btn-sm">
                            🔄 Reprocessar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- DELETAR -->
        <div class="card card-danger">
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/curriculos/<?= $curriculo['id'] ?>/delete"
                      onsubmit="return confirm('Tem certeza que deseja remover este currículo? Esta ação não pode ser desfeita.')">
                    <button type="submit" class="btn btn-danger btn-block">🗑 Remover Currículo</button>
                </form>
            </div>
        </div>

    </div>

    <!-- COLUNA DIREITA: Dados extraídos OU texto original -->
    <div class="col-right">

        <?php if ($dadosIA): ?>
        <!-- DADOS EXTRAÍDOS PELA IA -->
        <div class="card">
            <div class="card-header">
                <h3>Dados Extraídos pela IA</h3>
            </div>
            <div class="card-body">

                <div class="cv-section">
                    <h4>👤 Dados Pessoais</h4>
                    <dl class="info-list">
                        <dt>Nome</dt><dd><?= htmlspecialchars($dadosIA['nome'] ?? '-') ?></dd>
                        <dt>E-mail</dt><dd><?= htmlspecialchars($dadosIA['email'] ?? '-') ?></dd>
                        <dt>Telefone</dt><dd><?= htmlspecialchars($dadosIA['telefone'] ?? '-') ?></dd>
                        <dt>Cidade</dt><dd><?= htmlspecialchars($dadosIA['cidade'] ?? '-') ?></dd>
                        <?php if (!empty($dadosIA['linkedin'])): ?>
                        <dt>LinkedIn</dt><dd><a href="<?= htmlspecialchars($dadosIA['linkedin']) ?>" target="_blank">Ver perfil</a></dd>
                        <?php endif; ?>
                    </dl>
                </div>

                <?php if (!empty($dadosIA['objetivo'])): ?>
                <div class="cv-section">
                    <h4>🎯 Objetivo</h4>
                    <p><?= htmlspecialchars($dadosIA['objetivo']) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($dadosIA['resumo_profissional'])): ?>
                <div class="cv-section">
                    <h4>📝 Resumo</h4>
                    <p><?= htmlspecialchars($dadosIA['resumo_profissional']) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($dadosIA['experiencias'])): ?>
                <div class="cv-section">
                    <h4>💼 Experiências</h4>
                    <?php foreach ($dadosIA['experiencias'] as $exp): ?>
                    <div class="cv-item">
                        <strong><?= htmlspecialchars($exp['cargo']) ?></strong> — <?= htmlspecialchars($exp['empresa']) ?>
                        <span class="text-muted text-sm">(<?= htmlspecialchars($exp['periodo']) ?>)</span>
                        <p class="text-sm"><?= htmlspecialchars($exp['descricao']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($dadosIA['formacao'])): ?>
                <div class="cv-section">
                    <h4>🎓 Formação</h4>
                    <?php foreach ($dadosIA['formacao'] as $f): ?>
                    <div class="cv-item">
                        <strong><?= htmlspecialchars($f['curso']) ?></strong> — <?= htmlspecialchars($f['instituicao']) ?>
                        <span class="text-muted text-sm">(<?= htmlspecialchars($f['periodo']) ?>)</span>
                        <p class="text-sm"><?= htmlspecialchars($f['nivel']) ?> · <?= htmlspecialchars($f['status']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($dadosIA['habilidades'])): ?>
                <div class="cv-section">
                    <h4>⚡ Habilidades</h4>
                    <div class="tags">
                        <?php foreach ($dadosIA['habilidades'] as $h): ?>
                            <span class="tag"><?= htmlspecialchars($h) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($dadosIA['idiomas'])): ?>
                <div class="cv-section">
                    <h4>🌍 Idiomas</h4>
                    <div class="tags">
                        <?php foreach ($dadosIA['idiomas'] as $i): ?>
                            <span class="tag"><?= htmlspecialchars($i['idioma']) ?>: <?= htmlspecialchars($i['nivel']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php else: ?>
        <!-- TEXTO ORIGINAL -->
        <div class="card">
            <div class="card-header"><h3>Texto Original do Currículo</h3></div>
            <div class="card-body">
                <pre class="cv-raw"><?= htmlspecialchars($curriculo['texto_original'] ?? 'Nenhum texto disponível.') ?></pre>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
