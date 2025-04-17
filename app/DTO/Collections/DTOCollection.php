<?php

namespace App\DTO\Collections;

use Illuminate\Support\Collection;

class DTOCollection extends Collection
{
    /**
     * Aplica uma sequência de métodos em cada DTO da coleção.
     *
     * Exemplo: ['getData', 'format'] irá fazer $dto->getData()->format()
     *
     * @param array $methods Lista de métodos a serem encadeados em cada DTO.
     * @return static
     */
    public function mapEachMethods(array $methods): static
    {
        return $this->map(function ($dto) use ($methods) {
            return array_reduce($methods, fn($carry, $method) => $carry->$method(), $dto);
        });
    }

    /**
     * Aplica um método específico de cada DTO da coleção e retorna os resultados como nova Collection.
     *
     * @param string $method Nome do método a ser chamado em cada item.
     * @param array $args Argumentos opcionais a serem passados para o método.
     * @return static
     */
    public function mapMethod(string $method, array $args = []): static
    {
        return $this->map(fn($dto) => $dto->$method(...$args));
    }

    /**
     * Converte todos os DTOs da coleção para arrays.
     *
     * @return static
     */
    public function toArray(): array
    {
        return $this->items ? array_map(fn($dto) => $dto->toArray(), $this->items) : [];
    }
    
}
