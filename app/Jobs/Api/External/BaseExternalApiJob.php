<?php

namespace App\Jobs\Api\External;

use App\Jobs\Job;
use App\Services\ExternalApiService;

/*
    Este é um exemplo de estrutura base para Jobs\Api\{Servico}\Base{Servico}Job.

    Você pode duplicar esta estrutura para outros serviços externos como:
    - Jobs\Api\Facebook\BaseFacebookJob
    - Jobs\Api\Google\BaseGoogleJob
    - Jobs\Api\Webhook\BaseWebhookJob

    Cada job base pode definir fila/conexão padrão e compartilhar lógica ou instâncias de serviços.
*/

abstract class BaseExternalApiJob extends Job
{
    public const QUEUE = 'api-external';
    public const CONNECTION = 'redis';

    protected ExternalApiService $external;

    public function __construct()
    {
        $this->external = app(ExternalApiService::class);
        $this->configure();
    }
}
