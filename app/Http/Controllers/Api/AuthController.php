<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paroquia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! \Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tipo' => $user->tipo,
                'paroquia_id' => $user->paroquia_id,
            ],
            'token' => $token,
        ]);
    }

    public function registro(Request $request)
    {
        $request->validate([
            'nome_paroquia' => 'required|string|max:255|unique:paroquias,nome',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nome_paroquia.required' => 'O nome da paróquia é obrigatório.',
            'nome_paroquia.unique' => 'Já existe uma paróquia cadastrada com esse nome.',
            'name.required' => 'O nome do responsável é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        $paroquia = Paroquia::create([
            'nome' => $request->nome_paroquia,
            'status' => 'pendente',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'paroquia',
            'paroquia_id' => $paroquia->id,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tipo' => $user->tipo,
                'paroquia_id' => $user->paroquia_id,
            ],
            'token' => $token,
            'mensagem' => 'Cadastro realizado! Aguarde aprovação do administrador.',
        ], 201);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tipo' => $user->tipo,
            'paroquia_id' => $user->paroquia_id,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['mensagem' => 'Logout realizado com sucesso.']);
    }
}
