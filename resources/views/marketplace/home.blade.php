@extends('layouts.marketplace')
@section('title','CombosPlus — Marketplace')
@section('content')
<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 sm:py-20">
        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-sm text-blue-200">Marketplace de tiendas independientes</span>
        <h1 class="mt-6 max-w-4xl text-4xl font-semibold tracking-tight sm:text-6xl">Descubre tiendas y productos que merecen ser encontrados.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">Explora negocios, encuentra productos y utiliza las valoraciones de la comunidad para decidir dónde comprar.</p>
        <form action="{{ route('marketplace.stores') }}" method="GET" class="mt-8 flex max-w-2xl flex-col gap-3 sm:flex-row">
            <label class="sr-only" for="marketplace-search">Buscar tiendas</label>
            <input id="marketplace-search" name="q" type="search" placeholder="Buscar por nombre o descripción..." class="min-h-12 flex-1 rounded-xl border-0 px-4 text-slate-900">
            <button class="min-h-12 rounded-xl bg-blue-600 px-6 font-semibold hover:bg-blue-500">Explorar</button>
        </form>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="border-b border-slate-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Explora</p><h2 class="mt-1 text-2xl font-semibold">Categorías populares</h2></div>
            <span class="hidden text-sm text-slate-500 sm:block">Basadas en productos visibles</span>
        </div>
        <div class="mt-5 flex gap-3 overflow-x-auto pb-1">
            @foreach($categories as $category)
                <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700">{{ $category->name }} <span class="text-slate-400">· {{ $category->product_count }}</span></span>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Reputación</p><h2 class="mt-1 text-3xl font-semibold">Tiendas destacadas</h2></div><a href="{{ route('marketplace.stores') }}" class="text-sm font-semibold text-blue-600">Ver todas →</a></div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($stores as $store)
            <a href="{{ route('marketplace.store',$store) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-center gap-4"><div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-blue-50 font-bold text-blue-600">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</div><div class="min-w-0"><h3 class="truncate font-semibold group-hover:text-blue-600">{{ $store->name }}</h3><p class="text-sm text-slate-500">{{ $store->review_count }} opiniones</p></div></div>
                <div class="mt-5 text-sm"><span class="text-amber-500">★</span> <b>{{ number_format((float)$store->rating,1) }}</b></div>
                <p class="mt-3 line-clamp-2 text-sm text-slate-500">{{ $store->description ?: 'Conoce esta tienda en CombosPlus.' }}</p>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-500">Aún no hay tiendas publicadas. Registra tu negocio y sé de los primeros.</div>
        @endforelse
    </div>
</section>

@if($newStores->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Novedades</p><h2 class="mt-1 text-3xl font-semibold">Nuevas tiendas</h2>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($newStores as $store)
                <a href="{{ route('marketplace.store',$store) }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-slate-100 font-bold text-slate-700">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</div>
                    <div class="min-w-0"><h3 class="truncate font-semibold group-hover:text-blue-600">{{ $store->name }}</h3><p class="mt-1 line-clamp-1 text-sm text-slate-500">{{ $store->description ?: 'Nuevo comercio en CombosPlus.' }}</p></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Descubrimiento</p><h2 class="mt-1 text-3xl font-semibold">Productos destacados</h2></div></div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($featuredProducts as $product)
            <a href="{{ route('marketplace.product',[$product->marketplace_tenant_id,$product->getKey()]) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="aspect-[4/3] bg-slate-100">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">@else<div class="grid h-full place-items-center text-sm text-slate-400">Sin imagen</div>@endif</div>
                <div class="p-5"><h3 class="font-semibold group-hover:text-blue-600">{{ $product->name }}</h3><div class="mt-3 flex justify-between gap-3"><b>{{ '$'.number_format((float)$product->price,2) }}</b><span class="text-amber-500">★ {{ number_format((float)($product->rating ?? 0),1) }}</span></div></div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-500">Los productos destacados aparecerán aquí.</div>
        @endforelse
    </div>
</section>

@if($recentProducts->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Recién añadidos</p><h2 class="mt-1 text-3xl font-semibold">Nuevos productos</h2>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($recentProducts as $product)
                <a href="{{ route('marketplace.product',[$product->marketplace_tenant_id,$product->getKey()]) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-lg">
                    <h3 class="font-semibold group-hover:text-blue-600">{{ $product->name }}</h3><p class="mt-2 text-sm text-slate-500">{{ $product->description ?: 'Producto disponible en el Marketplace.' }}</p><div class="mt-4 flex justify-between gap-3 text-sm"><b>{{ '$'.number_format((float)$product->price,2) }}</b><span class="text-slate-500">Ver producto →</span></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div><p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Experiencias reales</p><h2 class="mt-1 text-3xl font-semibold">Lo que dicen nuestros clientes</h2></div>
    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
        @foreach($storeReviews as $review)<article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="text-amber-500">★★★★★ <span class="ml-1 text-sm text-slate-500">{{ $review->rating }}/5</span></div><p class="mt-4 line-clamp-3 text-slate-700">“{{ $review->comment ?: 'Una experiencia valorada por nuestra comunidad.' }}”</p><p class="mt-5 text-sm font-semibold text-slate-900">{{ $review->tenant?->name ?: 'Tienda' }}</p><p class="text-xs text-slate-500">{{ $review->user?->name ?: 'Cliente' }}</p></article>@endforeach
        @foreach($platformReviews as $review)<article class="rounded-2xl border border-blue-100 bg-blue-50 p-6"><div class="text-amber-500">★★★★★ <span class="ml-1 text-sm text-slate-500">{{ $review->rating }}/5</span></div><p class="mt-4 line-clamp-3 text-slate-700">“{{ $review->comment ?: 'Una opinión sobre la experiencia en CombosPlus.' }}”</p><p class="mt-5 text-sm font-semibold text-slate-900">Experiencia con CombosPlus</p><p class="text-xs text-slate-500">{{ $review->user?->name ?: 'Cliente' }}</p></article>@endforeach
        @if($storeReviews->isEmpty() && $platformReviews->isEmpty())<div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">Las opiniones aparecerán aquí a medida que la comunidad comparta sus experiencias.</div>@endif
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8"><div class="grid gap-6 md:grid-cols-2"><div class="rounded-3xl bg-slate-900 p-8 text-white"><p class="text-sm font-semibold text-blue-300">Para clientes</p><h2 class="mt-2 text-2xl font-semibold">Explora. Compara. Descubre.</h2><p class="mt-3 text-slate-300">Encuentra comercios y productos publicados por negocios independientes.</p></div><div class="rounded-3xl border border-blue-100 bg-blue-50 p-8"><p class="text-sm font-semibold text-blue-700">Para negocios</p><h2 class="mt-2 text-2xl font-semibold text-slate-900">Tu tienda puede estar aquí.</h2><p class="mt-3 text-slate-600">Crea tu espacio, completa el catálogo y publícalo cuando esté listo.</p><a href="{{ route('marketplace.register') }}" class="mt-6 inline-flex rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-500">Registrar mi tienda</a></div></div></section>
@endsection