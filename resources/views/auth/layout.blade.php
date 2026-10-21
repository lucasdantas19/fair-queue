<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Acesso' }} · Fair Queue</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; color: #172033; background: #f6f8fc; }
        a { color: #3763f4; text-decoration: none; font-weight: 600; }
        a:hover { color: #2749c7; }
        .auth-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(420px, .95fr); }
        .auth-hero { display: flex; align-items: center; padding: clamp(2rem, 7vw, 7rem); color: #fff; background: #13203b; position: relative; overflow: hidden; }
        .auth-hero::before, .auth-hero::after { content: ""; position: absolute; border-radius: 999px; background: #3763f4; opacity: .2; }
        .auth-hero::before { width: 34rem; height: 34rem; right: -13rem; top: -12rem; }
        .auth-hero::after { width: 25rem; height: 25rem; left: -16rem; bottom: -14rem; }
        .hero-content { max-width: 560px; position: relative; z-index: 1; }
        .brand { display: inline-flex; align-items: center; gap: .7rem; color: inherit; font-weight: 800; letter-spacing: -.03em; font-size: 1.15rem; }
        .brand-mark { display: grid; place-items: center; width: 2.6rem; height: 2.6rem; border-radius: .8rem; background: #fff; color: #3763f4; font-size: 1.3rem; box-shadow: 0 8px 25px #08112755; }
        .hero-content h1 { margin: 4rem 0 1.2rem; max-width: 500px; font-size: clamp(2.5rem, 5vw, 4.8rem); line-height: 1.02; letter-spacing: -.06em; }
        .hero-content p { max-width: 430px; color: #b9c6e3; font-size: 1.1rem; line-height: 1.7; }
        .auth-panel { display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { width: min(100%, 440px); padding: clamp(1.7rem, 4vw, 3rem); background: #fff; border: 1px solid #e3e8f2; border-radius: 1.4rem; box-shadow: 0 20px 60px #18284b12; }
        .auth-card h2 { margin: 0 0 .6rem; color: #172033; font-size: 1.8rem; letter-spacing: -.04em; }
        .subtitle { margin: 0 0 2rem; color: #68738a; line-height: 1.6; }
        .field { margin-bottom: 1.15rem; }
        label { display: block; margin-bottom: .5rem; color: #34405a; font-size: .88rem; font-weight: 700; }
        input { width: 100%; padding: .85rem 1rem; color: #172033; background: #fbfcfe; border: 1px solid #d9e0ec; border-radius: .7rem; outline: 0; transition: border-color .2s, box-shadow .2s; }
        input:focus { border-color: #3763f4; box-shadow: 0 0 0 4px #3763f41c; }
        .field-error { display: block; margin-top: .4rem; color: #c33c52; font-size: .82rem; }
        .form-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin: .25rem 0 1.5rem; }
        .check { display: inline-flex; align-items: center; gap: .5rem; color: #68738a; font-size: .88rem; }
        .check input { width: 1rem; height: 1rem; accent-color: #3763f4; }
        button { width: 100%; padding: .9rem 1rem; color: #fff; background: #3763f4; border: 0; border-radius: .7rem; font-weight: 750; cursor: pointer; box-shadow: 0 8px 18px #3763f43b; transition: transform .2s, background .2s; }
        button:hover { background: #2749c7; transform: translateY(-1px); }
        .auth-footer { margin: 1.6rem 0 0; color: #68738a; text-align: center; font-size: .9rem; }
        .alert { margin-bottom: 1.25rem; padding: .8rem 1rem; color: #23634a; background: #eaf8f0; border: 1px solid #bce8ce; border-radius: .7rem; font-size: .9rem; line-height: 1.5; }
        .alert-error { color: #9e3045; background: #fff0f2; border-color: #f5c5ce; }
        .back-link { display: inline-block; margin-bottom: 1.8rem; font-size: .88rem; }
        @media (max-width: 820px) { .auth-shell { display: block; } .auth-hero { min-height: 235px; padding: 2rem; } .hero-content h1 { margin: 2.2rem 0 .5rem; font-size: 2.2rem; } .hero-content p { display: none; } .auth-panel { min-height: calc(100vh - 235px); padding: 1.25rem; } }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="auth-hero" aria-label="Fair Queue">
            <div class="hero-content">
                <a class="brand" href="{{ url('/') }}"><span class="brand-mark">FQ</span> Fair Queue</a>
                <h1>Filas justas. Fluxos mais leves.</h1>
                <p>Organize seu trabalho com clareza e mantenha cada tarefa avançando no ritmo certo.</p>
            </div>
        </section>
        <section class="auth-panel">
            <div class="auth-card">
                @if (session('status')) <div class="alert">{{ session('status') }}</div> @endif
                @if ($errors->any()) <div class="alert alert-error">Confira os campos destacados e tente novamente.</div> @endif
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
