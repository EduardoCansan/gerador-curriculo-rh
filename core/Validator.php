<?php

class Validator
{
    /**
     * Valida um array de dados conforme regras.
     *
     * Regras suportadas:
     *   required, email, min:N, max:N, confirmed, in:a,b,c
     *
     * @return array Erros encontrados (vazio se válido)
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value    = $data[$field] ?? null;
            $label    = self::label($field);

            foreach ($ruleList as $rule) {
                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || trim((string)$value) === '') {
                            $errors[$field][] = "{$label} é obrigatório.";
                        }
                        break;

                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "{$label} deve ser um e-mail válido.";
                        }
                        break;

                    case 'min':
                        if ($value !== null && strlen((string)$value) < (int)$param) {
                            $errors[$field][] = "{$label} deve ter pelo menos {$param} caracteres.";
                        }
                        break;

                    case 'max':
                        if ($value !== null && strlen((string)$value) > (int)$param) {
                            $errors[$field][] = "{$label} deve ter no máximo {$param} caracteres.";
                        }
                        break;

                    case 'confirmed':
                        $confirmValue = $data[$field . '_confirmation'] ?? null;
                        if ($value !== $confirmValue) {
                            $errors[$field][] = "{$label} e a confirmação não coincidem.";
                        }
                        break;

                    case 'in':
                        $allowed = explode(',', $param);
                        if ($value && !in_array($value, $allowed, true)) {
                            $errors[$field][] = "{$label} inválido.";
                        }
                        break;

                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "{$label} deve ser numérico.";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Converte o nome do campo em label legível.
     */
    private static function label(string $field): string
    {
        $map = [
            'name'             => 'Nome',
            'email'            => 'E-mail',
            'password'         => 'Senha',
            'password_confirmation' => 'Confirmação de senha',
            'perfil'           => 'Perfil',
            'texto_curriculo'  => 'Texto do currículo',
        ];

        return $map[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
