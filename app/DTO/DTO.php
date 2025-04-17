<?php

namespace App\DTO;

use Carbon\Carbon;
use App\DTO\Collections\DTOCollection;

class DTO
{
    /**
     * Cria uma instância do DTO a partir de um array associativo.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    /**
     * Cria uma instância do DTO a partir de um objeto.
     *
     * @param object $object
     * @return static
     */
    public static function fromObject(object $object): static
    {
        return static::fromArray((array) $object);
    }

    /**
     * Cria uma Collection de DTOs a partir de um array de arrays ou objetos.
     *
     * @param iterable $items
     * @return \Illuminate\Support\Collection<static>
     */
    public static function dtoCollection(iterable $items): DTOCollection
    {
        return new DTOCollection(
            collect($items)->map(fn($item) => is_object($item)
                ? static::fromObject($item)
                : static::fromArray($item)
            )->all()
        );
    }

    /**
     * Converte o DTO para array associativo.
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}