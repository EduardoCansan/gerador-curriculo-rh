<?php

class CurriculoController extends Controller
{
    private Curriculo $model;

    public function __construct()
    {
        $this->model = new Curriculo();
    }

    // ------------------------------------------------
    //  LISTAGEM
    // ------------------------------------------------

    public function index(): void
    {
        $this->requireAuth();
        $curriculos = $this->model->listarTodos();
        $this->view('curriculos/index', [
            'curriculos' => $curriculos,
            'pageTitle'  => 'Currículos',
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    // ------------------------------------------------
    //  FORMULÁRIO DE NOVO CURRÍCULO
    // ------------------------------------------------

    public function create(): void
    {
        $this->requireAuth();
        $this->view('curriculos/create', [
            'pageTitle' => 'Novo Currículo',
            'errors'    => [],
        ]);
    }

    // ------------------------------------------------
    //  SALVAR NOVO CURRÍCULO
    // ------------------------------------------------

    public function store(): void
    {
        $this->requireAuth();

        $tipo  = $_POST['tipo_entrada'] ?? 'texto'; // 'texto' ou 'arquivo'
        $nome  = trim($_POST['nome_candidato'] ?? '');
        $texto = trim($_POST['texto_curriculo'] ?? '');
        $arquivo = null;

        $errors = $this->validate(
            ['nome_candidato' => $nome],
            ['nome_candidato' => 'required|max:150']
        );

        // Upload de arquivo
        if ($tipo === 'arquivo') {
            $uploadResult = $this->handleUpload();
            if (isset($uploadResult['error'])) {
                $errors['arquivo'] = [$uploadResult['error']];
            } else {
                $arquivo = $uploadResult['filename'];
                // Extrai texto do arquivo para passar à IA
                $texto = $uploadResult['text'];
            }
        } else {
            if (empty($texto)) {
                $errors['texto_curriculo'] = ['O texto do currículo é obrigatório.'];
            }
        }

        if ($errors) {
            $this->view('curriculos/create', [
                'pageTitle' => 'Novo Currículo',
                'errors'    => $errors,
                'old'       => $_POST,
            ]);
            return;
        }

        $id = $this->model->create([
            'usuario_id'      => Auth::id(),
            'nome_candidato'  => $nome,
            'tipo_entrada'    => $tipo,
            'texto_original'  => $texto,
            'arquivo_original'=> $arquivo,
            'status'          => 'pendente',
        ]);

        Session::flash('success', 'Currículo cadastrado! Clique em "Processar com IA" para padronizar.');
        $this->redirect("curriculos/{$id}");
    }

    // ------------------------------------------------
    //  VISUALIZAR CURRÍCULO
    // ------------------------------------------------

    public function show(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->buscarComRecrutador((int)$id);

        if (!$curriculo) $this->abort(404);

        $dadosIA = null;
        if ($curriculo['dados_extraidos']) {
            $dadosIA = json_decode($curriculo['dados_extraidos'], true);
        }

        $this->view('curriculos/show', [
            'curriculo' => $curriculo,
            'dadosIA'   => $dadosIA,
            'pageTitle' => 'Currículo: ' . $curriculo['nome_candidato'],
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    // ------------------------------------------------
    //  EDITAR / ATUALIZAR
    // ------------------------------------------------

    public function edit(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->find((int)$id);
        if (!$curriculo) $this->abort(404);

        $this->view('curriculos/edit', [
            'curriculo' => $curriculo,
            'pageTitle' => 'Editar Currículo',
            'errors'    => [],
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->find((int)$id);
        if (!$curriculo) $this->abort(404);

        $nome  = trim($_POST['nome_candidato'] ?? '');
        $texto = trim($_POST['texto_curriculo'] ?? '');

        $errors = $this->validate(
            ['nome_candidato' => $nome, 'texto_curriculo' => $texto],
            ['nome_candidato' => 'required|max:150', 'texto_curriculo' => 'required']
        );

        if ($errors) {
            $this->view('curriculos/edit', [
                'curriculo' => array_merge($curriculo, $_POST),
                'pageTitle' => 'Editar Currículo',
                'errors'    => $errors,
            ]);
            return;
        }

        $this->model->update((int)$id, [
            'nome_candidato' => $nome,
            'texto_original' => $texto,
            'status'         => 'pendente', // Resetar para reprocessar
        ]);

        Session::flash('success', 'Currículo atualizado. Reprocesse com IA se necessário.');
        $this->redirect("curriculos/{$id}");
    }

    // ------------------------------------------------
    //  DELETAR
    // ------------------------------------------------

    public function destroy(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->find((int)$id);
        if (!$curriculo) $this->abort(404);

        // Remove arquivo original se existir
        if ($curriculo['arquivo_original']) {
            $filePath = UPLOAD_PATH . $curriculo['arquivo_original'];
            if (file_exists($filePath)) unlink($filePath);
        }

        // Remove PDF padronizado se existir
        if ($curriculo['pdf_padronizado']) {
            $pdfPath = PDF_OUTPUT_PATH . $curriculo['pdf_padronizado'];
            if (file_exists($pdfPath)) unlink($pdfPath);
        }

        $this->model->delete((int)$id);
        Session::flash('success', 'Currículo removido com sucesso.');
        $this->redirect('curriculos');
    }

    // ------------------------------------------------
    //  PROCESSAR COM IA (Claude API)
    // ------------------------------------------------

    public function processar(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->find((int)$id);
        if (!$curriculo) $this->abort(404);

        if (empty($curriculo['texto_original'])) {
            Session::flash('error', 'Não há texto para processar.');
            $this->redirect("curriculos/{$id}");
            return;
        }

        try {
            // 1. Chama a Claude API para extrair dados estruturados
            $dadosExtraidos = $this->chamarClaudeExtracao($curriculo['texto_original']);

            // 2. Gera o PDF padronizado
            $pdfFilename = $this->gerarPDF($dadosExtraidos, (int)$id);

            // 3. Salva no banco
            $this->model->salvarDadosIA((int)$id, $dadosExtraidos, $pdfFilename);

            Session::flash('success', 'Currículo processado com sucesso pela IA!');
        } catch (Exception $e) {
            $this->model->marcarErro((int)$id, $e->getMessage());
            Session::flash('error', 'Erro ao processar: ' . $e->getMessage());
        }

        $this->redirect("curriculos/{$id}");
    }

    // ------------------------------------------------
    //  DOWNLOAD DO PDF
    // ------------------------------------------------

    public function download(string $id): void
    {
        $this->requireAuth();
        $curriculo = $this->model->find((int)$id);

        if (!$curriculo || !$curriculo['pdf_padronizado']) {
            Session::flash('error', 'PDF não disponível.');
            $this->redirect("curriculos/{$id}");
            return;
        }

        $filePath = PDF_OUTPUT_PATH . $curriculo['pdf_padronizado'];

        if (!file_exists($filePath)) {
            Session::flash('error', 'Arquivo não encontrado.');
            $this->redirect("curriculos/{$id}");
            return;
        }

        $filename = 'curriculo_' . preg_replace('/[^a-z0-9]/i', '_', $curriculo['nome_candidato']) . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    // ================================================
    //  MÉTODOS PRIVADOS
    // ================================================

    /**
     * Faz upload do arquivo e extrai o texto.
     */
    private function handleUpload(): array
    {
        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Falha no upload do arquivo.'];
        }

        $file = $_FILES['arquivo'];

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return ['error' => 'Arquivo muito grande. Máximo: 5MB.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) {
            return ['error' => 'Tipo de arquivo não permitido. Use PDF ou DOCX.'];
        }

        $filename = uniqid('cv_', true) . '.' . $ext;
        $dest = UPLOAD_PATH . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Não foi possível salvar o arquivo.'];
        }

        // Extrai texto do PDF (requer pdftotext instalado)
        $text = '';
        if ($ext === 'pdf') {
            $text = shell_exec('pdftotext ' . escapeshellarg($dest) . ' - 2>/dev/null') ?? '';
        }

        // Para DOCX, usa uma extração simples via zip
        if ($ext === 'docx') {
            $text = $this->extractDocxText($dest);
        }

        return ['filename' => $filename, 'text' => trim($text)];
    }

    /**
     * Extrai texto de DOCX (formato ZIP com XML interno).
     */
    private function extractDocxText(string $filePath): string
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                $xml  = preg_replace('/<[^>]+>/', ' ', $xml);
                return html_entity_decode(strip_tags($xml), ENT_QUOTES, 'UTF-8');
            }
        } catch (Exception $e) {
            // silencia erros de extração
        }
        return '';
    }

    /**
     * Chama a API do Claude para extrair dados estruturados do currículo.
     * Retorna array com os dados extraídos.
     */
    private function chamarClaudeExtracao(string $textoCurriculo): array
    {
        $prompt = <<<PROMPT
Você é um especialista em RH. Analise o currículo abaixo e extraia as informações em formato JSON estruturado.

CURRÍCULO:
{$textoCurriculo}

Retorne APENAS um JSON válido com esta estrutura (sem markdown, sem explicações):
{
  "nome": "Nome completo",
  "email": "email@exemplo.com",
  "telefone": "(00) 00000-0000",
  "cidade": "Cidade - Estado",
  "linkedin": "URL ou null",
  "resumo_profissional": "Resumo em 2-3 frases",
  "objetivo": "Cargo/área de interesse",
  "experiencias": [
    {
      "empresa": "Nome da empresa",
      "cargo": "Cargo",
      "periodo": "Mês/Ano - Mês/Ano",
      "descricao": "Principais atividades"
    }
  ],
  "formacao": [
    {
      "instituicao": "Nome",
      "curso": "Nome do curso",
      "nivel": "Graduação/Pós/MBA/Técnico",
      "periodo": "Ano - Ano",
      "status": "Concluído/Em andamento"
    }
  ],
  "habilidades": ["Habilidade 1", "Habilidade 2"],
  "idiomas": [
    {"idioma": "Português", "nivel": "Nativo"},
    {"idioma": "Inglês", "nivel": "Intermediário"}
  ],
  "certificacoes": ["Certificação 1"]
}
PROMPT;

        $response = $this->claudeAPICall($prompt);

        // Limpa e valida o JSON
        $json = preg_replace('/```(json)?/i', '', $response);
        $json = trim($json, "`\n ");

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('A IA retornou um formato inválido. Tente novamente.');
        }

        return $data;
    }

    /**
     * Faz a chamada HTTP para a API do Claude.
     */
    private function claudeAPICall(string $prompt): string
    {
        $payload = json_encode([
            'model'      => CLAUDE_MODEL,
            'max_tokens' => CLAUDE_MAX_TOKENS,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . CLAUDE_API_KEY,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_TIMEOUT        => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('Erro de conexão com a API: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            throw new RuntimeException('API retornou erro: ' . ($error['error']['message'] ?? $httpCode));
        }

        $data = json_decode($response, true);
        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Gera um PDF padronizado com os dados extraídos pela IA.
     * Usa HTML + wkhtmltopdf (ou fallback em PHP puro).
     */
    private function gerarPDF(array $dados, int $curriculoId): string
    {
        // Garante que o diretório existe
        if (!is_dir(PDF_OUTPUT_PATH)) {
            mkdir(PDF_OUTPUT_PATH, 0755, true);
        }

        $filename = 'cv_padronizado_' . $curriculoId . '_' . time() . '.pdf';
        $outputPath = PDF_OUTPUT_PATH . $filename;

        // Gera o HTML do currículo
        $html = $this->gerarHTMLCurriculo($dados);

        // Tenta usar wkhtmltopdf se disponível
        $tempHtml = sys_get_temp_dir() . '/cv_temp_' . $curriculoId . '.html';
        file_put_contents($tempHtml, $html);

        $wk = shell_exec('which wkhtmltopdf 2>/dev/null');
        if (trim($wk)) {
            $cmd = sprintf(
                'wkhtmltopdf --quiet --page-size A4 --margin-top 15 --margin-bottom 15 --margin-left 15 --margin-right 15 %s %s 2>/dev/null',
                escapeshellarg($tempHtml),
                escapeshellarg($outputPath)
            );
            shell_exec($cmd);
            @unlink($tempHtml);
        } else {
            // Fallback: salva como HTML (o usuário pode abrir e imprimir como PDF)
            $filename = str_replace('.pdf', '.html', $filename);
            $outputPath = PDF_OUTPUT_PATH . $filename;
            file_put_contents($outputPath, $html);
        }

        return $filename;
    }

    /**
     * Gera o HTML formatado do currículo padronizado.
     */
    private function gerarHTMLCurriculo(array $d): string
    {
        $esc = fn($s) => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');

        $experiencias = '';
        foreach ($d['experiencias'] ?? [] as $exp) {
            $experiencias .= "
            <div class='item'>
                <div class='item-header'>
                    <strong>{$esc($exp['cargo'])}</strong> — {$esc($exp['empresa'])}
                    <span class='periodo'>{$esc($exp['periodo'])}</span>
                </div>
                <p>{$esc($exp['descricao'])}</p>
            </div>";
        }

        $formacao = '';
        foreach ($d['formacao'] ?? [] as $f) {
            $formacao .= "
            <div class='item'>
                <div class='item-header'>
                    <strong>{$esc($f['curso'])}</strong> — {$esc($f['instituicao'])}
                    <span class='periodo'>{$esc($f['periodo'])}</span>
                </div>
                <p>{$esc($f['nivel'])} · {$esc($f['status'])}</p>
            </div>";
        }

        $habilidades = implode(' &bull; ', array_map($esc, $d['habilidades'] ?? []));

        $idiomas = '';
        foreach ($d['idiomas'] ?? [] as $i) {
            $idiomas .= "<span class='tag'>{$esc($i['idioma'])}: {$esc($i['nivel'])}</span> ";
        }

        $linkedin = $d['linkedin'] ? "<a href='{$esc($d['linkedin'])}'>LinkedIn</a>" : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; font-size: 11pt; margin: 0; padding: 0; }
  .header { background: #1a3a5c; color: white; padding: 24px 30px; }
  .header h1 { margin: 0 0 6px; font-size: 22pt; }
  .header .contato { font-size: 9.5pt; opacity: 0.9; }
  .header .contato span { margin-right: 16px; }
  .body { padding: 20px 30px; }
  h2 { color: #1a3a5c; border-bottom: 2px solid #1a3a5c; padding-bottom: 4px; margin-top: 22px; font-size: 13pt; }
  .objetivo { background: #f0f4f8; border-left: 4px solid #1a3a5c; padding: 10px 14px; border-radius: 0 4px 4px 0; margin-bottom: 10px; }
  .item { margin-bottom: 14px; }
  .item-header { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; }
  .item-header .periodo { color: #666; font-size: 9.5pt; }
  .item p { margin: 4px 0 0; color: #555; font-size: 10pt; }
  .habilidades { line-height: 2; }
  .tag { background: #e8eef4; padding: 2px 8px; border-radius: 10px; margin-right: 4px; font-size: 9.5pt; display: inline-block; }
  .footer { margin-top: 20px; text-align: center; font-size: 8pt; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }
</style>
</head>
<body>
<div class="header">
  <h1>{$esc($d['nome'])}</h1>
  <div class="contato">
    <span>✉ {$esc($d['email'])}</span>
    <span>☎ {$esc($d['telefone'])}</span>
    <span>📍 {$esc($d['cidade'])}</span>
    {$linkedin}
  </div>
</div>
<div class="body">
  <div class="objetivo"><strong>Objetivo:</strong> {$esc($d['objetivo'])}</div>

  <h2>Resumo Profissional</h2>
  <p>{$esc($d['resumo_profissional'])}</p>

  <h2>Experiência Profissional</h2>
  {$experiencias}

  <h2>Formação Acadêmica</h2>
  {$formacao}

  <h2>Habilidades</h2>
  <div class="habilidades">{$habilidades}</div>

  <h2>Idiomas</h2>
  <div>{$idiomas}</div>
</div>
<div class="footer">Currículo padronizado por RH Padronizador · Gerado em {$esc(date('d/m/Y'))}</div>
</body>
</html>
HTML;
    }
}
