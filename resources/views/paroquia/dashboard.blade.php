<h1>Dashboard da Paróquia: {{ $paroquia->nome }}</h1>
<p>Status: {{ $paroquia->status }}</p>
<p>Igrejas cadastradas: {{ count($igrejas) }}</p>

@if(session('mensagem'))
    <p style="color:green">{{ session('mensagem') }}</p>
@endif

<form method="POST" action="/logout">@csrf <button>Sair</button></form>
