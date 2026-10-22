@extends('layouts.marketplace')
@section('title','Nexo — Compra, vende y descubre algo nuevo')
@section('content')
<section class="relative isolate overflow-hidden bg-gradient-to-br from-indigo-950 via-indigo-900 to-violet-800 text-white">
    <div aria-hidden="true" class="pointer-events-none absolute -right-24 -top-28 h-96 w-96 rounded-full bg-fuchsia-500/25 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-36 left-1/3 h-80 w-80 rounded-full bg-amber-400/20 blur-3xl"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-24">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-amber-200 backdrop-blur"><span aria-hidden="true" class="text-amber-300">✦</span> Todo lo que buscas, en un solo lugar</span>
            <h1 class="mt-6 max-w-3xl text-4xl font-black leading-[1.04] tracking-tight sm:text-6xl lg:text-7xl">Descubre tu próximo <span class="bg-gradient-to-r from-amber-300 via-yellow-200 to-orange-300 bg-clip-text text-transparent">favorito.</span></h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-indigo-100 sm:text-xl">Explora productos increíbles, encuentra tiendas que te encanten y apoya negocios que hacen la diferencia.</p>
            <form action="{{ route('marketplace.products') }}" method="GET" role="search" class="mt-8 flex max-w-2xl flex-col gap-2 rounded-2xl bg-white p-2 shadow-2xl shadow-indigo-950/30 sm:flex-row">
                <label class="sr-only" for="marketplace-product-search">Buscar productos</label>
                <span aria-hidden="true" class="hidden items-center pl-3 text-xl text-slate-400 sm:flex">⌕</span>
                <input id="marketplace-product-search" name="q" type="search" placeholder="¿Qué te gustaría encontrar hoy?" class="min-h-12 min-w-0 flex-1 border-0 bg-transparent px-3 text-slate-900 placeholder:text-slate-400 focus:ring-0">
                <button class="min-h-12 rounded-xl bg-gradient-to-r from-amber-300 to-yellow-400 px-6 font-extrabold text-slate-950 transition hover:from-amber-200 hover:to-yellow-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">Buscar productos <span aria-hidden="true">→</span></button>
            </form>
            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-indigo-100"><span><span class="mr-1 text-emerald-300">✓</span> Tiendas independientes</span><span><span class="mr-1 text-emerald-300">✓</span> Nuevos descubrimientos</span><span><span class="mr-1 text-emerald-300">✓</span> Todo en un mismo lugar</span></div>
        </div>
        <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
            <div aria-hidden="true" class="absolute -left-3 top-8 h-24 w-24 rounded-3xl bg-fuchsia-400/70 rotate-12 blur-[1px] sm:-left-5"></div>
            <div aria-hidden="true" class="absolute -right-3 bottom-8 h-28 w-28 rounded-full bg-amber-300/80 blur-[1px]"></div>
            <div class="relative grid grid-cols-2 gap-4">
                <a href="{{ route('marketplace.products') }}" class="group relative flex min-h-48 flex-col justify-between overflow-hidden rounded-[2rem] bg-orange-300 p-5 text-white shadow-xl sm:min-h-56 sm:p-7">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=700&q=85" alt="Reloj moderno, accesorios y artículos de estilo" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-110" fetchpriority="high">
                    <span class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/15 to-slate-950/10"></span><span class="relative text-xs font-extrabold uppercase tracking-[.18em]">Estilo y accesorios</span><span class="relative mt-12 text-xl font-black sm:text-2xl">Pequeños grandes hallazgos <span aria-hidden="true">↗</span></span>
                </a>
                <a href="{{ route('marketplace.products') }}" class="group relative mt-9 flex min-h-48 flex-col justify-between overflow-hidden rounded-[2rem] bg-cyan-300 p-5 text-white shadow-xl sm:min-h-56 sm:p-7">
                    <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=700&q=85" alt="Tecnología y dispositivos electrónicos" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-110" fetchpriority="high">
                    <span class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/15 to-slate-950/10"></span><span class="relative text-xs font-extrabold uppercase tracking-[.18em]">Tecnología</span><span class="relative mt-12 text-xl font-black sm:text-2xl">Ideas que conectan <span aria-hidden="true">↗</span></span>
                </a>
                <a href="{{ route('marketplace.products') }}" class="group relative -mt-5 flex min-h-44 flex-col justify-between overflow-hidden rounded-[2rem] bg-emerald-300 p-5 text-white shadow-xl sm:min-h-52 sm:p-7">
                    <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=85" alt="Decoración y artículos para el hogar" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-110" loading="lazy">
                    <span class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/15 to-slate-950/10"></span><span class="relative text-xs font-extrabold uppercase tracking-[.18em]">Hogar</span><span class="relative mt-10 text-xl font-black sm:text-2xl">Dale vida a tus espacios <span aria-hidden="true">↗</span></span>
                </a>
                <a href="{{ route('marketplace.products') }}" class="group relative flex min-h-44 flex-col justify-between overflow-hidden rounded-[2rem] bg-rose-300 p-5 text-white shadow-xl sm:min-h-52 sm:p-7">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=700&q=85" alt="Calzado deportivo en color rojo" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-110" loading="lazy">
                    <span class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/15 to-slate-950/10"></span><span class="relative text-xs font-extrabold uppercase tracking-[.18em]">Moda y deporte</span><span class="relative mt-10 text-xl font-black sm:text-2xl">Encuentra tu próximo favorito <span aria-hidden="true">↗</span></span>
                </a>
            </div>
        </div>
    </div>
