@extends('auth.layout', ['title' => 'Criar conta'])

@section('content')
    <a class="back-link" href="{{ route('login') }}">← Voltar para o login</a>
    <h2>Crie sua conta</h2><p class="subtitle">Comece a organizar seu fluxo em poucos passos.</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field"><label for="name">Nome</label><input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Seu nome">@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="voce@exemplo.com">@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Senha</label><input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Crie uma senha segura">@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password_confirmation">Confirmar senha</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita sua senha">@error('password_confirmation')<span class="field-error">{{ $message }}</span>@enderror</div>
        <button type="submit">Criar minha conta</button>
    </form>
@endsection
