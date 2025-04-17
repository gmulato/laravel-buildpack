<?php

namespace App\Processors;

class Processor
{
    /**
     * Executa um callback se o valor de uma chave específica estiver presente e não for vazio.
     *
     * Útil para evitar ifs encadeados ao processar respostas de APIs ou dados parcialmente estruturados.
     *
     * @param array $response Array contendo os dados.
     * @param string $key Chave a ser verificada no array.
     * @param callable $callback Função que será chamada com o valor da chave, caso ela exista e não esteja vazia.
     * 
     * @return void
     */
    protected function processIfNotEmpty(array $response, string $key, callable $callback): void
    {
        if (!empty($response[$key])) {
            $callback($response[$key]);
        }
    }

    /**
     * Executa um callback se a chave existir no array, mesmo que o valor seja nulo.
     *
     * @param array $response
     * @param string $key
     * @param callable $callback
     * 
     * @return void
     */
    protected function processIfExists(array $response, string $key, callable $callback): void
    {
        if (array_key_exists($key, $response)) {
            $callback($response[$key]);
        }
    }

    /**
     * Executa o callback se o valor da chave for considerado "truthy".
     *
     * @param array $response
     * @param string $key
     * @param callable $callback
     * 
     * @return void
     */
    protected function processIfTruthy(array $response, string $key, callable $callback): void
    {
        if (!empty($response[$key]) || $response[$key] === true) {
            $callback($response[$key]);
        }
    }

    /**
     * Executa o callback se o valor da chave for uma instância da classe fornecida.
     *
     * @param array $response
     * @param string $key
     * @param string $class
     * @param callable $callback
     * 
     * @return void
     */
    protected function processIfInstanceOf(array $response, string $key, string $class, callable $callback): void
    {
        if (isset($response[$key]) && $response[$key] instanceof $class) {
            $callback($response[$key]);
        }
    }

    /**
     * Itera sobre o array da chave fornecida se ela estiver presente e for um array.
     *
     * @param array $response
     * @param string $key
     * @param callable $callback
     * 
     * @return void
     */
    protected function eachIfArray(array $response, string $key, callable $callback): void
    {
        if (isset($response[$key]) && is_array($response[$key])) {
            foreach ($response[$key] as $item) {
                $callback($item);
            }
        }
    }

    /**
     * Itera sobre a Collection da chave fornecida se ela for uma instância válida.
     *
     * @param array $response
     * @param string $key
     * @param callable $callback
     *
     * @return void
     */
    protected function eachIfCollection(array $response, string $key, callable $callback): void
    {
        if (isset($response[$key]) && $response[$key] instanceof \Illuminate\Support\Collection) {
            $response[$key]->each($callback);
        }
    }

}