</section>


<section aria-label="Descubre el universo Nexo" class="nexo-parallax relative isolate overflow-hidden bg-violet-950 text-white" style="background-image: linear-gradient(90deg, rgba(46,16,101,.94), rgba(88,28,135,.76), rgba(15,23,42,.70)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=2000&q=85')">
    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-r from-fuchsia-700/20 via-transparent to-orange-500/20"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8 lg:py-28">
        <p class="text-xs font-black uppercase tracking-[.25em] text-amber-300 sm:text-sm">Más que comprar: descubrir</p>
        <h2 class="mt-4 max-w-3xl text-3xl font-black leading-tight text-white drop-shadow-lg sm:text-5xl lg:text-6xl">Un mundo de tiendas. <span class="text-amber-300">Tu próxima historia.</span></h2>
        <p class="mt-5 max-w-2xl text-base font-medium leading-7 text-white drop-shadow sm:text-xl sm:leading-8">Recorre comercios independientes, conoce sus propuestas y encuentra eso que no sabías que estabas buscando.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('marketplace.stores') }}" class="inline-flex min-h-12 items-center rounded-xl bg-amber-300 px-6 py-3 font-black text-slate-950 shadow-lg transition hover:-translate-y-0.5 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-violet-900">Explorar tiendas <span class="ml-2" aria-hidden="true">↗</span></a>
            <a href="{{ route('marketplace.products') }}" class="inline-flex min-h-12 items-center rounded-xl border-2 border-white/80 bg-slate-950/30 px-6 py-3 font-extrabold text-white transition hover:bg-white hover:text-violet-950 focus:outline-none focus:ring-2 focus:ring-amber-300">Ver productos</a>
        </div>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[.2em] text-violet-800">Encuentra tu estilo</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Explora por categoría</h2></div><a href="{{ route('marketplace.products') }}" class="shrink-0 text-sm font-bold text-violet-700 hover:text-fuchsia-700">Ver catálogo <span aria-hidden="true">→</span></a></div>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-5">
            @foreach($categories as $category)
                @php
                    $categoryIcon = match (mb_strtolower($category->name)) {
                        'tecnología', 'tecnologia', 'electrónica', 'electronica' => '🎧',
                        'moda', 'ropa', 'vestuario' => '👕',
                        'hogar', 'casa', 'muebles' => '🪴',
                        'deportes', 'deporte' => '⚽',
                        'belleza', 'salud' => '✨',
                        'alimentos', 'comida' => '🍓',
                        'juguetes', 'niños', 'ninos' => '🧸',
                        'calzado', 'zapatos' => '👟',
                        default => '🛍️',
                    };
                    $categoryImage = match (mb_strtolower($category->name)) {
                        'tecnología', 'tecnologia', 'electrónica', 'electronica' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=600&q=80',
                        'moda', 'ropa', 'vestuario' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80',
                        'hogar', 'casa', 'muebles' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=600&q=80',
                        'deportes', 'deporte' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=600&q=80',
                        'belleza', 'salud' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=600&q=80',
                        'alimentos', 'comida' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80',
                        'juguetes', 'niños', 'ninos' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?auto=format&fit=crop&w=600&q=80',
                        'calzado', 'zapatos' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80',
                        default => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=600&q=80',
                    };
                @endphp
                <a href="{{ route('marketplace.products', ['category' => $category->name]) }}" class="group overflow-hidden rounded-2xl border border-slate-100 bg-white transition duration-200 hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg hover:shadow-violet-100/70 focus:outline-none focus:ring-2 focus:ring-violet-500"><div class="relative h-28 overflow-hidden bg-violet-50 sm:h-36"><img src="{{ $categoryImage }}" alt="Productos de {{ $category->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" loading="lazy"><span class="absolute inset-0 bg-gradient-to-t from-slate-950/35 to-transparent"></span><span class="absolute bottom-2 right-2 rounded-full bg-white/90 px-2 py-1 text-lg shadow-sm" aria-hidden="true">{{ $categoryIcon }}</span></div><div class="p-3 sm:p-4"><span class="block font-extrabold text-slate-800 group-hover:text-violet-800">{{ $category->name }}</span><span class="mt-1 block text-xs text-slate-500">{{ $category->product_count }} {{ $category->product_count === 1 ? 'producto' : 'productos' }}</span></div></a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-extrabold uppercase tracking-[.2em] text-fuchsia-700">Favoritas de la comunidad</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Tiendas que vale la pena descubrir</h2><p class="mt-2 max-w-2xl text-sm font-medium text-slate-700 sm:text-base">Conoce a los vendedores y encuentra tu próximo lugar favorito para comprar.</p></div><a href="{{ route('marketplace.stores') }}" class="shrink-0 text-sm font-bold text-violet-700 hover:text-fuchsia-700">Todas las tiendas <span aria-hidden="true">→</span></a></div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($stores as $store)
            <a href="{{ route('marketplace.store',$store->slug) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-violet-200 hover:shadow-xl hover:shadow-violet-100/60">
                <div class="flex items-center gap-4"><div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-xl font-black text-white shadow-lg shadow-violet-200">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</div><div class="min-w-0"><h3 class="truncate font-extrabold text-slate-900 group-hover:text-violet-700">{{ $store->name }}</h3><p class="text-sm text-slate-500">{{ $store->review_count }} {{ $store->review_count === 1 ? 'opinión' : 'opiniones' }}</p></div></div>
                <div class="mt-5 flex items-center gap-2"><span class="rounded-full bg-amber-50 px-2.5 py-1 text-sm font-extrabold text-amber-700">★ {{ number_format((float)$store->rating,1) }}</span><span class="text-xs text-slate-500">Valoración de clientes</span></div>
                <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-600">{{ $store->description ?: 'Descubre los productos y novedades de esta tienda en Nexo.' }}</p>
                <span class="mt-5 inline-flex items-center text-sm font-extrabold text-violet-700 group-hover:text-fuchsia-700">Visitar tienda <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-violet-200 bg-violet-50 p-10 text-center"><p class="text-3xl" aria-hidden="true">🏪</p><h3 class="mt-3 text-lg font-bold text-slate-900">Tu tienda puede ser una de las primeras</h3><p class="mt-2 text-sm text-slate-600">Abre tu espacio en Nexo y empieza a preparar tu catálogo.</p><a href="{{ route('marketplace.register') }}" class="mt-5 inline-flex rounded-xl bg-violet-700 px-5 py-3 font-bold text-white hover:bg-violet-800">Crear mi tienda</a></div>
        @endforelse
    </div>
