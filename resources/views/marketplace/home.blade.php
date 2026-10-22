@extends('layouts.marketplace')
@section('title','NEXO — Conecta · Descubre · Compra')
@section('content')
@php
    $categoryIcons = [
        'tecnología'=>'🎧','tecnologia'=>'🎧','electrónica'=>'🎧','electronica'=>'🎧',
        'hogar'=>'🏠','cocina'=>'🍳','moda'=>'👗','belleza'=>'🩷','deportes'=>'⚽',
        'salud'=>'❤️','bienestar'=>'❤️','supermercado'=>'🛒','ferretería'=>'🔨','ferreteria'=>'🔨',
        'juguetes'=>'🧸','automotriz'=>'🚙','calzado'=>'👟','alimentos'=>'🥑',
    ];
    $categoryColors = ['bg-sky-100','bg-amber-100','bg-pink-100','bg-blue-100','bg-rose-100','bg-emerald-100','bg-orange-100','bg-yellow-100','bg-cyan-100','bg-violet-100'];
@endphp
<section class="nexo-hero relative isolate overflow-hidden bg-[#111] text-white">
    <div class="absolute inset-0 -z-20 bg-cover bg-center" style="background-image:linear-gradient(90deg,rgba(0,0,0,.88) 0%,rgba(0,0,0,.42) 43%,rgba(0,0,0,.18) 72%,rgba(0,0,0,.8) 100%),url('https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=2200&q=90')"></div>
    <div class="mx-auto grid min-h-[300px] max-w-[1400px] items-center gap-6 px-5 py-8 sm:min-h-[340px] sm:px-8 lg:grid-cols-[1fr_240px] lg:px-10">
        <div class="max-w-2xl">
            <h1 class="text-4xl font-black leading-[.98] tracking-tight sm:text-5xl lg:text-6xl">Tu mundo,<br><span class="text-yellow-400">más cerca</span></h1>
            <p class="mt-4 max-w-lg text-sm leading-6 text-white sm:text-base">En Nexo encuentras todo lo que necesitas: tecnología, moda, hogar, deportes, belleza y mucho más. Las mejores marcas, los mejores precios.</p>
            <a href="{{ route('marketplace.products') }}" class="mt-5 inline-flex min-h-10 items-center gap-3 rounded-full bg-yellow-400 px-6 py-2.5 text-sm font-black text-slate-950 hover:bg-yellow-300">Comprar ahora <span class="text-lg">→</span></a>
        </div>
        <div class="hidden space-y-4 lg:block">
            <div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-full border border-white/30 bg-black/40 text-2xl">🚚</span><div><p class="font-black">Envíos rápidos</p><p class="text-xs text-white/80">A todo el país</p></div></div>
            <div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-full border border-white/30 bg-black/40 text-2xl">🛡</span><div><p class="font-black">Compra segura</p><p class="text-xs text-white/80">Tus datos protegidos</p></div></div>
            <div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-full border border-white/30 bg-black/40 text-2xl">🎧</span><div><p class="font-black">Atención 24/7</p><p class="text-xs text-white/80">Siempre para ti</p></div></div>
        </div>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="mx-auto max-w-[1400px] px-4 pt-5 sm:px-7 lg:px-10">
    <div class="nexo-panel grid grid-cols-5 gap-2 rounded-2xl p-3 sm:grid-cols-5 sm:gap-4 sm:p-5 lg:grid-cols-10">
        @foreach($categories->take(10) as $category)
            @php $icon = $categoryIcons[mb_strtolower($category->name)] ?? '🛍️'; $tile = $categoryColors[$loop->index % count($categoryColors)]; @endphp
            <a href="{{ route('marketplace.products', ['category'=>$category->name]) }}" class="group flex min-w-0 flex-col items-center gap-2 rounded-xl p-1 text-center transition hover:-translate-y-1">
                <span class="{{ $tile }} grid h-12 w-12 place-items-center rounded-2xl text-2xl shadow-sm transition group-hover:scale-105 sm:h-16 sm:w-16 sm:text-3xl">{{ $icon }}</span>
                <span class="text-[10px] font-extrabold leading-tight text-slate-800 sm:text-xs">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="mx-auto max-w-[1400px] px-4 py-4 sm:px-7 lg:px-10">
    <div class="grid grid-cols-2 gap-2 rounded-2xl bg-gradient-to-r from-indigo-50 via-white to-violet-50 px-3 py-4 sm:grid-cols-4 sm:gap-4 sm:px-6">
        <div class="flex items-center gap-2 border-slate-200 px-1 sm:gap-3 sm:border-r"><span class="text-3xl sm:text-4xl">🚀</span><div><p class="text-xs font-black text-slate-900 sm:text-sm">Descubre más</p><p class="text-[10px] text-slate-600 sm:text-xs">Tiendas y productos</p></div></div>
        <div class="flex items-center gap-2 px-1 sm:gap-3 sm:border-r sm:border-slate-200"><span class="text-3xl sm:text-4xl">🛡️</span><div><p class="text-xs font-black text-slate-900 sm:text-sm">Compra informada</p><p class="text-[10px] text-slate-600 sm:text-xs">Detalles a tu alcance</p></div></div>
        <div class="flex items-center gap-2 border-slate-200 px-1 sm:gap-3 sm:border-r"><span class="text-3xl sm:text-4xl">🎧</span><div><p class="text-xs font-black text-slate-900 sm:text-sm">Conoce las tiendas</p><p class="text-[10px] text-slate-600 sm:text-xs">Explora cada catálogo</p></div></div>
        <div class="flex items-center gap-2 px-1 sm:gap-3"><span class="text-3xl sm:text-4xl">🤝</span><div><p class="text-xs font-black text-slate-900 sm:text-sm">Negocios conectados</p><p class="text-[10px] text-slate-600 sm:text-xs">Compra local y descubre</p></div></div>
    </div>
