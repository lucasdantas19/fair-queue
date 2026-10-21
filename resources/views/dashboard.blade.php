<h1>Olá, {{ auth()->user()->name }}</h1>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Sair</button>
</form>
