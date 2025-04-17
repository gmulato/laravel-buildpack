<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    /**
     * Resposta de sucesso padronizada.
     */
    protected function success(mixed $data = null, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Resposta de erro padronizada.
     */
    protected function error(string $message = 'Erro interno', int $status = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

}