</section>

<section class="mx-auto max-w-[1400px] px-4 py-4 sm:px-7 lg:px-10">
    <div class="grid gap-4 lg:grid-cols-[1.85fr_1fr]">
        <div class="nexo-panel overflow-hidden rounded-2xl p-4 sm:p-5">
            <div class="mb-4 flex items-end justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-[.18em] text-orange-600">Explora el catálogo</p><h2 class="mt-1 text-xl font-black text-slate-950 sm:text-2xl">Productos destacados</h2></div><a href="{{ route('marketplace.products') }}" class="shrink-0 text-xs font-extrabold text-violet-800 hover:text-orange-600 sm:text-sm">Ver todos →</a></div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
                @forelse($featuredProducts->take(6) as $product)
                    <a href="{{ route('marketplace.product',[$product->marketplace_tenant_slug,$product->getKey()]) }}" class="group flex min-w-0 flex-col overflow-hidden rounded-xl border border-slate-100 bg-white transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg">
                        <div class="aspect-square overflow-hidden bg-slate-50 p-2 sm:p-3">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-500 group-hover:scale-105" loading="lazy">@else<div class="grid h-full place-items-center text-5xl">🛍️</div>@endif</div>
                        <div class="flex flex-1 flex-col p-3"><p class="line-clamp-2 min-h-10 text-xs font-semibold leading-5 text-slate-800 sm:text-sm">{{ $product->name }}</p><p class="mt-2 text-base font-black text-red-600">{{ number_format((float)$product->price,2) }}</p><span class="mt-3 rounded-lg bg-orange-500 px-2 py-2 text-center text-xs font-black text-white transition group-hover:bg-orange-600">Ver producto →</span></div>
                    </a>
                @empty
                    <div class="col-span-full rounded-xl bg-slate-50 p-8 text-center"><p class="font-bold text-slate-800">Pronto verás productos aquí.</p><a href="{{ route('marketplace.stores') }}" class="mt-2 inline-block text-sm font-bold text-violet-700">Explorar tiendas →</a></div>
                @endforelse
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            <a href="{{ route('marketplace.stores') }}" class="group relative flex min-h-40 overflow-hidden rounded-2xl bg-gradient-to-br from-teal-950 via-teal-800 to-emerald-600 p-5 text-white shadow-lg sm:min-h-44"><img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=700&q=80" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-35 transition duration-500 group-hover:scale-105"><div class="relative m-auto w-full"><p class="text-xs font-black uppercase tracking-widest text-amber-300">Descubre algo nuevo</p><h3 class="mt-2 text-2xl font-black">Un mundo de tiendas</h3><p class="mt-1 text-sm text-white/90">Encuentra comercios y visita sus catálogos.</p><span class="mt-4 inline-flex rounded-full bg-amber-300 px-4 py-2 text-sm font-black text-slate-950">Explorar tiendas →</span></div></a>
            <a href="{{ route('marketplace.register') }}" class="group relative flex min-h-40 overflow-hidden rounded-2xl bg-gradient-to-br from-violet-950 via-indigo-800 to-fuchsia-700 p-5 text-white shadow-lg sm:min-h-44"><div class="absolute -right-5 -top-8 text-[110px] opacity-20">✦</div><div class="relative m-auto w-full"><p class="text-xs font-black uppercase tracking-widest text-amber-300">¿Tienes un negocio?</p><h3 class="mt-2 text-2xl font-black">Vende en NEXO</h3><p class="mt-1 text-sm text-white/90">Crea tu tienda y muestra tus productos.</p><span class="mt-4 inline-flex rounded-full bg-orange-500 px-4 py-2 text-sm font-black text-white">Crear mi tienda ↗</span></div></a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-[1400px] px-4 py-5 sm:px-7 lg:px-10">
    <div class="mb-4 flex items-end justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-[.18em] text-violet-700">Comunidad NEXO</p><h2 class="mt-1 text-xl font-black text-slate-950 sm:text-2xl">Tiendas para descubrir</h2><p class="mt-1 text-sm text-slate-600">Visita cada comercio y explora su catálogo.</p></div><a href="{{ route('marketplace.stores') }}" class="shrink-0 text-xs font-extrabold text-violet-800 sm:text-sm">Todas las tiendas →</a></div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($stores->take(4) as $store)
            <a href="{{ route('marketplace.store',$store->slug) }}" class="nexo-panel group rounded-2xl p-4 transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg"><div class="flex items-center gap-3"><span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-700 to-fuchsia-500 text-xl font-black text-white">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</span><div class="min-w-0"><h3 class="truncate font-black text-slate-900 group-hover:text-violet-700">{{ $store->name }}</h3><p class="text-xs text-slate-500">{{ $store->review_count }} {{ $store->review_count===1?'opinión':'opiniones' }}</p></div></div><p class="mt-3 line-clamp-2 min-h-10 text-sm leading-5 text-slate-600">{{ $store->description ?: 'Descubre productos y novedades de esta tienda.' }}</p><span class="mt-3 inline-flex text-sm font-black text-orange-600">Visitar tienda →</span></a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-violet-200 bg-violet-50 p-8 text-center"><p class="font-bold text-slate-900">Las tiendas aparecerán aquí.</p><a href="{{ route('marketplace.register') }}" class="mt-2 inline-block font-bold text-violet-700">Registra tu tienda →</a></div>
        @endforelse
    </div>
</section>

@if($newStores->isNotEmpty())
<section class="mx-auto max-w-[1400px] px-4 py-5 sm:px-7 lg:px-10"><h2 class="text-xl font-black text-slate-950">Nuevas tiendas en NEXO</h2><div class="mt-4 grid gap-3 sm:grid-cols-3">@foreach($newStores->take(3) as $store)<a href="{{ route('marketplace.store',$store->slug) }}" class="nexo-panel flex items-center gap-3 rounded-xl p-4 hover:border-emerald-300"><span class="grid h-11 w-11 place-items-center rounded-xl bg-emerald-100 font-black text-emerald-800">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</span><span class="min-w-0"><span class="block truncate font-bold text-slate-900">{{ $store->name }}</span><span class="block truncate text-xs text-slate-500">{{ $store->description ?: 'Nueva en NEXO' }}</span></span></a>@endforeach</div></section>
@endif
@endsection