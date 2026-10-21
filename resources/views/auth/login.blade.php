@extends('auth.layout', ['title' => 'Entrar'])

@section('content')
    <h2>Bem-vindo de volta</h2>
    <p class="subtitle">Entre para continuar acompanhando suas filas.</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="voce@exemplo.com">@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Senha</label><input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha">@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="form-row"><label class="check"><input type="checkbox" name="remember"> Lembrar de mim</label><a href="{{ route('password.request') }}">Esqueci a senha</a></div>
        <button type="submit">Entrar</button>
    </form>
    <p class="auth-footer">Ainda não tem uma conta? <a href="{{ route('register') }}">Criar conta</a></p>
@endsection
