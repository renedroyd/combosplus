@extends('layouts.marketplace')
@section('title','Tiendas — CombosPlus')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Marketplace</p>
    <h1 class="mt-2 text-4xl font-semibold">Explora tiendas</h1>
    <p class="mt-3 text-slate-600">Busca por nombre o descripción y filtra por categoría.</p>

    <form method="GET" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="sr-only" for="store-search">Buscar tiendas</label>
        <input id="store-search" name="q" value="{{ $query }}" placeholder="Buscar por nombre o descripción..." class="min-h-11 rounded-xl border border-slate-300 bg-white px-4">
        <label class="sr-only" for="store-category">Categoría</label>
        <select id="store-category" name="category" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4">
            <option value="">Todas las categorías</option>
            @foreach($categories as $item)
                <option value="{{ $item->name }}" @selected(mb_strtolower($category) === mb_strtolower($item->name))>{{ $item->name }} ({{ $item->product_count }})</option>
            @endforeach
        </select>
        <label class="sr-only" for="store-sort">Ordenar</label>
        <select id="store-sort" name="sort" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4">
            <option value="rating" @selected($sort === 'rating')>Más valoradas</option>
            <option value="newest" @selected($sort === 'newest')>Más recientes</option>
            <option value="name" @selected($sort === 'name')>Nombre A–Z</option>
        </select>
        <button class="min-h-11 rounded-xl bg-blue-600 px-5 font-semibold text-white hover:bg-blue-500">Aplicar</button>
    </form>

    @if($query !== '' || $category !== '')
        <div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-slate-500">
            <span>Filtros activos:</span>
            @if($query !== '')<span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">“{{ $query }}”</span>@endif
            @if($category !== '')<span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">{{ $category }}</span>@endif
            <a href="{{ route('marketplace.stores') }}" class="font-semibold text-slate-700 hover:text-blue-600">Limpiar</a>
        </div>
    @endif

    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($stores as $store)
            <a href="{{ route('marketplace.store',$store) }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-xl font-semibold group-hover:text-blue-600">{{ $store->name }}</h2>
                    <span class="shrink-0 text-sm text-amber-500">★ {{ number_format((float)$store->rating,1) }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-400">{{ $store->review_count }} opiniones</p>
                <p class="mt-4 text-sm leading-6 text-slate-500">{{ $store->description ?: 'Tienda en CombosPlus.' }}</p>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 px-6 py-16 text-center text-slate-500">No encontramos tiendas con esos criterios.</div>
        @endforelse
    </div>
</section>
@endsection