</section>

<section class="overflow-hidden bg-gradient-to-r from-orange-500 via-rose-500 to-fuchsia-600 text-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
        <div class="max-w-2xl"><span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-extrabold uppercase tracking-[.18em]">Tu próximo hallazgo está aquí</span><h2 class="mt-3 text-2xl font-black sm:text-3xl">Un mundo de productos. Miles de razones para explorar.</h2><p class="mt-2 text-rose-50">Busca por nombre o categoría y encuentra algo que te encante.</p></div>
        <a href="{{ route('marketplace.products') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-6 py-3.5 font-extrabold text-rose-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-rose-50">Explorar productos <span class="ml-2" aria-hidden="true">→</span></a>
    </div>
</section>

@if($newStores->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-extrabold uppercase tracking-[.2em] text-emerald-700">Recién llegados</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Demos la bienvenida a nuevas tiendas</h2></div><a href="{{ route('marketplace.stores', ['sort' => 'newest']) }}" class="shrink-0 text-sm font-bold text-violet-700 hover:text-fuchsia-700">Ver novedades →</a></div>
        <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($newStores as $store)
                <a href="{{ route('marketplace.store',$store->slug) }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-emerald-200 hover:shadow-lg">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-emerald-400 to-cyan-400 font-black text-slate-950">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</div><div class="min-w-0 flex-1"><h3 class="truncate font-extrabold text-slate-900 group-hover:text-emerald-700">{{ $store->name }}</h3><p class="mt-1 line-clamp-1 text-sm text-slate-500">{{ $store->description ?: 'Nuevo comercio en Nexo.' }}</p></div><span class="text-xl text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-600" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-extrabold uppercase tracking-[.2em] text-violet-700">Selección Nexo</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Productos que te pueden encantar</h2><p class="mt-2 text-sm text-slate-600 sm:text-base">Una selección de productos de tiendas con catálogo publicado.</p></div><a href="{{ route('marketplace.products') }}" class="shrink-0 text-sm font-bold text-violet-700 hover:text-fuchsia-700">Ver catálogo →</a></div>
    <div class="mt-8 grid gap-4 grid-cols-2 sm:gap-5 lg:grid-cols-4">
        @forelse($featuredProducts as $product)
            <a href="{{ route('marketplace.product',[$product->marketplace_tenant_slug,$product->getKey()]) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-violet-200 hover:shadow-xl hover:shadow-violet-100/60">
                <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-slate-50 to-violet-50 p-3 sm:p-5">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105" loading="lazy">@else<div class="grid h-full place-items-center text-center"><span class="text-4xl sm:text-5xl" aria-hidden="true">🛍️</span><span class="mt-2 text-xs font-medium text-slate-400">Imagen próximamente</span></div>@endif<div class="absolute left-3 top-3 rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-emerald-800 sm:text-xs">Descúbrelo</div></div>
                <div class="flex flex-1 flex-col p-3 sm:p-4"><h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-slate-800 group-hover:text-violet-700 sm:text-base">{{ $product->name }}</h3><div class="mt-3 flex flex-wrap items-center justify-between gap-2"><b class="text-base font-black text-slate-950 sm:text-lg">{{ '$'.number_format((float)$product->price,2) }}</b><span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700">★ {{ number_format((float)($product->rating ?? 0),1) }}</span></div><span class="mt-4 inline-flex items-center text-xs font-extrabold text-violet-700 sm:text-sm">Ver producto <span class="ml-1 transition group-hover:translate-x-1" aria-hidden="true">→</span></span></div>
            </a>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-violet-200 bg-violet-50 p-10 text-center"><p class="text-3xl" aria-hidden="true">📦</p><h3 class="mt-3 text-lg font-bold text-slate-900">Pronto habrá productos para descubrir</h3><p class="mt-2 text-sm text-slate-600">Mientras tanto, conoce las tiendas que ya forman parte de Nexo.</p><a href="{{ route('marketplace.stores') }}" class="mt-5 inline-flex rounded-xl bg-violet-700 px-5 py-3 font-bold text-white hover:bg-violet-800">Explorar tiendas</a></div>
        @endforelse
    </div>
</section>


<section aria-label="Explora nuevos hallazgos" class="nexo-parallax relative isolate overflow-hidden bg-slate-950 text-white" style="background-image: linear-gradient(90deg, rgba(124,45,18,.92), rgba(190,24,93,.78), rgba(76,29,149,.82)), url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=2000&q=85')">
    <div class="relative mx-auto flex max-w-7xl flex-col gap-7 px-4 py-14 sm:px-6 sm:py-20 md:flex-row md:items-center md:justify-between lg:px-8 lg:py-24">
        <div class="max-w-3xl"><p class="text-xs font-black uppercase tracking-[.25em] text-amber-300 sm:text-sm">Tu próxima sorpresa te espera</p><h2 class="mt-4 text-3xl font-black leading-tight text-white drop-shadow-lg sm:text-5xl">Sal de lo de siempre. <span class="text-amber-300">Encuentra algo distinto.</span></h2><p class="mt-4 max-w-2xl font-medium leading-7 text-white drop-shadow sm:text-lg">Explora negocios nuevos, descubre sus productos y apoya a las tiendas que hacen única a nuestra comunidad.</p></div>
        <a href="{{ route('marketplace.stores', ['sort' => 'newest']) }}" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-white px-6 py-3 font-black text-fuchsia-900 shadow-xl transition hover:-translate-y-0.5 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-white">Descubrir nuevas tiendas <span class="ml-2" aria-hidden="true">↗</span></a>
    </div>
</section>

@if($recentProducts->isNotEmpty())
<section class="bg-gradient-to-b from-slate-50 to-white">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-extrabold uppercase tracking-[.2em] text-cyan-700">Recién publicados</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Lo más nuevo del marketplace</h2></div><a href="{{ route('marketplace.products', ['sort' => 'newest']) }}" class="shrink-0 text-sm font-bold text-violet-700 hover:text-fuchsia-700">Ver lo nuevo →</a></div>
        <div class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
            @foreach($recentProducts as $product)
                <a href="{{ route('marketplace.product',[$product->marketplace_tenant_slug,$product->getKey()]) }}" class="group rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-cyan-200 hover:shadow-lg"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="line-clamp-2 font-bold text-slate-800 group-hover:text-cyan-700">{{ $product->name }}</h3><p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 sm:text-sm">{{ $product->description ?: 'Conoce los detalles de este producto.' }}</p></div><span class="text-xl" aria-hidden="true">🆕</span></div><div class="mt-4 flex items-center justify-between gap-2"><b class="text-sm font-black text-slate-950 sm:text-base">{{ '$'.number_format((float)$product->price,2) }}</b><span class="text-xs font-bold text-cyan-700">Ver →</span></div></a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-2"><p class="text-xs font-extrabold uppercase tracking-[.2em] text-amber-700">Historias de confianza</p><h2 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">La comunidad tiene la palabra</h2><p class="max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">Las experiencias compartidas ayudan a otros clientes a elegir con más confianza.</p></div>
    <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($storeReviews as $review)<article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-2"><span class="text-lg tracking-wide text-amber-500" aria-label="Valoración {{ $review->rating }} de 5">★★★★★</span><span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-extrabold text-amber-800">{{ $review->rating }}/5</span></div><p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-700">“{{ $review->comment ?: 'Una experiencia valorada por nuestra comunidad.' }}”</p><p class="mt-5 text-sm font-extrabold text-slate-900">{{ $review->tenant?->name ?: 'Tienda' }}</p><p class="mt-1 text-xs text-slate-500">Por {{ $review->user?->name ?: 'Cliente' }}</p></article>@endforeach
        @foreach($platformReviews as $review)<article class="rounded-2xl border border-violet-100 bg-gradient-to-br from-violet-50 to-fuchsia-50 p-5"><div class="flex items-center justify-between gap-2"><span class="text-lg tracking-wide text-amber-500" aria-label="Valoración {{ $review->rating }} de 5">★★★★★</span><span class="rounded-full bg-white px-2 py-1 text-xs font-extrabold text-violet-800">{{ $review->rating }}/5</span></div><p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-700">“{{ $review->comment ?: 'Una opinión sobre la experiencia en Nexo.' }}”</p><p class="mt-5 text-sm font-extrabold text-slate-900">Experiencia con Nexo</p><p class="mt-1 text-xs text-slate-500">Por {{ $review->user?->name ?: 'Cliente' }}</p></article>@endforeach
        @if($storeReviews->isEmpty() && $platformReviews->isEmpty())<div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">Las opiniones de la comunidad aparecerán aquí.</div>@endif
    </div>
</section>

<section class="px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl overflow-hidden rounded-[2rem] bg-slate-950 shadow-2xl shadow-violet-200/50 md:grid-cols-2">
        <div class="relative isolate overflow-hidden p-7 text-white sm:p-10 lg:p-12"><div aria-hidden="true" class="absolute -right-16 -top-16 -z-10 h-56 w-56 rounded-full bg-violet-600/50 blur-3xl"></div><span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-extrabold uppercase tracking-[.18em] text-violet-200">Para quienes compran</span><h2 class="mt-4 text-2xl font-black sm:text-3xl">Tu próxima gran compra empieza aquí.</h2><p class="mt-3 max-w-md leading-7 text-slate-300">Explora tiendas, compara opciones y descubre productos de negocios independientes.</p><a href="{{ route('marketplace.products') }}" class="mt-6 inline-flex items-center rounded-xl bg-white px-5 py-3 font-extrabold text-slate-950 transition hover:bg-violet-100">Empezar a explorar <span class="ml-2" aria-hidden="true">→</span></a></div>
        <div class="relative isolate overflow-hidden bg-gradient-to-br from-amber-300 via-orange-300 to-rose-400 p-7 text-slate-950 sm:p-10 lg:p-12"><div aria-hidden="true" class="absolute -bottom-20 -right-16 -z-10 h-64 w-64 rounded-full border-[40px] border-white/20"></div><span class="inline-flex rounded-full bg-slate-950/10 px-3 py-1 text-xs font-extrabold uppercase tracking-[.18em]">Para quienes emprenden</span><h2 class="mt-4 text-2xl font-black sm:text-3xl">Haz que más personas descubran tu negocio.</h2><p class="mt-3 max-w-md leading-7 text-slate-800">Crea tu tienda, prepara tu catálogo y lleva tus productos a una comunidad que está buscando algo nuevo.</p><a href="{{ route('marketplace.register') }}" class="mt-6 inline-flex items-center rounded-xl bg-slate-950 px-5 py-3 font-extrabold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-violet-950">Vender en Nexo <span class="ml-2" aria-hidden="true">↗</span></a></div>
    </div>
</section>
@endsection