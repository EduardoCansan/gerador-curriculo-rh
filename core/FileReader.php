<?php

class FileReader
{
    /**
     * Extrai texto de um arquivo PDF ou DOCX.
     * Retorna o texto puro ou lança Exception em caso de erro.
     */
    public static function extract(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match($ext) {
            'pdf'  => self::extractPdf($filePath),
            'docx' => self::extractDocx($filePath),
            default => throw new RuntimeException("Formato não suportado: {$ext}"),
        };
    }

    // ------------------------------------------------
    //  PDF — usa pdfparser.phar
    // ------------------------------------------------
    private static function extractPdf(string $filePath): string
    {
        $autoload = APP_ROOT . '/core/pdfparser/alt_autoload.php-dist';

        if (!file_exists($autoload)) {
            throw new RuntimeException(
                'pdfparser não encontrado. Baixe em: github.com/smalot/pdfparser e coloque em core/pdfparser/'
            );
        }

        require_once $autoload;

        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($filePath);
        $text   = $pdf->getText();

        if (empty(trim($text))) {
            throw new RuntimeException(
                'Não foi possível extrair texto do PDF. O arquivo pode ser uma imagem escaneada.'
            );
        }

        return trim($text);
    }

    // ------------------------------------------------
    //  DOCX — lê o XML interno do arquivo ZIP
    // ------------------------------------------------
    private static function extractDocx(string $filePath): string
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('Extensão ZipArchive não está ativa no PHP.');
        }

        $zip = new ZipArchive();

        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('Não foi possível abrir o arquivo DOCX.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException('Arquivo DOCX inválido ou corrompido.');
        }

        // Remove tags XML e decodifica entidades HTML
        $text = preg_replace('/(<\/w:p>|<\/w:br>)/', "\n", $xml);
        $text = preg_replace('/<[^>]+>/', ' ', $text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s{2,}/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        if (empty(trim($text))) {
            throw new RuntimeException('Não foi possível extrair texto do DOCX.');
        }

        return trim($text);
    }
}