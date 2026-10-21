@extends('auth.layout', ['title' => 'Recuperar senha'])

@section('content')
    <a class="back-link" href="{{ route('login') }}">← Voltar para o login</a>
    <h2>Esqueceu sua senha?</h2><p class="subtitle">Informe seu e-mail e enviaremos um link para você criar uma nova senha.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="voce@exemplo.com">@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        <button type="submit">Enviar link de recuperação</button>
    </form>
@endsection
