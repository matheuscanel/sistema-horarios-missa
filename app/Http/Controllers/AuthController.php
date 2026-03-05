<?php

namespace App\Http\Controllers;

use App\Models\Paroquia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * GET /login
     * Exibe o formulário de login.
     * Acessível apenas para visitantes (middleware 'guest' na rota).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * POST /login
     * Processa o login do usuário (admin ou paróquia).
     * Valida email e senha, autentica e redireciona:
     *   - Admin → painel do administrador
     *   - Paróquia → dashboard da paróquia
     * Se as credenciais forem inválidas, volta pro form com erro.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt verifica email/senha no banco e faz login automático
        if (Auth::attempt($credentials)) {
            // Regenera a sessão para prevenir session fixation attacks
            $request->session()->regenerate();

            $user = Auth::user();

            // Redireciona baseado no tipo do usuário
            if ($user->isAdmin()) {
                return redirect()->route('admin.index');
            }

            return redirect()->route('paroquia.dashboard');
        }

        // Login falhou — volta com mensagem de erro mantendo o email digitado
        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    /**
     * GET /registro
     * Exibe o formulário de cadastro de nova paróquia.
     * Acessível apenas para visitantes (middleware 'guest' na rota).
     */
    public function showRegistroForm()
    {
        return view('auth.registro');
    }

    /**
     * POST /registro
     * Processa o cadastro de uma nova paróquia.
     * Cria duas coisas em sequência:
     *   1. A paróquia (com status 'pendente', aguardando aprovação do admin)
     *   2. O usuário vinculado à paróquia (tipo 'paroquia')
     * Após o cadastro, faz login automático e redireciona pro dashboard.
     */
    public function registro(Request $request)
    {
        $request->validate([
            'nome_paroquia' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // precisa de campo 'password_confirmation'
        ]);

        // Cria a paróquia com status pendente
        $paroquia = Paroquia::create([
            'nome' => $request->nome_paroquia,
            'status' => 'pendente',
        ]);

        // Cria o usuário vinculado à paróquia
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'paroquia',
            'paroquia_id' => $paroquia->id,
        ]);

        // Faz login automático após cadastro
        Auth::login($user);

        return redirect()->route('paroquia.dashboard')
            ->with('mensagem', 'Cadastro realizado! Aguarde aprovação do administrador.');
    }

    /**
     * POST /logout
     * Encerra a sessão do usuário e redireciona para a página pública.
     * Invalida a sessão e regenera o token CSRF por segurança.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('publico.index');
    }
}
