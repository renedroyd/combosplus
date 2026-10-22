@extends('layouts.marketplace')
@section('title','Explora tiendas — Nexo')
@section('content')
<section class="relative isolate overflow-hidden bg-gradient-to-br from-violet-950 via-violet-800 to-fuchsia-700 text-white">
    <div aria-hidden="true" class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-orange-400/30 blur-3xl"></div>
    <div aria-hidden="true" class="absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-cyan-400/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <p class="text-xs font-black uppercase tracking-[.22em] text-amber-300">Conoce a quienes venden en Nexo</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-white sm:text-5xl">Cada tienda tiene algo especial.</h1>
        <p class="mt-4 max-w-2xl text-base font-medium leading-7 text-white sm:text-lg">Encuentra comercios, explora sus catálogos y visita sus espacios para descubrir lo que ofrecen.</p>
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-6"><p class="text-xs font-black uppercase tracking-[.2em] text-violet-800">Directorio de comercios</p><p class="mt-2 font-medium text-slate-700">Busca por nombre o descripción y filtra por categoría.</p></div>
    <form method="GET" class="grid gap-3 rounded-2xl border border-violet-100 bg-violet-50 p-4 shadow-sm sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="sr-only" for="store-search">Buscar tiendas</label>
        <input id="store-search" name="q" value="{{ $query }}" placeholder="Buscar por nombre o descripción..." class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 text-slate-950 placeholder:text-slate-500 focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200">
        <label class="sr-only" for="store-category">Categoría</label>
        <select id="store-category" name="category" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 text-slate-950 focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200">
            <option value="">Todas las categorías</option>
            @foreach($categories as $item)
                <option value="{{ $item->name }}" @selected(mb_strtolower($category) === mb_strtolower($item->name))>{{ $item->name }} ({{ $item->product_count }})</option>
            @endforeach
        </select>
        <label class="sr-only" for="store-sort">Ordenar</label>
        <select id="store-sort" name="sort" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 text-slate-950 focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200">
            <option value="rating" @selected($sort === 'rating')>Más valoradas</option>
            <option value="newest" @selected($sort === 'newest')>Más recientes</option>
            <option value="name" @selected($sort === 'name')>Nombre A–Z</option>
        </select>
        <button class="min-h-11 rounded-xl bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 font-extrabold text-white shadow-md transition hover:from-violet-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:ring-offset-2">Aplicar filtros</button>
    </form>
    @if($query !== '' || $category !== '')
        <div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-slate-700"><span>Filtros activos:</span>
            @if($query !== '')<span class="rounded-full bg-violet-100 px-3 py-1 font-semibold text-violet-950">“{{ $query }}”</span>@endif
            @if($category !== '')<span class="rounded-full bg-fuchsia-100 px-3 py-1 font-semibold text-fuchsia-950">{{ $category }}</span>@endif
            <a href="{{ route('marketplace.stores') }}" class="font-bold text-violet-800 underline underline-offset-2 hover:text-fuchsia-800">Limpiar filtros</a>
        </div>
    @endif
    <div class="mt-8 flex items-center justify-between gap-3"><h2 class="text-xl font-black text-slate-950 sm:text-2xl">Tiendas para descubrir</h2><span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-black text-amber-950">{{ $stores->count() }} {{ $stores->count() === 1 ? 'tienda' : 'tiendas' }}</span></div>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($stores as $store)
            <a href="{{ route('marketplace.store',$store) }}" class="group flex h-full flex-col rounded-3xl border border-violet-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-fuchsia-200 hover:shadow-xl hover:shadow-violet-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <div class="flex items-start gap-4">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 via-fuchsia-600 to-orange-400 text-xl font-black text-white shadow-lg">{{ mb_strtoupper(mb_substr($store->name,0,1)) }}</div>
                    <div class="min-w-0 flex-1"><h3 class="break-words text-xl font-black text-slate-950 group-hover:text-violet-800">{{ $store->name }}</h3><p class="mt-1 text-sm font-medium text-slate-600">{{ $store->review_count }} {{ $store->review_count === 1 ? 'opinión' : 'opiniones' }}</p></div>
                </div>
                <div class="mt-5"><span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-black text-amber-950">★ {{ number_format((float)$store->rating,1) }} <span class="ml-1 font-semibold">valoración</span></span></div>
                <p class="mt-4 flex-1 text-sm font-medium leading-6 text-slate-700">{{ $store->description ?: 'Descubre los productos y novedades de esta tienda en Nexo.' }}</p>
                <span class="mt-5 inline-flex items-center self-start rounded-xl bg-fuchsia-50 px-4 py-2.5 text-sm font-black text-fuchsia-900 transition group-hover:bg-fuchsia-100">Visitar tienda <span class="ml-2" aria-hidden="true">↗</span></span>
            </a>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-violet-300 bg-violet-50 px-6 py-16 text-center"><p class="text-4xl" aria-hidden="true">🏪</p><h3 class="mt-4 text-xl font-black text-slate-950">No encontramos tiendas con esos criterios.</h3><p class="mt-2 font-medium text-slate-700">Prueba con otra búsqueda o elimina los filtros.</p><a href="{{ route('marketplace.stores') }}" class="mt-5 inline-flex rounded-xl bg-violet-700 px-5 py-3 font-black text-white hover:bg-violet-800">Ver todas las tiendas</a></div>
        @endforelse
    </div>
</section>
@endsection