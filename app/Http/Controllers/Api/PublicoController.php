<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use Illuminate\Http\Request;

class PublicoController extends Controller
{
    public function index(Request $request)
    {
        $query = Igreja::with(['paroquia', 'horarioMissas'])
            ->whereHas('paroquia', function ($q) {
                $q->where('status', 'aprovada');
            });

        if ($request->filled('bairro')) {
            $query->where('bairro', $request->bairro);
        }

        if ($request->filled('dia_semana')) {
            $query->whereHas('horarioMissas', function ($q) use ($request) {
                $q->where('dia_semana', $request->dia_semana);
            });
        }

        $igrejas = $query->get();

        $bairros = Igreja::whereHas('paroquia', function ($q) {
            $q->where('status', 'aprovada');
        })->distinct()->pluck('bairro');

        return response()->json([
            'igrejas' => $igrejas,
            'bairros' => $bairros,
        ]);
    }
}
