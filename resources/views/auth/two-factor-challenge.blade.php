@extends('auth.layout', ['title' => 'Verificação em duas etapas'])

@section('content')
    <h2>Verificação de segurança</h2><p class="subtitle">Digite o código do seu aplicativo autenticador ou use um código de recuperação.</p>
    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf
        <div class="field"><label for="code">Código de autenticação</label><input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" autofocus placeholder="000 000">@error('code')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="recovery_code">Código de recuperação <span style="font-weight:400;color:#68738a">(alternativo)</span></label><input id="recovery_code" type="text" name="recovery_code" autocomplete="one-time-code" placeholder="Código de recuperação">@error('recovery_code')<span class="field-error">{{ $message }}</span>@enderror</div>
        <button type="submit">Continuar</button>
    </form>
    <p class="auth-footer"><a href="{{ route('login') }}">Voltar para o login</a></p>
@endsection
