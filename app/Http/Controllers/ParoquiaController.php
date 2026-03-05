<?php

namespace App\Http\Controllers;

use App\Models\HorarioMissa;
use App\Models\Igreja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParoquiaController extends Controller
{
    /**
     * GET /paroquia/dashboard
     * Exibe o painel da paróquia logada com suas igrejas e horários.
     * A paróquia só pode ver e gerenciar seus próprios dados.
     */
    public function dashboard()
    {
        $paroquia = Auth::user()->paroquia;
        $igrejas = $paroquia->igrejas()->with('horarioMissas')->get();

        return view('paroquia.dashboard', compact('paroquia', 'igrejas'));
    }

    // ==================== CRUD DE IGREJAS ====================

    /**
     * POST /paroquia/igrejas
     * Cadastra uma nova igreja vinculada à paróquia logada.
     * Os dados obrigatórios são: nome, bairro e endereço.
     */
    public function criarIgreja(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
        ]);

        // Cria a igreja automaticamente vinculada à paróquia do usuário logado
        Auth::user()->paroquia->igrejas()->create($request->only('nome', 'bairro', 'endereco'));

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Igreja cadastrada com sucesso!');
    }

    /**
     * PUT /paroquia/igrejas/{igreja}
     * Atualiza os dados de uma igreja existente.
     * Verifica se a igreja pertence à paróquia do usuário logado antes de editar.
     */
    public function editarIgreja(Request $request, Igreja $igreja)
    {
        // Garante que a paróquia só edita suas próprias igrejas
        $this->autorizarIgreja($igreja);

        $request->validate([
            'nome' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
        ]);

        $igreja->update($request->only('nome', 'bairro', 'endereco'));

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Igreja atualizada com sucesso!');
    }

    /**
     * DELETE /paroquia/igrejas/{igreja}
     * Remove uma igreja e todos os seus horários (cascadeOnDelete na FK).
     * Verifica autorização antes de excluir.
     */
    public function excluirIgreja(Igreja $igreja)
    {
        $this->autorizarIgreja($igreja);

        $igreja->delete();

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Igreja removida com sucesso!');
    }

    // ==================== CRUD DE HORÁRIOS DE MISSA ====================

    /**
     * POST /paroquia/igrejas/{igreja}/horarios
     * Adiciona um novo horário de missa a uma igreja.
     * Campos obrigatórios: dia_semana (ex: 'domingo') e horario (ex: '08:00').
     */
    public function criarHorario(Request $request, Igreja $igreja)
    {
        $this->autorizarIgreja($igreja);

        $request->validate([
            'dia_semana' => 'required|string',
            'horario' => 'required|date_format:H:i',
        ]);

        // Cria o horário vinculado à igreja via relacionamento
        $igreja->horarioMissas()->create($request->only('dia_semana', 'horario'));

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Horário adicionado com sucesso!');
    }

    /**
     * PUT /paroquia/horarios/{horario}
     * Edita um horário de missa existente.
     * Verifica se o horário pertence a uma igreja da paróquia logada.
     */
    public function editarHorario(Request $request, HorarioMissa $horario)
    {
        $this->autorizarHorario($horario);

        $request->validate([
            'dia_semana' => 'required|string',
            'horario' => 'required|date_format:H:i',
        ]);

        $horario->update($request->only('dia_semana', 'horario'));

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Horário atualizado com sucesso!');
    }

    /**
     * DELETE /paroquia/horarios/{horario}
     * Remove um horário de missa.
     * Verifica autorização antes de excluir.
     */
    public function excluirHorario(HorarioMissa $horario)
    {
        $this->autorizarHorario($horario);

        $horario->delete();

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Horário removido com sucesso!');
    }

    // ==================== MÉTODOS DE AUTORIZAÇÃO ====================
    //
    // DEFESA EM PROFUNDIDADE: Embora o dashboard (frontend) só exiba as
    // igrejas/horários da própria paróquia, essas verificações existem como
    // camada extra de segurança no backend. Um usuário mal-intencionado
    // poderia manipular IDs via DevTools, Postman ou URL direta para tentar
    // editar dados de outra paróquia. Esses métodos bloqueiam isso com 403.
    //

    /**
     * Verifica se a igreja pertence à paróquia do usuário logado.
     * Retorna erro 403 (Forbidden) se não pertencer.
     * Impede que uma paróquia edite/exclua igrejas de outra.
     */
    private function autorizarIgreja(Igreja $igreja): void
    {
        if ($igreja->paroquia_id !== Auth::user()->paroquia_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    /**
     * Verifica se o horário pertence a uma igreja da paróquia do usuário logado.
     * Navega a relação: horário → igreja → paroquia_id.
     * Retorna erro 403 (Forbidden) se não pertencer.
     */
    private function autorizarHorario(HorarioMissa $horario): void
    {
        if ($horario->igreja->paroquia_id !== Auth::user()->paroquia_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }
}
