<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Define valores padrões de comportamento de fila.
     * Ex: fila, connection, delay, batch, etc.
     */
    public function configure(): static
    {
        return $this->onQueue($this->queueName())
                    ->onConnection($this->connectionName());
    }

    /**
     * Nome da fila padrão para o job.
     * Pode ser sobrescrita por constante ou método.
     */
    protected function queueName(): string
    {
        return static::QUEUE ?? 'default';
    }

    /**
     * Nome da conexão da fila padrão para o job.
     * Pode ser sobrescrita por constante ou método.
     */
    protected function connectionName(): string
    {
        return static::CONNECTION ?? config('queue.default', 'redis');
    }
    
}
