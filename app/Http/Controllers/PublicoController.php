<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\Paroquia;
use Illuminate\Http\Request;

class PublicoController extends Controller
{
    /**
     * GET /
     * Página inicial pública — lista todas as igrejas com horários de missa.
     * Qualquer pessoa pode acessar sem login.
     * Aceita filtros opcionais por bairro e dia da semana via query string.
     * Exemplo: /?bairro=Boa+Viagem&dia_semana=domingo
     */
    public function index(Request $request)
    {
        // Busca igrejas com seus relacionamentos (paroquia e horários),
        // filtrando apenas as que pertencem a paróquias aprovadas pelo admin
        $query = Igreja::with(['paroquia', 'horarioMissas'])
            ->whereHas('paroquia', function ($q) {
                $q->where('status', 'aprovada');
            });

        // Filtro por bairro — só aplica se o parâmetro foi enviado na URL
        if ($request->filled('bairro')) {
            $query->where('bairro', $request->bairro);
        }

        // Filtro por dia da semana — filtra igrejas que tenham pelo menos
        // um horário de missa no dia selecionado
        if ($request->filled('dia_semana')) {
            $query->whereHas('horarioMissas', function ($q) use ($request) {
                $q->where('dia_semana', $request->dia_semana);
            });
        }

        $igrejas = $query->get();

        // Busca lista de bairros únicos para popular o dropdown de filtro
        $bairros = Igreja::whereHas('paroquia', function ($q) {
            $q->where('status', 'aprovada');
        })->distinct()->pluck('bairro');

        return view('publico.index', compact('igrejas', 'bairros'));
    }
}
