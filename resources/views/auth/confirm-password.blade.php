@extends('auth.layout', ['title' => 'Confirmar senha'])

@section('content')
    <h2>Confirme sua senha</h2><p class="subtitle">Por segurança, confirme sua senha para continuar.</p>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="field"><label for="password">Senha</label><input id="password" type="password" name="password" required autofocus autocomplete="current-password" placeholder="Digite sua senha">@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
        <button type="submit">Confirmar senha</button>
    </form>
@endsection
