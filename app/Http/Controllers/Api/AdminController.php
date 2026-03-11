<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paroquia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $pendentes = Paroquia::where('status', 'pendente')->get();
        $aprovadas = Paroquia::where('status', 'aprovada')->get();
        $rejeitadas = Paroquia::where('status', 'rejeitada')->get();

        return response()->json([
            'pendentes' => $pendentes,
            'aprovadas' => $aprovadas,
            'rejeitadas' => $rejeitadas,
        ]);
    }

    public function aprovar(Paroquia $paroquia)
    {
        $paroquia->update(['status' => 'aprovada']);
        return response()->json(['mensagem' => 'Paróquia aprovada com sucesso!']);
    }

    public function rejeitar(Paroquia $paroquia)
    {
        $paroquia->update(['status' => 'rejeitada']);
        return response()->json(['mensagem' => 'Paróquia rejeitada.']);
    }

    public function remover(Paroquia $paroquia)
    {
        // Remove igrejas e horários associados
        foreach ($paroquia->igrejas as $igreja) {
            $igreja->horarioMissas()->delete();
            $igreja->delete();
        }

        // Desvincula o usuário da paróquia (se existir)
        if ($paroquia->user) {
            $paroquia->user->update(['paroquia_id' => null]);
        }

        $paroquia->delete();

        return response()->json(['mensagem' => 'Paróquia removida com sucesso!']);
    }
}
