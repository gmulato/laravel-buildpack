<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class Service
{
    /**
     * Executa uma função com múltiplas tentativas (retry) em caso de falhas.
     *
     * Útil para operações que podem falhar temporariamente, como chamadas de API externa ou gravações concorrentes no banco.
     *
     * @param callable $callback Função a ser executada.
     * @param int $maxAttempts Número máximo de tentativas (padrão: 3).
     * @param int $sleepMs Tempo de espera entre tentativas em milissegundos (padrão: 100ms).
     * 
     * @return mixed Retorno da função, caso tenha sucesso.
     *
     * @throws \Throwable Lança a exceção original caso todas as tentativas falhem.
     */
    protected function tryWithRetries(callable $callback, int $maxAttempts = 3, int $sleepMs = 100): mixed
    {
        $attempts = 0;
        do {
            try {
                return $callback();
            } catch (\Throwable $e) {
                if (++$attempts >= $maxAttempts) {
                    throw $e;
                }
                usleep($sleepMs * 1000);
            }
        } while (true);
    }

    /**
     * Valida os dados recebidos com base nas regras definidas.
     *
     * Caso a validação falhe, lança uma ValidationException. Ideal para validações internas de serviços (sem depender de Form Requests).
     *
     * @param array $data Dados a serem validados.
     * @param array $rules Regras de validação no formato do Laravel Validator.
     * 
     * @return array Dados validados.
     *
     * @throws \Illuminate\Validation\ValidationException Se a validação falhar.
     */
    protected function validateOrFail(array $data, array $rules): array
    {
        $validator = validator($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }

    /**
     * Lança uma exceção se a condição for falsa.
     *
     * @param bool $condition
     * @param string|\Closure $message
     *
     * @throws \RuntimeException
     */
    protected function ensure(bool $condition, string|\Closure $message): void
    {
        if (! $condition) {
            throw new \RuntimeException(is_callable($message) ? $message() : $message);
        }
    }

}
