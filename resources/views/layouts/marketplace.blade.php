<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','CombosPlus')</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-white text-slate-900 antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur"><div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
<a href="{{ route('marketplace.home') }}" class="text-xl font-bold"><span class="text-blue-600">Combos</span><span>Plus</span></a>
<nav class="hidden items-center gap-7 text-sm font-medium sm:flex"><a href="{{ route('marketplace.stores') }}" class="text-slate-600 hover:text-blue-600">Tiendas</a><a href="{{ route('register') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-white hover:bg-blue-500">Registra tu tienda</a></nav>
</div></header><main>@yield('content')</main>
<footer class="border-t border-slate-200 bg-slate-950 text-slate-300"><div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"><b class="text-white">CombosPlus</b><span class="ml-4 text-sm">Marketplace de comercios independientes.</span></div></footer>
</body></html>