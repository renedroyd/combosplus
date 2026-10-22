<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#0b164d">
    <meta name="description" content="NEXO: conecta, descubre y compra en un marketplace de tiendas independientes.">
    <title>@yield('title','NEXO — Conecta · Descubre · Compra')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        html{scroll-behavior:smooth}body{background:#f8f9ff;color:#11152f}
        .nexo-panel{background:#fff;border:1px solid #e5e7f5;box-shadow:0 2px 8px rgba(22,31,90,.045)}
        .nexo-wordmark{font-weight:1000;letter-spacing:-.075em;line-height:.9}
        .nexo-nav-link{display:inline-flex;align-items:center;gap:.5rem;min-height:44px;padding:.5rem .8rem;border-bottom:3px solid transparent;color:#fff;font-size:.875rem;font-weight:800;white-space:nowrap;transition:background .2s}
        .nexo-nav-link:hover,.nexo-nav-link:focus-visible{background:rgba(255,255,255,.12);border-bottom-color:#ffb000;outline:none}
        @media(max-width:767px){.nexo-nav-scroll{overflow-x:auto;scrollbar-width:none}.nexo-nav-scroll::-webkit-scrollbar{display:none}}
        @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}}
    </style>
</head>
<body class="min-h-screen antialiased selection:bg-orange-200 selection:text-slate-950">
<div class="bg-gradient-to-r from-violet-800 via-fuchsia-700 to-orange-500 text-white">
    <div class="mx-auto flex max-w-[1400px] items-center justify-center gap-2 px-3 py-1.5 text-center text-[11px] font-bold sm:text-xs"><span>✦</span><span>Conecta · Descubre · Compra</span><span class="hidden sm:inline">— descubre nuevas tiendas y productos</span><a href="{{ route('marketplace.register') }}" class="ml-1 underline underline-offset-2">Vende en NEXO</a></div>
</div>
<header class="relative z-40 bg-white shadow-sm">
    <div class="mx-auto flex max-w-[1400px] items-center gap-3 px-4 py-3 sm:gap-5 sm:px-7 lg:px-10">
        <a href="{{ route('marketplace.home') }}" aria-label="NEXO, página principal" class="shrink-0 text-[2rem] font-black tracking-[-.09em] text-[#11184f] sm:text-4xl"><span class="nexo-wordmark">NE<span class="text-orange-500">X</span>O</span><span class="mt-1 block text-[9px] font-bold tracking-normal text-slate-700 sm:text-[10px]">Conecta · Descubre · Compra</span></a>
        <form action="{{ route('marketplace.products') }}" method="GET" role="search" class="hidden min-w-0 flex-1 md:flex">
            <label for="global-product-search" class="sr-only">Buscar productos, marcas y tiendas</label>
            <input id="global-product-search" name="q" type="search" value="{{ request('q') }}" placeholder="Buscar productos, marcas y tiendas..." class="min-h-11 min-w-0 flex-1 rounded-l-full border border-slate-200 bg-[#f1f2fa] px-5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-100">
            <button class="rounded-r-full border border-l-0 border-slate-200 bg-[#f1f2fa] px-5 text-lg font-black text-[#11184f] hover:bg-violet-100" aria-label="Buscar">⌕</button>
        </form>
        <nav aria-label="Accesos de usuario" class="ml-auto flex shrink-0 items-center gap-2 text-center text-[10px] font-bold text-[#11184f] sm:gap-5 sm:text-xs">
            @auth<a href="{{ route('tenant.switch.index') }}" class="hidden flex-col items-center gap-1 sm:flex"><span class="text-xl">♙</span>Mi cuenta</a>@else<a href="{{ route('marketplace.register') }}" class="hidden flex-col items-center gap-1 sm:flex"><span class="text-xl">♙</span>Mi cuenta</a>@endauth
            <a href="{{ route('marketplace.stores') }}" class="flex flex-col items-center gap-1"><span class="text-xl sm:text-2xl">♡</span>Tiendas</a>
            <a href="{{ route('marketplace.products') }}" class="flex flex-col items-center gap-1"><span class="text-xl sm:text-2xl">🛍</span>Productos</a>
        </nav>
    </div>
    <div class="border-t border-slate-100 px-4 py-2 md:hidden"><form action="{{ route('marketplace.products') }}" method="GET" role="search" class="mx-auto flex max-w-[1400px]"><label for="mobile-product-search" class="sr-only">Buscar productos</label><input id="mobile-product-search" name="q" type="search" value="{{ request('q') }}" placeholder="Buscar productos, marcas y tiendas..." class="min-h-10 min-w-0 flex-1 rounded-l-full border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 placeholder:text-slate-500 focus:border-violet-500 focus:outline-none"><button class="rounded-r-full bg-violet-800 px-4 font-bold text-white" aria-label="Buscar">⌕</button></form></div>
    <nav aria-label="Navegación principal" class="nexo-nav-scroll bg-[#0b164d] text-white"><div class="mx-auto flex max-w-[1400px] items-center gap-1 px-3 sm:px-6 lg:px-10">
        <a href="{{ route('marketplace.products') }}" class="nexo-nav-link">☰ <span>Todas las categorías</span>⌄</a>
        <a href="{{ route('marketplace.home') }}" class="nexo-nav-link border-b-orange-400">⌂ <span>Inicio</span></a>
        <a href="{{ route('marketplace.products') }}" class="nexo-nav-link">🔥 <span>Productos</span></a>
        <a href="{{ route('marketplace.stores') }}" class="nexo-nav-link">♙ <span>Tiendas</span></a>
        <a href="{{ route('marketplace.products') }}" class="nexo-nav-link">◆ <span>Descubrir</span></a>
        <a href="{{ route('marketplace.register') }}" class="ml-auto my-1 inline-flex min-h-9 shrink-0 items-center gap-2 rounded-lg bg-orange-500 px-3 text-xs font-black text-white shadow transition hover:bg-orange-400 sm:px-4 sm:text-sm">✦ <span>Vende en NEXO</span></a>
    </div></nav>
</header>
<main class="pb-8">@yield('content')</main>
<footer class="mt-6 bg-[#0b164d] text-white">
    <div class="mx-auto grid max-w-[1400px] gap-5 px-4 py-7 sm:grid-cols-2 sm:px-7 lg:grid-cols-[1.2fr_1fr_1fr_1fr] lg:px-10">
        <div><a href="{{ route('marketplace.home') }}" class="text-3xl font-black tracking-[-.08em]">NE<span class="text-orange-400">X</span>O</a><p class="mt-1 text-xs text-blue-100">Conecta · Descubre · Compra</p></div>
        <div class="flex items-center gap-3"><span class="text-3xl">👥</span><div><p class="text-xl font-black">Tu próxima tienda</p><p class="text-xs text-blue-100">Está por descubrirse</p></div></div>
        <div class="flex items-center gap-3"><span class="text-3xl">🏪</span><div><p class="text-xl font-black">Tiendas aliadas</p><a href="{{ route('marketplace.stores') }}" class="text-xs text-blue-100 underline">Conócelas</a></div></div>
        <div class="flex items-center gap-3"><span class="text-3xl">🛡️</span><div><p class="text-xl font-black">Compra informada</p><p class="text-xs text-blue-100">Conoce cada producto</p></div></div>
    </div>
    <div class="border-t border-white/15"><div class="mx-auto flex max-w-[1400px] flex-col gap-2 px-4 py-3 text-xs text-blue-100 sm:flex-row sm:justify-between sm:px-7 lg:px-10"><span>© {{ date('Y') }} NEXO. Todos los derechos reservados.</span><span>Conecta · Descubre · Compra</span></div></div>
</footer>
</body>
</html>