<?php

class AdminController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // ------------------------------------------------
    //  LISTAGEM DE USUÁRIOS
    // ------------------------------------------------

    public function usuarios(): void
    {
        $this->requireAdmin();
        $usuarios = $this->userModel->listarTodos();

        $this->view('admin/usuarios', [
            'usuarios'  => $usuarios,
            'pageTitle' => 'Gerenciar Usuários',
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    // ------------------------------------------------
    //  CRIAR USUÁRIO
    // ------------------------------------------------

    public function novoUsuario(): void
    {
        $this->requireAdmin();
        $this->view('admin/usuario_form', [
            'pageTitle' => 'Novo Usuário',
            'usuario'   => [],
            'errors'    => [],
            'isNew'     => true,
        ]);
    }

    public function criarUsuario(): void
    {
        $this->requireAdmin();

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'perfil'   => $_POST['perfil'] ?? 'recrutador',
        ];

        $errors = $this->validate($data, [
            'name'     => 'required|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:8',
            'perfil'   => 'required|in:admin,recrutador',
        ]);

        if ($this->userModel->emailExiste($data['email'])) {
            $errors['email'][] = 'Este e-mail já está cadastrado.';
        }

        if ($errors) {
            $this->view('admin/usuario_form', [
                'pageTitle' => 'Novo Usuário',
                'usuario'   => $data,
                'errors'    => $errors,
                'isNew'     => true,
            ]);
            return;
        }

        $data['ativo'] = 1;
        $this->userModel->criar($data);

        Session::flash('success', 'Usuário criado com sucesso!');
        $this->redirect('admin/usuarios');
    }

    // ------------------------------------------------
    //  EDITAR USUÁRIO
    // ------------------------------------------------

    public function editarUsuario(string $id): void
    {
        $this->requireAdmin();
        $usuario = $this->userModel->find((int)$id);
        if (!$usuario) $this->abort(404);

        unset($usuario['password']);

        $this->view('admin/usuario_form', [
            'pageTitle' => 'Editar Usuário',
            'usuario'   => $usuario,
            'errors'    => [],
            'isNew'     => false,
        ]);
    }

    public function atualizarUsuario(string $id): void
    {
        $this->requireAdmin();
        $usuario = $this->userModel->find((int)$id);
        if (!$usuario) $this->abort(404);

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'perfil'   => $_POST['perfil'] ?? 'recrutador',
        ];

        $rules = [
            'name'   => 'required|max:100',
            'email'  => 'required|email',
            'perfil' => 'required|in:admin,recrutador',
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'min:8';
        }

        $errors = $this->validate($data, $rules);

        if ($this->userModel->emailExiste($data['email'], (int)$id)) {
            $errors['email'][] = 'Este e-mail já está sendo usado por outro usuário.';
        }

        if ($errors) {
            $this->view('admin/usuario_form', [
                'pageTitle' => 'Editar Usuário',
                'usuario'   => array_merge($usuario, $data),
                'errors'    => $errors,
                'isNew'     => false,
            ]);
            return;
        }

        $this->userModel->atualizar((int)$id, $data);

        // Impede admin de desativar a si mesmo
        if ((int)$id !== Auth::id()) {
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            $this->userModel->update((int)$id, ['ativo' => $ativo]);
        }

        Session::flash('success', 'Usuário atualizado com sucesso!');
        $this->redirect('admin/usuarios');
    }

    // ------------------------------------------------
    //  DELETAR USUÁRIO
    // ------------------------------------------------

    public function deletarUsuario(string $id): void
    {
        $this->requireAdmin();

        if ((int)$id === Auth::id()) {
            Session::flash('error', 'Você não pode deletar sua própria conta.');
            $this->redirect('admin/usuarios');
            return;
        }

        $this->userModel->delete((int)$id);
        Session::flash('success', 'Usuário removido.');
        $this->redirect('admin/usuarios');
    }

    // ------------------------------------------------
    //  TOGGLE ATIVO/INATIVO
    // ------------------------------------------------

    public function toggleAtivo(string $id): void
    {
        $this->requireAdmin();

        if ((int)$id === Auth::id()) {
            Session::flash('error', 'Você não pode desativar sua própria conta.');
            $this->redirect('admin/usuarios');
            return;
        }

        $usuario = $this->userModel->find((int)$id);
        if (!$usuario) $this->abort(404);

        $novoStatus = (int)$usuario['ativo'] === 1 ? 0 : 1;
        $this->userModel->update((int)$id, ['ativo' => $novoStatus]);

        $msg = $novoStatus ? 'Usuário ativado.' : 'Usuário desativado.';
        Session::flash('success', $msg);
        $this->redirect('admin/usuarios');
    }
}
