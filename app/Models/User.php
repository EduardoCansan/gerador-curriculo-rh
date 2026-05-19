<?php

class User extends Model
{
    protected string $table = 'usuarios';

    /**
     * Cria um novo usuário com senha criptografada.
     */
    public function criar(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->create($data);
    }

    /**
     * Atualiza usuário; criptografa senha apenas se fornecida.
     */
    public function atualizar(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        } else {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }

    /**
     * Lista todos os usuários sem a senha.
     */
    public function listarTodos(): array
    {
        return $this->query(
            "SELECT id, name, email, perfil, ativo, created_at FROM usuarios ORDER BY name ASC"
        );
    }

    /**
     * Verifica se email já existe (opcionalmente excluindo um ID).
     */
    public function emailExiste(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $result = $this->queryOne(
                "SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1",
                [$email, $excludeId]
            );
        } else {
            $result = $this->findBy('email', $email);
        }
        return (bool)$result;
    }
}
