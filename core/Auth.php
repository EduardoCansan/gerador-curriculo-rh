<?php

class Auth
{
    /**
     * Tenta fazer login com email e senha.
     */
    public static function attempt(string $email, string $password): bool
    {
        require_once APP_ROOT . '/app/Models/User.php';
        $userModel = new User();
        $user = $userModel->findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if ((int)$user['ativo'] === 0) {
            return false;
        }

        // Salva dados do usuário na sessão (sem a senha)
        unset($user['password']);
        Session::set('auth_user', $user);
        Session::set('auth_time', time());

        return true;
    }

    /**
     * Verifica se há um usuário autenticado.
     */
    public static function check(): bool
    {
        if (!Session::has('auth_user')) {
            return false;
        }

        // Verifica expiração de sessão
        $authTime = Session::get('auth_time', 0);
        if (time() - $authTime > SESSION_LIFETIME) {
            self::logout();
            return false;
        }

        // Renova o tempo a cada request
        Session::set('auth_time', time());
        return true;
    }

    /**
     * Retorna os dados do usuário logado.
     */
    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    /**
     * Retorna o ID do usuário logado.
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Verifica se o usuário logado é admin.
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && $user['perfil'] === 'admin';
    }

    /**
     * Faz logout do usuário.
     */
    public static function logout(): void
    {
        Session::remove('auth_user');
        Session::remove('auth_time');
    }
}

// =============================================

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => false, // true em produção com HTTPS
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
            self::$started = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Mensagem flash (exibida uma vez e destruída).
     */
    public static function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    public static function destroy(): void
    {
        session_destroy();
        self::$started = false;
    }
}
