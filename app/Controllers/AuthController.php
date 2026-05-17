<?php

require_once APP_ROOT . '/app/Controllers/AuthController.php';

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $error = Session::getFlash('error');
        $this->view('auth/login', ['error' => $error], 'auth');
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = $this->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if ($errors) {
            Session::flash('error', 'Preencha e-mail e senha corretamente.');
            $this->redirect('login');
            return;
        }

        if (Auth::attempt($email, $password)) {
            $this->redirect('dashboard');
        } else {
            Session::flash('error', 'E-mail ou senha inválidos, ou usuário inativo.');
            $this->redirect('login');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'Você saiu do sistema.');
        $this->redirect('login');
    }
}
