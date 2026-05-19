<?php

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    /**
     * Registra uma rota GET.
     */
    public function get(string $path, string $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Registra uma rota POST.
     */
    public function post(string $path, string $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Registra uma rota DELETE.
     */
    public function delete(string $path, string $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Adiciona uma rota à lista.
     */
    private function addRoute(string $method, string $path, string $handler, array $middlewares): void
    {
        // Converte parâmetros dinâmicos (:id, :slug) em regex
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $path);
        $pattern = '@^' . $pattern . '$@';

        $this->routes[] = [
            'method'      => $method,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Despacha a requisição para o controller/método correto.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        // Suporte a _method (para DELETE via formulário)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove o prefixo do APP_URL do URI
        $basePath = parse_url(APP_URL, PHP_URL_PATH);
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '') $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Filtra apenas os grupos nomeados
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Executa middlewares
                foreach ($route['middlewares'] as $middleware) {
                    (new $middleware())->handle();
                }

                // Extrai Controller@método
                [$controllerName, $methodName] = explode('@', $route['handler']);

                $controllerFile = APP_ROOT . "/app/Controllers/{$controllerName}.php";
                if (!file_exists($controllerFile)) {
                    $this->notFound();
                    return;
                }

                require_once $controllerFile;
                $controller = new $controllerName();

                if (!method_exists($controller, $methodName)) {
                    $this->notFound();
                    return;
                }

                $controller->$methodName(...array_values($params));
                return;
            }
        }

        $this->notFound();
    }

    /**
     * Resposta 404.
     */
    private function notFound(): void
    {
        http_response_code(404);
        if (file_exists(APP_ROOT . '/app/Views/errors/404.php')) {
            include APP_ROOT . '/app/Views/errors/404.php';
        } else {
            echo '<h1>404 - Página não encontrada</h1>';
        }
        exit;
    }
}
