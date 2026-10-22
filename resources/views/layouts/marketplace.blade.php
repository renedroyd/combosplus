<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#6d28d9">
    <meta name="description" content="Nexo: descubre productos, explora tiendas independientes y lleva tu negocio a más clientes.">
    <title>@yield('title','Nexo — Tu mundo, más cerca')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
<style>html{scroll-behavior:smooth}.nexo-parallax{background-position:center;background-size:cover;background-attachment:fixed}@media(max-width:767px),(prefers-reduced-motion:reduce){.nexo-parallax{background-attachment:scroll}}@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}</style>
</head>
<body class="min-h-screen bg-white text-slate-950 antialiased selection:bg-fuchsia-200 selection:text-slate-950">
<div class="bg-gradient-to-r from-violet-700 via-fuchsia-600 to-rose-500 text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-center gap-2 px-4 py-2 text-center text-xs font-bold sm:text-sm">
        <span aria-hidden="true">✦</span><span>Descubre tiendas increíbles y encuentra algo nuevo en Nexo.</span><a href="{{ route('marketplace.register') }}" class="underline decoration-white/60 underline-offset-2 hover:decoration-white">¿Vendes? Únete aquí</a>
    </div>
</div>
<header class="sticky top-0 z-40 border-b border-violet-100 bg-white/95 text-slate-950 shadow-md shadow-violet-950/5 backdrop-blur">
    <div class="mx-auto flex min-h-[4.5rem] max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('marketplace.home') }}" aria-label="Nexo, página principal" class="flex shrink-0 items-center gap-2 text-2xl font-black tracking-tight"><span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-violet-700 to-fuchsia-600 text-xl text-white shadow-md shadow-violet-200">N</span><span class="bg-gradient-to-r from-violet-800 to-fuchsia-600 bg-clip-text text-transparent">Nexo<span class="text-slate-900">.</span></span></a>
        <form action="{{ route('marketplace.products') }}" method="GET" role="search" class="hidden min-w-0 max-w-xl flex-1 md:flex">
            <label for="global-product-search" class="sr-only">Buscar productos</label><input id="global-product-search" name="q" type="search" value="{{ request('q') }}" placeholder="Busca productos, marcas y categorías..." class="min-h-11 min-w-0 flex-1 rounded-l-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-100">
            <button class="rounded-r-xl bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 font-extrabold text-white transition hover:from-violet-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-violet-400" aria-label="Buscar">Buscar <span aria-hidden="true">⌕</span></button>
        </form>
        <nav aria-label="Navegación principal" class="flex shrink-0 items-center gap-3 text-sm font-bold sm:gap-5">
            <a href="{{ route('marketplace.stores') }}" class="hidden text-slate-600 transition hover:text-violet-700 sm:inline">Tiendas</a>
            <a href="{{ route('marketplace.products') }}" class="hidden text-slate-600 transition hover:text-violet-700 sm:inline">Productos</a>
            <a href="{{ route('marketplace.register') }}" class="rounded-xl bg-amber-300 px-3 py-2.5 text-slate-950 shadow-sm transition hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:px-4"><span class="hidden sm:inline">Vende en Nexo</span><span class="sm:hidden">Vender</span></a>
        </nav>
    </div>
    <div class="border-t border-slate-100 px-4 py-2 md:hidden">
        <form action="{{ route('marketplace.products') }}" method="GET" role="search" class="mx-auto flex max-w-7xl">
            <label for="mobile-product-search" class="sr-only">Buscar productos</label><input id="mobile-product-search" name="q" type="search" value="{{ request('q') }}" placeholder="¿Qué estás buscando?" class="min-h-10 min-w-0 flex-1 rounded-l-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-100">
            <button class="rounded-r-lg bg-violet-700 px-4 font-bold text-white hover:bg-violet-800" aria-label="Buscar productos">⌕</button>
        </form>
    </div>
</header>
<main>@yield('content')</main>
<footer class="bg-slate-950 text-slate-300">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
        <div><a href="{{ route('marketplace.home') }}" class="inline-flex items-center gap-2 text-2xl font-black tracking-tight text-white"><span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-lg">N</span>Nexo<span class="text-amber-300">.</span></a><p class="mt-3 max-w-xs text-sm leading-6 text-slate-400">Tu mundo, más cerca. Descubre tiendas, encuentra productos y conecta con nuevos negocios.</p></div>
        <div><h2 class="text-sm font-extrabold uppercase tracking-wider text-white">Explora Nexo</h2><div class="mt-4 flex flex-col items-start gap-3 text-sm"><a class="transition hover:text-amber-300" href="{{ route('marketplace.stores') }}">Explorar tiendas</a><a class="transition hover:text-amber-300" href="{{ route('marketplace.products') }}">Explorar productos</a></div></div>
        <div><h2 class="text-sm font-extrabold uppercase tracking-wider text-white">Haz crecer tu negocio</h2><p class="mt-4 text-sm leading-6 text-slate-400">Lleva tus productos a más personas y crea tu espacio en el marketplace.</p><a class="mt-4 inline-flex rounded-xl bg-amber-300 px-4 py-2.5 text-sm font-extrabold text-slate-950 transition hover:bg-amber-200" href="{{ route('marketplace.register') }}">Vende en Nexo <span class="ml-2" aria-hidden="true">↗</span></a></div>
    </div>
    <div class="border-t border-white/10"><div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"><span>© {{ date('Y') }} Nexo. Todos los derechos reservados.</span><span>Compra con curiosidad. Vende con confianza.</span></div></div>
</footer>
</body>
</html>