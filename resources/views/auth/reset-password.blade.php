@extends('auth.layout', ['title' => 'Redefinir senha'])

@section('content')
    <a class="back-link" href="{{ route('login') }}">← Voltar para o login</a>
    <h2>Redefina sua senha</h2><p class="subtitle">Escolha uma nova senha para recuperar o acesso à sua conta.</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email" placeholder="voce@exemplo.com">@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Nova senha</label><input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Digite sua nova senha">@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password_confirmation">Confirmar nova senha</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita sua nova senha"></div>
        <button type="submit">Redefinir senha</button>
    </form>
@endsection
