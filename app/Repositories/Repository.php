<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Repository
{
    /**
     * Insere itens em massa no banco de dados, ignorando duplicatas com base em chaves únicas.
     *
     * Divide o array de itens em pedaços e tenta inserir no banco. Registros que causariam violação de chave única são ignorados.
     *
     * @param array $items Dados a serem inseridos no banco. Cada item é um array associativo.
     * @param string $model O modelo Eloquent usado para a inserção (ex: League::class, Team::class).
     * @param int $chunkSize Tamanho dos pedaços para inserção. O padrão é 30.
     * 
     * @return void
     */
    protected function insertOrIgnore(array $items, string $model, int $chunkSize = 30): void
    {
        if (empty($items)) {
            return;
        }

        $chunks = array_chunk($items, $chunkSize);

        foreach ($chunks as $chunk) {
            $model::insertOrIgnore($chunk);
        }
    }

    /**
     * Sincroniza uma relação hasMany com base em uma chave identificadora.
     *
     * Para cada item:
     * - Se o item contiver o identificador (ex: 'id') e já existir na relação, será atualizado.
     * - Se o identificador for null ou ausente, um novo registro será criado.
     * - Todos os registros existentes na relação que não estiverem presentes nos dados fornecidos serão deletados.
     *
     * @param HasMany $relation Relação hasMany Eloquent (ex: $post->comments()).
     * @param array $items Lista de dados que devem representar exatamente os filhos da relação.
     * @param int $chunkSize Tamanho dos blocos usados na exclusão em massa.
     * @param string $key Chave usada para identificar os registros (por padrão: 'id').
     *
     * @return void
     */
    protected function syncHasManyByKey(HasMany $relation, array $items, int $chunkSize = 30, string $key = 'id'): void
    {
        DB::transaction(function () use ($relation, $items, $key, $chunkSize) {
            $existing = $relation->get()->keyBy($key);
            $existingIds = $existing->keys();
            $syncedIds = [];
    
            foreach ($items as $item) {
                if (!empty($item[$key]) && $existing->has($item[$key])) {
                    $model = $existing[$item[$key]];
    
                    if (array_diff_assoc($item, $model->getAttributes())) {
                        $model->fill($item)->save();
                    }
    
                    $syncedIds[] = $item[$key];
                } elseif (empty($item[$key])) { 
                    $new = $relation->create($item);
                    $syncedIds[] = $new->$key;
                }
            }
            
            $idsToDelete = $existingIds->diff($syncedIds);
            if ($idsToDelete->isNotEmpty()) {
                foreach ($idsToDelete->chunk($chunkSize) as $chunk) {
                    $relation->whereIn($key, $chunk->all())->delete();
                }
            }
        });
    }

    /**
     * Substitui todos os registros de uma relação HasMany.
     *
     * Deleta todos os registros existentes relacionados e insere os novos registros fornecidos.
     * Operações de deleção e inserção são feitas em pedaços (chunks) para evitar sobrecarga no banco de dados.
     *
     * Útil quando não há identificador único para sincronização e é mais eficiente remover e reinserir.
     *
     * @param HasMany $relation Relação HasMany que será substituída.
     * @param array $items Lista de novos itens a serem inseridos. Cada item é um array associativo.
     * @param int $chunkSize Tamanho dos pedaços (chunks) usados para deletar e inserir registros. Padrão é 50.
     *
     * @return void
     */
    protected function replaceHasMany(HasMany $relation, array $items, int $chunkSize = 50): void
    {
        DB::transaction(function () use ($relation, $items, $chunkSize) {
            $relation->getQuery()->chunkById($chunkSize, function ($models) {
                $models->each->delete();
            });

            foreach (array_chunk($items, $chunkSize) as $chunk) {
                $relation->createMany($chunk);
            }
        });
    }
    
    /**
     * Tenta criar ou buscar um registro de forma segura, contornando erros de chave única.
     *
     * Esse método é uma alternativa segura ao `firstOrCreate`, especialmente útil quando há
     * possibilidade de concorrência ou conflitos com chaves únicas no banco de dados.
     *
     * Ele tenta criar um novo registro com os atributos fornecidos. Caso ocorra uma exceção
     * de integridade (como `Duplicate entry` por uma chave única), ele realiza uma busca
     * com os mesmos atributos e retorna o registro existente.
     *
     * @param string $model Classe do model Eloquent (ex: App\Models\User)
     * @param array $attributes Atributos usados para buscar ou criar o registro
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function firstOrCreateSafe(string $model, array $attributes)
    {
        try {
            return $model::firstOrCreate($attributes);
        } catch (\Illuminate\Database\QueryException $e) {
            return $model::where($attributes)->first();
        }
    }

    /**
     * Normaliza um valor numérico, retornando um valor mínimo caso o original seja inválido ou nulo.
     *
     * @param mixed $value Valor de entrada (pode ser nulo, string, etc).
     * @param int|float $default Valor padrão mínimo a ser retornado. Padrão é 0.
     * @return int|float Valor numérico normalizado.
     */
    protected function normalizeNumber(mixed $value, int|float $default = 0): int|float
    {
        return is_numeric($value) && $value > 0 ? $value : $default;
    }

    /**
     * Retorna o valor de um campo numérico, ou zero caso ele seja nulo ou menor que zero.
     *
     * @param mixed $value Valor a ser avaliado.
     * @return int|float Valor válido ou zero.
     */
    protected function getValueOrZero(mixed $value): int|float
    {
        return $value > 0 ? $value : 0;
    }

}
