<?php

class Curriculo extends Model
{
    protected string $table = 'curriculos';

    /**
     * Lista todos os currículos com o nome do recrutador.
     */
    public function listarTodos(): array
    {
        return $this->query(
            "SELECT c.*, u.name AS recrutador_nome
             FROM curriculos c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             ORDER BY c.created_at DESC"
        );
    }

    /**
     * Busca um currículo com nome do recrutador.
     */
    public function buscarComRecrutador(int $id): array|false
    {
        return $this->queryOne(
            "SELECT c.*, u.name AS recrutador_nome, u.email AS recrutador_email
             FROM curriculos c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.id = ?",
            [$id]
        );
    }

    /**
     * Estatísticas gerais para o dashboard.
     */
    public function estatisticas(): array
    {
        $total       = $this->count();
        $processados = $this->count('status', 'processado');
        $pendentes   = $this->count('status', 'pendente');
        $erro        = $this->count('status', 'erro');

        return compact('total', 'processados', 'pendentes', 'erro');
    }

    /**
     * Últimos N currículos enviados.
     */
    public function ultimos(int $limit = 10): array
    {
        return $this->query(
            "SELECT c.*, u.name AS recrutador_nome
             FROM curriculos c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             ORDER BY c.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Salva os dados extraídos pela IA no currículo.
     */
    public function salvarDadosIA(int $id, array $dadosIA, string $pdfPath): bool
    {
        return $this->update($id, [
            'dados_extraidos' => json_encode($dadosIA, JSON_UNESCAPED_UNICODE),
            'pdf_padronizado' => $pdfPath,
            'status'          => 'processado',
            'processado_em'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Marca o currículo como erro no processamento.
     */
    public function marcarErro(int $id, string $mensagem): bool
    {
        return $this->update($id, [
            'status'         => 'erro',
            'erro_mensagem'  => $mensagem,
        ]);
    }
}
