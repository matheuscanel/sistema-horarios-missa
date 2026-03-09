<h1>Registro de Paróquia</h1>
<form method="POST" action="/registro">
    @csrf
    <input type="text" name="nome_paroquia" placeholder="Nome da Paróquia"><br>
    <input type="text" name="name" placeholder="Seu Nome"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Senha"><br>
    <input type="password" name="password_confirmation" placeholder="Confirmar Senha"><br>
    <button type="submit">Cadastrar</button>
</form>
@if($errors->any())
    <ul style="color:red">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
@endif
