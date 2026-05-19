<?php

abstract class Controller
{
    /**
     * Renderiza uma view com layout padrão.
     *
     * @param string $view    Caminho relativo à pasta Views (ex: 'curriculos/index')
     * @param array  $data    Variáveis passadas para a view
     * @param string $layout  Layout a usar (default: 'main')
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extrai as variáveis para o escopo da view
        extract($data);

        // Captura o conteúdo da view
        ob_start();
        $viewFile = APP_ROOT . "/app/Views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View não encontrada: {$view}");
        }

        include $viewFile;
        $content = ob_get_clean();

        // Renderiza dentro do layout
        $layoutFile = APP_ROOT . "/app/Views/layouts/{$layout}.php";
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout não encontrado: {$layout}");
        }

        include $layoutFile;
    }

    /**
     * Renderiza uma view SEM layout (ex: para modais, fragments).
     */
    protected function viewPartial(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_ROOT . "/app/Views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View não encontrada: {$view}");
        }

        include $viewFile;
    }

    /**
     * Redireciona para uma URL.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Retorna resposta JSON (para requisições AJAX).
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Verifica se o usuário está autenticado.
     * Redireciona para login caso não esteja.
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }
    }

    /**
     * Verifica se o usuário é admin.
     * Redireciona com erro caso não seja.
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            Session::flash('error', 'Acesso restrito a administradores.');
            $this->redirect('dashboard');
        }
    }

    /**
     * Valida os dados recebidos conforme regras definidas.
     * Retorna array de erros (vazio se válido).
     */
    protected function validate(array $data, array $rules): array
    {
        return Validator::validate($data, $rules);
    }

    /**
     * Aborta a requisição com código HTTP.
     */
    protected function abort(int $code = 404, string $message = 'Página não encontrada'): void
    {
        http_response_code($code);
        echo "<h1>{$code} - {$message}</h1>";
        exit;
    }
}
