<?php

namespace App\Http\Controllers;

use App\Models\Paroquia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * GET /admin
     * Painel do administrador — lista as paróquias pendentes (aguardando aprovação)
     * e as paróquias já aprovadas.
     * Apenas usuários com tipo 'admin' devem acessar.
     */
    public function index()
    {
        $pendentes = Paroquia::where('status', 'pendente')->get();
        $aprovadas = Paroquia::where('status', 'aprovada')->get();

        return view('admin.index', compact('pendentes', 'aprovadas'));
    }

    /**
     * PATCH /admin/paroquias/{paroquia}/aprovar
     * Aprova uma paróquia — muda o status para 'aprovada'.
     * Após aprovação, as igrejas da paróquia passam a aparecer na área pública.
     */
    public function aprovar(Paroquia $paroquia)
    {
        $paroquia->update(['status' => 'aprovada']);

        return redirect()->route('admin.index')
            ->with('mensagem', 'Paróquia aprovada com sucesso!');
    }

    /**
     * PATCH /admin/paroquias/{paroquia}/rejeitar
     * Rejeita uma paróquia — muda o status para 'rejeitada'.
     * A paróquia rejeitada não aparece na área pública.
     */
    public function rejeitar(Paroquia $paroquia)
    {
        $paroquia->update(['status' => 'rejeitada']);

        return redirect()->route('admin.index')
            ->with('mensagem', 'Paróquia rejeitada.');
    }
}
