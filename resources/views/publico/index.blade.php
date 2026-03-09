<h1>Página Pública - Horários de Missa</h1>

<nav style="margin-bottom: 20px; padding: 10px; background: #f4f4f4;">
    @auth
        Logado como: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->tipo }}) | 
        <a href="{{ auth()->user()->isAdmin() ? route('admin.index') : route('paroquia.dashboard') }}">Ir para Painel</a> |
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit">Sair</button>
        </form>
    @else
        <a href="{{ route('login') }}">Entrar</a> | 
        <a href="{{ route('registro') }}">Criar Conta</a>
    @endauth
</nav>

<p>Igrejas encontradas: {{ count($igrejas) }}</p>
<p>Bairros disponíveis: {{ $bairros->implode(', ') }}</p>
