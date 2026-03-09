<h1>Painel Admin</h1>

<h2>Paróquias Pendentes ({{ count($pendentes) }})</h2>
@foreach($pendentes as $p)
    <p>{{ $p->nome }} - 
        <form style="display:inline" method="POST" action="/admin/paroquias/{{ $p->id }}/aprovar">@csrf @method('PATCH') <button>Aprovar</button></form>
        <form style="display:inline" method="POST" action="/admin/paroquias/{{ $p->id }}/rejeitar">@csrf @method('PATCH') <button>Rejeitar</button></form>
    </p>
@endforeach

<h2>Paróquias Aprovadas ({{ count($aprovadas) }})</h2>
@foreach($aprovadas as $p)
    <p>{{ $p->nome }}</p>
@endforeach

@if(session('mensagem'))
    <p style="color:green">{{ session('mensagem') }}</p>
@endif

<form method="POST" action="/logout">@csrf <button>Sair</button></form>
