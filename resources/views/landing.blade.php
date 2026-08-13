<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobiFone WorkHub - Trung tâm điều hành công việc</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        :root {
            --mf-navy: #001F5B;
            --mf-blue: #003DA5;
            --mf-blue-2: #0057C8;
            --mf-red: #E4002B;
            --mf-sky: #E8F0FE;
            --mf-line: #D8E4F5;
            --mf-text: #10233F;
            --mf-muted: #64748B;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Be Vietnam Pro', sans-serif;
            color: var(--mf-text);
            background: #F4F8FE;
        }

        .site-shell { min-height: 100vh; overflow-x: hidden; }
        .container { width: min(1180px, calc(100% - 36px)); margin: 0 auto; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid rgba(216, 228, 245, .9);
            backdrop-filter: blur(16px);
        }
        .topbar-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-mark {
            position: relative;
            overflow: hidden;
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: #fff;
            color: var(--mf-blue);
            font-size: 18px;
            font-weight: 900;
            box-shadow: inset 5px 0 0 var(--mf-red), 0 10px 22px rgba(0, 31, 91, .10);
            transform-origin: center;
            animation: logoMarkPulse 3.6s ease-in-out infinite;
        }
        .brand-mark::after {
            content: "";
            position: absolute;
            inset: -35% auto -35% -65%;
            width: 42%;
            background: linear-gradient(90deg, transparent, rgba(0,61,165,.20), transparent);
            transform: rotate(16deg);
            animation: logoMarkSweep 4.8s ease-in-out infinite;
        }
        .brand-word {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: baseline;
            padding: 5px 11px;
            border-radius: 7px;
            background: #fff;
            border: 1px solid rgba(216, 228, 245, .9);
            box-shadow: 0 10px 22px rgba(0, 31, 91, .08);
            line-height: 1;
        }
        .brand-word::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: -40%;
            width: 34%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.92), transparent);
            transform: skewX(-18deg);
            animation: logoWordShine 5.2s ease-in-out infinite;
        }
        .brand-word strong { position: relative; z-index: 1; display: inline-flex; align-items: baseline; font-size: 20px; letter-spacing: -.04em; }
        .brand-word .blue { color: var(--mf-blue); animation: logoBlueTone 4.6s ease-in-out infinite; }
        .brand-word .red { color: var(--mf-red); animation: logoRedTone 4.6s ease-in-out infinite; }
        .brand-letter {
            display: inline-block;
            transform-origin: 50% 80%;
            animation: logoLetterWave 2.8s ease-in-out infinite;
        }
        .brand-letter:nth-child(2) { animation-delay: .08s; }
        .brand-letter:nth-child(3) { animation-delay: .16s; }
        .brand-letter:nth-child(4) { animation-delay: .24s; }
        .brand-word .red .brand-letter:nth-child(1) { animation-delay: .34s; }
        .brand-word .red .brand-letter:nth-child(2) { animation-delay: .42s; }
        .brand-word .red .brand-letter:nth-child(3) { animation-delay: .50s; }
        .brand-word .red .brand-letter:nth-child(4) { animation-delay: .58s; }
        .brand-sub { margin-top: 5px; color: var(--mf-blue); font-size: 10px; font-weight: 900; letter-spacing: .18em; animation: logoSubTrack 4.8s ease-in-out infinite; }

        .nav { display: flex; align-items: center; gap: 6px; }
        .nav a {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 12px;
            border-radius: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
        }
        .nav a::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 6px;
            height: 2px;
            border-radius: 999px;
            background: var(--mf-red);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .18s ease;
        }
        .nav a:hover::after { transform: scaleX(1); }
        .nav a:hover { color: var(--mf-blue); background: var(--mf-sky); }
        .nav .login-btn {
            margin-left: 4px;
            color: #fff;
            background: var(--mf-blue);
            padding: 0 16px;
        }
        .nav .login-btn:hover { color: #fff; background: var(--mf-blue-2); }

        .hero {
            position: relative;
            min-height: calc(100svh - 118px);
            display: flex;
            align-items: center;
            isolation: isolate;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(0, 31, 91, .97) 0%, rgba(0, 61, 165, .90) 50%, rgba(0, 31, 91, .76) 100%),
                linear-gradient(135deg, #001F5B 0%, #003DA5 100%);
            color: #fff;
        }
        .hero::before {
            content: "";
            position: absolute;
            inset: -18% -8%;
            z-index: -1;
            pointer-events: none;
            background:
                linear-gradient(116deg, transparent 0 34%, rgba(255,255,255,.14) 42%, transparent 50%),
                radial-gradient(circle at 70% 46%, rgba(11,102,216,.34), transparent 28%);
            mix-blend-mode: screen;
            opacity: .78;
            animation: heroLightSweep 8.5s ease-in-out infinite;
        }
        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background: repeating-linear-gradient(180deg, rgba(255,255,255,.045) 0 1px, transparent 1px 7px);
            opacity: .18;
            animation: filmScan 1.6s linear infinite;
        }
        .ops-wall {
            position: absolute;
            inset: 0;
            z-index: -1;
            opacity: .48;
            background-image:
                linear-gradient(rgba(255,255,255,.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 42px 42px;
            animation: gridDrift 18s linear infinite;
        }
        .ops-radar {
            position: absolute;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }
        .signal-dot {
            position: absolute;
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 0 0 6px rgba(255,255,255,.12), 0 0 28px rgba(255,255,255,.7);
            opacity: .72;
            animation: signalFloat 8s ease-in-out infinite;
        }
        .signal-dot.red { background: var(--mf-red); box-shadow: 0 0 0 6px rgba(228,0,43,.16), 0 0 32px rgba(228,0,43,.65); }
        .signal-dot.green { background: #22C55E; box-shadow: 0 0 0 6px rgba(34,197,94,.16), 0 0 32px rgba(34,197,94,.62); }
        .signal-dot:nth-child(1) { left: 12%; top: 22%; animation-delay: 0s; }
        .signal-dot:nth-child(2) { left: 46%; top: 16%; animation-delay: 1.4s; }
        .signal-dot:nth-child(3) { left: 74%; top: 30%; animation-delay: 2.1s; }
        .signal-dot:nth-child(4) { left: 22%; top: 72%; animation-delay: 3.2s; }
        .signal-dot:nth-child(5) { left: 82%; top: 72%; animation-delay: 4.4s; }
        .ops-panel {
            position: absolute;
            right: max(18px, calc((100vw - 1180px) / 2));
            top: 52%;
            width: min(520px, 44vw);
            transform: translateY(-50%);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 8px;
            background: rgba(255,255,255,.10);
            box-shadow: 0 30px 80px rgba(0,0,0,.22);
            backdrop-filter: blur(12px);
            overflow: hidden;
            animation: panelFloat 7s ease-in-out infinite, panelGlow 4.8s ease-in-out infinite;
        }
        .ops-panel::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background:
                radial-gradient(circle at var(--pulse-x, 76%) 28%, rgba(34,197,94,.28), transparent 19%),
                linear-gradient(120deg, transparent 0 28%, rgba(255,255,255,.12) 42%, transparent 58%);
            opacity: .78;
            pointer-events: none;
            animation: panelPulsePoint 5.6s ease-in-out infinite;
        }
        .ops-panel::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -40%;
            height: 38%;
            background: linear-gradient(180deg, transparent 0%, rgba(255,255,255,.16) 48%, transparent 100%);
            transform: skewY(-8deg);
            animation: scanPanel 5.5s ease-in-out infinite;
            pointer-events: none;
        }
        .ops-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,.14);
        }
        .ops-title { font-size: 12px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: #D7E7FF; }
        .ops-live { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 900; color: #BBF7D0; }
        .ops-live::before { content: ""; width: 8px; height: 8px; border-radius: 999px; background: #22C55E; }
        .ops-body { padding: 14px; display: grid; gap: 10px; }
        .ops-row {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 34px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            background: rgba(255,255,255,.10);
            animation: rowPulse 6s ease-in-out infinite;
        }
        .ops-row::after {
            content: "";
            position: absolute;
            left: 56px;
            right: 18px;
            bottom: 0;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, #22C55E, #D7E7FF, transparent);
            transform: translateX(-105%);
            animation: rowDataSweep 3.8s ease-in-out infinite;
        }
        .ops-row:nth-child(2) { animation-delay: 1.2s; }
        .ops-row:nth-child(2)::after { animation-delay: .7s; background: linear-gradient(90deg, transparent, #0EA5E9, #D7E7FF, transparent); }
        .ops-row:nth-child(3) { animation-delay: 2.4s; }
        .ops-row:nth-child(3)::after { animation-delay: 1.4s; background: linear-gradient(90deg, transparent, #E4002B, #FFD0D8, transparent); }
        .ops-icon { width: 34px; height: 34px; border-radius: 8px; display: grid; place-items: center; background: #fff; color: var(--mf-blue); box-shadow: inset 4px 0 0 var(--mf-red); }
        .ops-row strong { display: block; color: #fff; font-size: 13px; line-height: 1.25; }
        .ops-row span { display: block; margin-top: 3px; color: #BFD8FF; font-size: 11px; }
        .ops-badge { min-width: 58px; padding: 6px 8px; border-radius: 999px; color: #fff; background: rgba(228,0,43,.92); font-size: 11px; font-weight: 900; text-align: center; }
        .ops-badge { animation: badgeBeat 2.8s ease-in-out infinite; }
        .ops-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 0 14px 14px; }
        .ops-metric { padding: 12px; border-radius: 8px; background: rgba(255,255,255,.12); }
        .ops-metric:nth-child(1) { animation: metricBreathe 4.2s ease-in-out infinite; }
        .ops-metric:nth-child(2) { animation: metricBreathe 4.2s .7s ease-in-out infinite; }
        .ops-metric:nth-child(3) { animation: metricBreathe 4.2s 1.4s ease-in-out infinite; }
        .ops-metric b { display: block; font-size: 22px; line-height: 1; }
        .ops-metric span { display: block; margin-top: 6px; color: #BFD8FF; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .ops-timeline {
            position: relative;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 0 14px 14px;
        }
        .ops-timeline::before {
            content: "";
            position: absolute;
            left: 24px;
            right: 24px;
            top: 8px;
            height: 2px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
        }
        .ops-step {
            position: relative;
            min-height: 28px;
            color: #BFD8FF;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            padding-top: 18px;
        }
        .ops-step::before {
            content: "";
            position: absolute;
            top: 4px;
            left: 50%;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #D7E7FF;
            transform: translateX(-50%);
            box-shadow: 0 0 0 5px rgba(255,255,255,.10);
            animation: timelineNode 3.6s ease-in-out infinite;
        }
        .ops-step:nth-child(2)::before { animation-delay: .55s; }
        .ops-step:nth-child(3)::before { animation-delay: 1.1s; background: #22C55E; }
        .ops-step:nth-child(4)::before { animation-delay: 1.65s; background: var(--mf-red); }

        .hero-copy { width: min(720px, 62vw); padding: 76px 0; }
        .hero-copy > * { animation: heroItemIn .72s ease both; }
        .hero-copy > *:nth-child(2) { animation-delay: .08s; }
        .hero-copy > *:nth-child(3) { animation-delay: .16s; }
        .hero-copy > *:nth-child(4) { animation-delay: .24s; }
        .hero-copy > *:nth-child(5) { animation-delay: .32s; }
        .eyebrow { color: #FFD0D8; font-size: 12px; font-weight: 900; letter-spacing: .18em; text-transform: uppercase; }
        h1 {
            margin: 16px 0 0;
            max-width: 760px;
            color: #fff;
            font-size: clamp(36px, 6vw, 76px);
            line-height: 1.16;
            letter-spacing: 0;
            font-weight: 900;
        }
        .hero-headline { display: grid; gap: 0; }
        .headline-line {
            display: block;
            width: fit-content;
            overflow: hidden;
            padding: .10em 0 .20em;
            margin: -.10em 0 -.12em;
        }
        .headline-line > span {
            display: inline-block;
            animation: headlineReveal .82s cubic-bezier(.2,.8,.2,1) both;
        }
        .headline-line:nth-child(2) > span { animation-delay: .18s; }
        .headline-line:nth-child(3) > span { animation-delay: .36s; }
        .headline-accent {
            position: relative;
            display: inline-block;
            padding: 0 .10em .04em;
            color: #fff;
            isolation: isolate;
            animation: headlineAccentLift 3.8s ease-in-out infinite;
        }
        .headline-accent::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -.02em;
            height: .28em;
            z-index: -1;
            border-radius: 6px;
            background: linear-gradient(90deg, var(--mf-red), #FF4B6A, var(--mf-blue-2));
            background-size: 220% 100%;
            animation: headlineAccentSweep 4.2s ease-in-out infinite;
        }
        .headline-accent::after {
            content: "";
            display: inline-block;
            width: .08em;
            height: .78em;
            margin-left: .08em;
            border-radius: 999px;
            background: #FFD0D8;
            vertical-align: -.08em;
            animation: headlineCaret 1.1s steps(2, end) infinite;
        }
        .headline-soft {
            color: #D7E7FF;
            text-shadow: 0 0 22px rgba(255,255,255,.16);
            animation: headlineSoftGlow 4s ease-in-out infinite;
            padding-bottom: .06em;
        }
        .hero-desc {
            margin: 22px 0 0;
            max-width: 640px;
            color: #D7E7FF;
            font-size: clamp(16px, 2vw, 19px);
            line-height: 1.75;
        }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            transition: transform .16s ease, background .16s ease, border-color .16s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--mf-red); color: #fff; }
        .btn-primary:hover { background: #C70025; }
        .btn-secondary { color: #fff; border: 1px solid rgba(255,255,255,.30); background: rgba(255,255,255,.10); }
        .btn-secondary:hover { background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.44); }
        .hero-proof { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 26px; color: #BFD8FF; font-size: 12px; font-weight: 800; }
        .hero-proof span { display: inline-flex; align-items: center; gap: 7px; }
        .hero-proof i { color: #BBF7D0; }

        .activity-ticker {
            border-top: 1px solid rgba(255,255,255,.15);
            border-bottom: 1px solid rgba(255,255,255,.15);
            background: rgba(255,255,255,.07);
            overflow: hidden;
        }
        .ticker-track {
            display: flex;
            width: max-content;
            animation: tickerMove 28s linear infinite;
        }
        .ticker-item {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 48px;
            padding: 0 24px;
            color: #D7E7FF;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }
        .ticker-item i { width: 15px; height: 15px; color: #FFD0D8; }
        .ticker-dot { width: 6px; height: 6px; border-radius: 999px; background: #22C55E; box-shadow: 0 0 0 5px rgba(34,197,94,.14); }

        .section { padding: 64px 0; }
        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .58s ease, transform .58s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }
        .section-head { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
        .section-kicker { color: var(--mf-red); font-size: 11px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
        h2 { margin: 8px 0 0; color: var(--mf-navy); font-size: clamp(26px, 4vw, 40px); line-height: 1.14; font-weight: 900; }
        .section-note { max-width: 470px; color: var(--mf-muted); font-size: 14px; line-height: 1.7; }

        .role-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .role-grid::before {
            content: "";
            position: absolute;
            left: 8%;
            right: 8%;
            top: 38%;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(0,61,165,.35), rgba(228,0,43,.38), transparent);
            transform-origin: left;
            animation: roleFlow 5.2s ease-in-out infinite;
        }
        .role-card {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            min-height: 360px;
            border: 1px solid rgba(185,205,245,.9);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(0,31,91,.07);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .role-card::before {
            content: "";
            position: absolute;
            inset: -40% -35% auto auto;
            width: 170px;
            height: 170px;
            border-radius: 50%;
            background: rgba(255,255,255,.14);
            z-index: 0;
            animation: roleOrb 6s ease-in-out infinite;
        }
        .role-card::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background: linear-gradient(120deg, transparent 0 35%, rgba(255,255,255,.22) 48%, transparent 62%);
            transform: translateX(-110%);
            transition: transform .65s ease;
        }
        .role-card:hover { transform: translateY(-7px); border-color: #96B7F4; box-shadow: 0 28px 58px rgba(0,31,91,.13); }
        .role-card:hover::after { transform: translateX(110%); }
        .role-top {
            position: relative;
            z-index: 2;
            min-height: 150px;
            padding: 20px;
            color: #fff;
            background:
                radial-gradient(circle at 88% 10%, rgba(255,255,255,.16), transparent 28%),
                linear-gradient(135deg, #003DA5, #0057C8);
        }
        .role-top.director {
            background:
                radial-gradient(circle at 88% 10%, rgba(255,255,255,.16), transparent 28%),
                linear-gradient(135deg, #001F5B, #00337F);
        }
        .role-top.employee {
            background:
                radial-gradient(circle at 88% 10%, rgba(255,255,255,.13), transparent 28%),
                linear-gradient(135deg, #334155, #1F2D44);
        }
        .role-top i { width: 26px; height: 26px; animation: roleIconFloat 3.8s ease-in-out infinite; }
        .role-card:nth-child(2) .role-top i { animation-delay: .5s; }
        .role-card:nth-child(3) .role-top i { animation-delay: 1s; }
        .role-top h3 { margin: 16px 0 0; font-size: 22px; line-height: 1.15; font-weight: 900; }
        .role-top p { margin: 7px 0 0; color: rgba(255,255,255,.76); font-size: 13px; font-weight: 700; }
        .role-status {
            position: absolute;
            right: 16px;
            top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 9px;
            border-radius: 999px;
            color: #D7E7FF;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .role-status::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #22C55E;
            box-shadow: 0 0 0 5px rgba(34,197,94,.14);
        }
        .role-preview {
            position: relative;
            z-index: 2;
            display: grid;
            gap: 10px;
            margin: -30px 18px 0;
            padding: 14px;
            border: 1px solid var(--mf-line);
            border-radius: 8px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 16px 30px rgba(0,31,91,.08);
        }
        .preview-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            color: #334155;
            font-size: 12px;
            font-weight: 900;
        }
        .preview-row small { color: var(--mf-muted); font-size: 10px; font-weight: 800; }
        .preview-pill { color: #fff; background: var(--mf-red); border-radius: 999px; padding: 5px 8px; font-size: 10px; font-weight: 900; }
        .preview-track { height: 6px; border-radius: 999px; background: #E5EDF8; overflow: hidden; }
        .preview-track i { display: block; height: 100%; width: var(--w); border-radius: inherit; background: var(--mf-blue); transform-origin: left; animation: roleBar 2.8s ease-in-out infinite; }
        .role-list { position: relative; z-index: 2; padding: 16px 20px 20px; display: grid; gap: 10px; }
        .role-list span { display: flex; align-items: center; gap: 9px; color: #334155; font-size: 13px; font-weight: 800; }
        .role-list i { color: #16A34A; width: 16px; height: 16px; flex-shrink: 0; }

        .flow-band { background: #fff; border-top: 1px solid var(--mf-line); border-bottom: 1px solid var(--mf-line); }
        .flow-grid { position: relative; display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .flow-grid::before {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            top: 35px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--mf-blue), var(--mf-red), transparent);
            transform-origin: left;
            animation: flowLine 4.8s ease-in-out infinite;
            opacity: .42;
        }
        .flow-step { position: relative; min-height: 176px; padding: 18px; border: 1px solid var(--mf-line); border-radius: 8px; background: #F8FAFF; }
        .flow-step { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; animation: stepRise .8s ease both; }
        .flow-step:nth-child(2) { animation-delay: .08s; }
        .flow-step:nth-child(3) { animation-delay: .16s; }
        .flow-step:nth-child(4) { animation-delay: .24s; }
        .flow-step:hover { transform: translateY(-4px); border-color: #B9CDF5; box-shadow: 0 18px 36px rgba(0,31,91,.08); }
        .flow-step::before { content: attr(data-step); display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; color: #fff; background: var(--mf-red); font-size: 13px; font-weight: 900; }
        .flow-step h3 { margin: 18px 0 8px; color: var(--mf-navy); font-size: 16px; font-weight: 900; }
        .flow-step p { margin: 0; color: var(--mf-muted); font-size: 13px; line-height: 1.65; }

        .insight-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 18px; align-items: stretch; }
        .insight-card { border: 1px solid var(--mf-line); border-radius: 8px; background: #fff; padding: 22px; box-shadow: 0 16px 34px rgba(0,31,91,.05); }
        .insight-title { display: flex; align-items: center; gap: 10px; color: var(--mf-navy); font-size: 18px; font-weight: 900; }
        .bars { display: grid; gap: 15px; margin-top: 22px; }
        .bar-line { display: grid; grid-template-columns: 120px 1fr 46px; gap: 12px; align-items: center; color: #334155; font-size: 13px; font-weight: 800; }
        .track { height: 9px; border-radius: 999px; background: #E5EDF8; overflow: hidden; }
        .fill { height: 100%; border-radius: inherit; background: var(--mf-blue); transform-origin: left; animation: barGrow 1.3s ease-out both; }
        .fill.red { background: var(--mf-red); }
        .fill.green { background: #16A34A; }
        .mini-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 22px; }
        .mini-stat { padding: 16px; border-radius: 8px; background: #F8FAFF; border: 1px solid var(--mf-line); animation: statGlow 5s ease-in-out infinite; }
        .mini-stat:nth-child(2) { animation-delay: .8s; }
        .mini-stat:nth-child(3) { animation-delay: 1.6s; }
        .mini-stat:nth-child(4) { animation-delay: 2.4s; }
        .mini-stat b { display: block; color: var(--mf-navy); font-size: 28px; line-height: 1; font-weight: 900; }
        .mini-stat span { display: block; margin-top: 7px; color: var(--mf-muted); font-size: 11px; font-weight: 900; text-transform: uppercase; }

        .cta {
            position: relative;
            overflow: hidden;
            padding: 54px 0;
            background: var(--mf-navy);
            color: #fff;
        }
        .cta::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: -30%;
            width: 24%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
            transform: skewX(-18deg);
            animation: ctaSweep 6s ease-in-out infinite;
        }
        .cta-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }
        .cta h2 { color: #fff; }
        .cta p { max-width: 620px; margin: 12px 0 0; color: #D7E7FF; line-height: 1.7; }
        .cta .btn { flex-shrink: 0; }

        footer { padding: 28px 0; background: #061A45; color: #BFD8FF; }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 18px; font-size: 12px; }

        @keyframes gridDrift {
            from { background-position: 0 0, 0 0; }
            to { background-position: 84px 42px, 84px 42px; }
        }
        @keyframes heroLightSweep {
            0%, 100% { transform: translate3d(-8%, -2%, 0) scale(1); opacity: .48; }
            45% { transform: translate3d(8%, 4%, 0) scale(1.06); opacity: .92; }
            70% { transform: translate3d(2%, -3%, 0) scale(1.02); opacity: .68; }
        }
        @keyframes filmScan {
            from { background-position: 0 0; }
            to { background-position: 0 56px; }
        }
        @keyframes heroItemIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes headlineReveal {
            from { transform: translateY(110%) skewY(3deg); opacity: 0; }
            to { transform: translateY(0) skewY(0); opacity: 1; }
        }
        @keyframes headlineAccentSweep {
            0%, 100% { background-position: 0% 50%; transform: scaleX(.96); }
            50% { background-position: 100% 50%; transform: scaleX(1.02); }
        }
        @keyframes headlineAccentLift {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        @keyframes headlineCaret {
            0%, 48% { opacity: 1; }
            49%, 100% { opacity: 0; }
        }
        @keyframes headlineSoftGlow {
            0%, 100% { color: #D7E7FF; }
            50% { color: #FFFFFF; }
        }
        @keyframes logoMarkPulse {
            0%, 100% { transform: translateY(0) scale(1); box-shadow: inset 5px 0 0 var(--mf-red), 0 10px 22px rgba(0,31,91,.10); }
            48% { transform: translateY(-1px) scale(1.035); box-shadow: inset 5px 0 0 var(--mf-red), 0 14px 28px rgba(0,61,165,.16); }
        }
        @keyframes logoMarkSweep {
            0%, 38% { left: -65%; opacity: 0; }
            48% { opacity: 1; }
            72%, 100% { left: 120%; opacity: 0; }
        }
        @keyframes logoWordShine {
            0%, 42% { left: -42%; opacity: 0; }
            52% { opacity: 1; }
            78%, 100% { left: 108%; opacity: 0; }
        }
        @keyframes logoBlueTone {
            0%, 100% { color: var(--mf-blue); }
            50% { color: var(--mf-blue-2); }
        }
        @keyframes logoRedTone {
            0%, 100% { color: var(--mf-red); }
            50% { color: #C70025; }
        }
        @keyframes logoLetterWave {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            18% { transform: translateY(-3px) rotate(-1deg); }
            36% { transform: translateY(0) rotate(0deg); }
        }
        @keyframes logoSubTrack {
            0%, 100% { letter-spacing: .18em; opacity: .92; }
            50% { letter-spacing: .22em; opacity: 1; }
        }
        @keyframes signalFloat {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: .56; }
            45% { transform: translate3d(14px, -18px, 0) scale(1.35); opacity: .92; }
            70% { transform: translate3d(-10px, 10px, 0) scale(.92); opacity: .68; }
        }
        @keyframes panelFloat {
            0%, 100% { transform: translateY(-50%); }
            50% { transform: translateY(calc(-50% - 10px)); }
        }
        @keyframes panelGlow {
            0%, 100% { box-shadow: 0 30px 80px rgba(0,0,0,.22), 0 0 0 rgba(11,102,216,0); }
            50% { box-shadow: 0 34px 90px rgba(0,0,0,.24), 0 0 48px rgba(11,102,216,.22); }
        }
        @keyframes panelPulsePoint {
            0%, 100% { --pulse-x: 76%; opacity: .52; }
            45% { --pulse-x: 34%; opacity: .92; }
            72% { --pulse-x: 62%; opacity: .70; }
        }
        @keyframes scanPanel {
            0%, 28% { top: -45%; opacity: 0; }
            45% { opacity: 1; }
            78%, 100% { top: 108%; opacity: 0; }
        }
        @keyframes rowDataSweep {
            0%, 24% { transform: translateX(-105%); opacity: 0; }
            42% { opacity: 1; }
            76%, 100% { transform: translateX(105%); opacity: 0; }
        }
        @keyframes rowPulse {
            0%, 100% { background: rgba(255,255,255,.10); }
            45% { background: rgba(255,255,255,.18); }
        }
        @keyframes badgeBeat {
            0%, 100% { transform: scale(1); box-shadow: none; }
            48% { transform: scale(1.04); box-shadow: 0 0 0 6px rgba(228,0,43,.14); }
        }
        @keyframes stepRise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes barGrow {
            from { transform: scaleX(.18); }
            to { transform: scaleX(1); }
        }
        @keyframes statGlow {
            0%, 100% { box-shadow: none; border-color: var(--mf-line); }
            50% { box-shadow: 0 14px 28px rgba(0,61,165,.08); border-color: #B9CDF5; }
        }
        @keyframes metricBreathe {
            0%, 100% { transform: translateY(0); background: rgba(255,255,255,.12); }
            50% { transform: translateY(-3px); background: rgba(255,255,255,.18); }
        }
        @keyframes timelineNode {
            0%, 100% { transform: translateX(-50%) scale(.82); opacity: .64; }
            45% { transform: translateX(-50%) scale(1.18); opacity: 1; box-shadow: 0 0 0 7px rgba(255,255,255,.14), 0 0 22px rgba(255,255,255,.42); }
        }
        @keyframes tickerMove {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        @keyframes roleIconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-5px) rotate(-4deg); }
        }
        @keyframes roleFlow {
            0%, 100% { transform: scaleX(.18); opacity: .16; }
            50% { transform: scaleX(1); opacity: .58; }
        }
        @keyframes roleOrb {
            0%, 100% { transform: translate3d(0,0,0) scale(1); opacity: .78; }
            50% { transform: translate3d(-22px,26px,0) scale(1.16); opacity: .42; }
        }
        @keyframes roleBar {
            0%, 100% { transform: scaleX(.72); }
            50% { transform: scaleX(1); }
        }
        @keyframes flowLine {
            0%, 100% { transform: scaleX(.12); opacity: .16; }
            52% { transform: scaleX(1); opacity: .50; }
        }
        @keyframes ctaSweep {
            0%, 45% { left: -30%; opacity: 0; }
            58% { opacity: 1; }
            86%, 100% { left: 120%; opacity: 0; }
        }

        @media (max-width: 980px) {
            .hero { min-height: auto; }
            .hero-copy { width: 100%; padding: 68px 0 38px; }
            .ops-panel { position: relative; inset: auto; width: 100%; transform: none; margin-bottom: 42px; }
            .ops-panel { animation-name: panelFloatMobile; }
            .role-grid, .flow-grid, .insight-grid { grid-template-columns: 1fr; }
            .role-grid::before { display: none; }
            .flow-grid::before { display: none; }
            .section-head, .cta-box, .footer-inner { align-items: flex-start; flex-direction: column; }
        }

        @keyframes panelFloatMobile {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }

        @media (max-width: 680px) {
            .topbar-inner { align-items: flex-start; flex-direction: column; padding: 14px 0; }
            .nav { width: 100%; flex-wrap: wrap; }
            .nav .login-btn { margin-left: 0; }
            .hero-copy { padding-top: 48px; }
            .ops-metrics, .mini-stats { grid-template-columns: 1fr; }
            .bar-line { grid-template-columns: 1fr; gap: 7px; }
            .hero-actions .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <header class="topbar">
            <div class="container topbar-inner">
                <a href="{{ route('landing') }}" class="brand" aria-label="MobiFone WorkHub">
                    <div>
                        <div class="brand-word" aria-label="MobiFone">
                            <strong class="blue" aria-hidden="true"><span class="brand-letter">m</span><span class="brand-letter">o</span><span class="brand-letter">b</span><span class="brand-letter brand-i">ı</span></strong><strong class="red" aria-hidden="true"><span class="brand-letter">f</span><span class="brand-letter">o</span><span class="brand-letter">n</span><span class="brand-letter">e</span></strong>
                        </div>
                        <div class="brand-sub">WORKHUB</div>
                    </div>
                </a>

                <nav class="nav" aria-label="Điều hướng trang chủ">
                    <a href="#roles">Vai trò</a>
                    <a href="#workflow">Luồng việc</a>
                    <a href="#insights">Báo cáo</a>
                    <a href="{{ route('register') }}">Đăng ký</a>
                    <a href="{{ route('login') }}" class="login-btn">Đăng nhập</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="ops-wall" aria-hidden="true"></div>
                <div class="ops-radar" aria-hidden="true">
                    <span class="signal-dot"></span>
                    <span class="signal-dot red"></span>
                    <span class="signal-dot green"></span>
                    <span class="signal-dot"></span>
                    <span class="signal-dot red"></span>
                </div>
                <div class="container">
                    <div class="hero-copy">
                        <div class="eyebrow">MobiFone HR Operations</div>
                        <h1 class="hero-headline" aria-label="Trung tâm điều hành công việc cho đội ngũ nội bộ.">
                            <span class="headline-line"><span>Trung tâm điều hành</span></span>
                            <span class="headline-line"><span><span class="headline-accent">công việc</span> cho</span></span>
                            <span class="headline-line"><span class="headline-soft">đội ngũ nội bộ</span></span>
                        </h1>
                        <p class="hero-desc">
                            WorkHub gom giao việc, phòng ban, tiến độ, tài liệu và thông báo vào một không gian rõ ràng cho Giám đốc, Trưởng phòng và Nhân viên.
                        </p>
                        <div class="hero-actions">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                Vào WorkHub <i data-lucide="arrow-right"></i>
                            </a>
                            <a href="#workflow" class="btn btn-secondary">
                                Xem cách vận hành <i data-lucide="chevron-down"></i>
                            </a>
                        </div>
                        <div class="hero-proof">
                            <span><i data-lucide="check-circle-2"></i>3 vai trò rõ ràng</span>
                            <span><i data-lucide="check-circle-2"></i>Theo dõi deadline</span>
                            <span><i data-lucide="check-circle-2"></i>Tài liệu theo công việc</span>
                        </div>
                    </div>

                    <div class="ops-panel" aria-label="Minh họa bảng điều hành WorkHub">
                        <div class="ops-panel-head">
                            <div class="ops-title">Operations board</div>
                            <div class="ops-live">Đang đồng bộ</div>
                        </div>
                        <div class="ops-body">
                            <div class="ops-row">
                                <div class="ops-icon"><i data-lucide="send"></i></div>
                                <div><strong>Giao việc cấp công ty</strong><span>Giám đốc theo dõi toàn bộ tiến độ</span></div>
                                <div class="ops-badge">57%</div>
                            </div>
                            <div class="ops-row">
                                <div class="ops-icon"><i data-lucide="users"></i></div>
                                <div><strong>Phòng ban xử lý</strong><span>Trưởng phòng phân công đúng nhân sự</span></div>
                                <div class="ops-badge">12 việc</div>
                            </div>
                            <div class="ops-row">
                                <div class="ops-icon"><i data-lucide="paperclip"></i></div>
                                <div><strong>Tài liệu đính kèm</strong><span>File được duyệt trước khi gửi lên cấp trên</span></div>
                                <div class="ops-badge">8 file</div>
                            </div>
                        </div>
                        <div class="ops-metrics">
                            <div class="ops-metric"><b>24</b><span>Công việc</span></div>
                            <div class="ops-metric"><b>3</b><span>Phòng ban</span></div>
                            <div class="ops-metric"><b>92%</b><span>Đúng hạn</span></div>
                        </div>
                        <div class="ops-timeline" aria-label="Luồng vận hành đang chạy">
                            <div class="ops-step">Tạo</div>
                            <div class="ops-step">Giao</div>
                            <div class="ops-step">Xử lý</div>
                            <div class="ops-step">Duyệt</div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="activity-ticker" aria-label="Hoạt động vận hành đang chạy">
                <div class="ticker-track">
                    @foreach([
                        ['send', 'Giám đốc vừa giao mục tiêu tuần'],
                        ['users', 'Phòng Nhân sự cập nhật 6 việc'],
                        ['paperclip', 'Tài liệu mới được đính kèm'],
                        ['bell', 'Nhắc hạn xử lý tự động'],
                        ['bar-chart-3', 'Báo cáo hiệu suất đang đồng bộ'],
                        ['shield-check', 'Quyền truy cập theo chức vụ'],
                    ] as $item)
                        <span class="ticker-item"><span class="ticker-dot"></span><i data-lucide="{{ $item[0] }}"></i>{{ $item[1] }}</span>
                    @endforeach
                    @foreach([
                        ['send', 'Giám đốc vừa giao mục tiêu tuần'],
                        ['users', 'Phòng Nhân sự cập nhật 6 việc'],
                        ['paperclip', 'Tài liệu mới được đính kèm'],
                        ['bell', 'Nhắc hạn xử lý tự động'],
                        ['bar-chart-3', 'Báo cáo hiệu suất đang đồng bộ'],
                        ['shield-check', 'Quyền truy cập theo chức vụ'],
                    ] as $item)
                        <span class="ticker-item"><span class="ticker-dot"></span><i data-lucide="{{ $item[0] }}"></i>{{ $item[1] }}</span>
                    @endforeach
                </div>
            </div>

            <section id="roles" class="section reveal">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">Role-based workspace</div>
                            <h2>Mỗi chức vụ có đúng không gian làm việc của mình.</h2>
                        </div>
                        <p class="section-note">Ba vai trò duy nhất giúp giao diện gọn hơn, quyền hạn dễ hiểu hơn và giảm nhầm lẫn khi vận hành hằng ngày.</p>
                    </div>

                    <div class="role-grid">
                        <article class="role-card">
                            <div class="role-top director">
                                <span class="role-status">Toàn quyền</span>
                                <i data-lucide="crown"></i>
                                <h3>Giám đốc</h3>
                                <p>Điều hành toàn công ty</p>
                            </div>
                            <div class="role-preview">
                                <div class="preview-row"><span>Tiến độ công ty <small>24 công việc</small></span><b class="preview-pill">92%</b></div>
                                <div class="preview-track"><i style="--w:92%"></i></div>
                                <div class="preview-row"><span>Phòng ban đang xử lý</span><small>3/3 online</small></div>
                            </div>
                            <div class="role-list">
                                <span><i data-lucide="check"></i>Giao việc cho mọi phòng ban</span>
                                <span><i data-lucide="check"></i>Xem báo cáo tổng hợp</span>
                                <span><i data-lucide="check"></i>Quản lý nhân sự và quyền truy cập</span>
                            </div>
                        </article>

                        <article class="role-card">
                            <div class="role-top">
                                <span class="role-status">Phòng ban</span>
                                <i data-lucide="briefcase"></i>
                                <h3>Trưởng phòng</h3>
                                <p>Điều phối đội ngũ trong phòng</p>
                            </div>
                            <div class="role-preview">
                                <div class="preview-row"><span>Việc chờ phân công <small>từ Giám đốc</small></span><b class="preview-pill">5</b></div>
                                <div class="preview-track"><i style="--w:68%"></i></div>
                                <div class="preview-row"><span>Nhân viên khả dụng</span><small>6 người</small></div>
                            </div>
                            <div class="role-list">
                                <span><i data-lucide="check"></i>Nhận việc cấp trên giao</span>
                                <span><i data-lucide="check"></i>Phân công nhân viên trong phòng</span>
                                <span><i data-lucide="check"></i>Duyệt tài liệu gửi Giám đốc</span>
                            </div>
                        </article>

                        <article class="role-card">
                            <div class="role-top employee">
                                <span class="role-status">Cá nhân</span>
                                <i data-lucide="user-check"></i>
                                <h3>Nhân viên</h3>
                                <p>Tập trung vào việc được giao</p>
                            </div>
                            <div class="role-preview">
                                <div class="preview-row"><span>Việc hôm nay <small>ưu tiên cao</small></span><b class="preview-pill">3</b></div>
                                <div class="preview-track"><i style="--w:54%"></i></div>
                                <div class="preview-row"><span>Tài liệu đã gửi</span><small>8 file</small></div>
                            </div>
                            <div class="role-list">
                                <span><i data-lucide="check"></i>Xem danh sách công việc cá nhân</span>
                                <span><i data-lucide="check"></i>Cập nhật tiến độ và trạng thái</span>
                                <span><i data-lucide="check"></i>Upload tài liệu đính kèm</span>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="workflow" class="section flow-band reveal">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">Workflow</div>
                            <h2>Luồng xử lý gọn từ giao việc đến báo cáo.</h2>
                        </div>
                        <p class="section-note">Các bước chính được đặt trong cùng một hệ thống để người dùng không phải chuyển qua nhiều công cụ rời rạc.</p>
                    </div>

                    <div class="flow-grid">
                        <article class="flow-step" data-step="01">
                            <h3>Tạo mục tiêu</h3>
                            <p>Ghi rõ nội dung, người phụ trách, hạn xử lý và trạng thái ban đầu.</p>
                        </article>
                        <article class="flow-step" data-step="02">
                            <h3>Phân công</h3>
                            <p>Giao cho một hoặc nhiều nhân viên theo đúng phạm vi chức vụ.</p>
                        </article>
                        <article class="flow-step" data-step="03">
                            <h3>Theo dõi</h3>
                            <p>Tiến độ, file đính kèm và cảnh báo quá hạn được cập nhật liên tục.</p>
                        </article>
                        <article class="flow-step" data-step="04">
                            <h3>Tổng hợp</h3>
                            <p>Báo cáo theo phòng ban và nhân sự giúp ra quyết định nhanh hơn.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="insights" class="section reveal">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <div class="section-kicker">Performance insights</div>
                            <h2>Báo cáo có thể đọc trong vài giây.</h2>
                        </div>
                        <p class="section-note">Dashboard ưu tiên chỉ số vận hành: hoàn thành, đang làm, quá hạn và khối lượng theo phòng ban.</p>
                    </div>

                    <div class="insight-grid">
                        <div class="insight-card">
                            <div class="insight-title"><i data-lucide="bar-chart-3"></i> Hiệu suất phòng ban</div>
                            <div class="bars">
                                <div class="bar-line"><span>Nhân sự</span><div class="track"><div class="fill" style="width: 72%"></div></div><b>72%</b></div>
                                <div class="bar-line"><span>Đào tạo</span><div class="track"><div class="fill green" style="width: 84%"></div></div><b>84%</b></div>
                                <div class="bar-line"><span>Pháp chế</span><div class="track"><div class="fill red" style="width: 48%"></div></div><b>48%</b></div>
                            </div>
                        </div>

                        <div class="insight-card">
                            <div class="insight-title"><i data-lucide="activity"></i> Tình hình tuần này</div>
                            <div class="mini-stats">
                                <div class="mini-stat"><b>18</b><span>Hoàn thành</span></div>
                                <div class="mini-stat"><b>6</b><span>Đang làm</span></div>
                                <div class="mini-stat"><b>2</b><span>Quá hạn</span></div>
                                <div class="mini-stat"><b>11</b><span>Tài liệu</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cta reveal">
                <div class="container cta-box">
                    <div>
                        <div class="section-kicker">Ready for work</div>
                        <h2>Bắt đầu điều hành công việc trong WorkHub.</h2>
                        <p>Đăng nhập để tiếp tục vào không gian phù hợp với chức vụ của bạn: Giám đốc, Trưởng phòng hoặc Nhân viên.</p>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Đăng nhập ngay <i data-lucide="log-in"></i>
                    </a>
                </div>
            </section>
        </main>

        <footer>
            <div class="container footer-inner">
                <div>MobiFone WorkHub</div>
                <div>© 2026 MobiFone. Tối ưu cho vận hành nội bộ.</div>
            </div>
        </footer>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        const revealItems = document.querySelectorAll('.reveal');
        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.16 });

            revealItems.forEach((item) => revealObserver.observe(item));
        } else {
            revealItems.forEach((item) => item.classList.add('is-visible'));
        }
    </script>
</body>
</html>
