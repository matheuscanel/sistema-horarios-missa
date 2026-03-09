<h1>Login</h1>
<form method="POST" action="/login">
    @csrf
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Senha"><br>
    <button type="submit">Entrar</button>
</form>
@if($errors->any())
    <p style="color:red">{{ $errors->first() }}</p>
@endif
