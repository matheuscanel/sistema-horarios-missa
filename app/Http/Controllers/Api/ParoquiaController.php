<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HorarioMissa;
use App\Models\Igreja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParoquiaController extends Controller
{
    public function dashboard()
    {
        $paroquia = Auth::user()->paroquia;
        $igrejas = $paroquia->igrejas()->with('horarioMissas')->get();

        return response()->json([
            'paroquia' => $paroquia,
            'igrejas' => $igrejas,
        ]);
    }

    public function criarIgreja(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
        ]);

        $igreja = Auth::user()->paroquia->igrejas()->create(
            $request->only('nome', 'bairro', 'endereco')
        );

        return response()->json([
            'mensagem' => 'Igreja cadastrada com sucesso!',
            'igreja' => $igreja,
        ], 201);
    }

    public function editarIgreja(Request $request, Igreja $igreja)
    {
        $this->autorizarIgreja($igreja);
        $igreja->update($request->only('nome', 'bairro', 'endereco'));
        return response()->json(['mensagem' => 'Igreja atualizada.', 'igreja' => $igreja]);
    }

    public function excluirIgreja(Igreja $igreja)
    {
        $this->autorizarIgreja($igreja);
        $igreja->delete();
        return response()->json(['mensagem' => 'Igreja removida.']);
    }

    public function criarHorario(Request $request, Igreja $igreja)
    {
        $this->autorizarIgreja($igreja);
        $request->validate(['dia_semana' => 'required', 'horario' => 'required']);
        $horario = $igreja->horarioMissas()->create($request->only('dia_semana', 'horario'));
        return response()->json(['mensagem' => 'Horário adicionado.', 'horario' => $horario], 201);
    }

    public function excluirHorario(HorarioMissa $horario)
    {
        $this->autorizarHorario($horario);
        $horario->delete();
        return response()->json(['mensagem' => 'Horário removido.']);
    }

    private function autorizarIgreja(Igreja $igreja)
    {
        if ($igreja->paroquia_id !== Auth::user()->paroquia_id) abort(403);
    }

    private function autorizarHorario(HorarioMissa $horario)
    {
        if ($horario->igreja->paroquia_id !== Auth::user()->paroquia_id) abort(403);
    }
}
