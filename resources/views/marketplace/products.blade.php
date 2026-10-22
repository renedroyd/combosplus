@extends('layouts.marketplace')
@section('title','Explora productos — Nexo')
@section('content')
<section class="relative isolate overflow-hidden bg-gradient-to-br from-violet-950 via-violet-800 to-fuchsia-700 text-white"><div aria-hidden="true" class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-orange-400/30 blur-3xl"></div><div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><p class="text-xs font-black uppercase tracking-[.22em] text-amber-300">Explora el universo Nexo</p><h1 class="mt-3 text-4xl font-black text-white sm:text-5xl">Encuentra algo que te encante.</h1><p class="mt-4 max-w-2xl font-medium leading-7 text-white sm:text-lg">Descubre productos de tiendas publicadas y compara por precio, valoración o novedad.</p></div></section><section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <p class="text-sm font-black uppercase tracking-wider text-violet-800">Catálogo de productos</p>
    <form method="GET" class="mt-8 grid gap-3 rounded-2xl border border-violet-100 bg-violet-50 p-4 shadow-sm sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="sr-only" for="product-search">Buscar productos</label>
        <input id="product-search" name="q" value="{{ $query }}" placeholder="Buscar producto..." class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 text-slate-950 placeholder:text-slate-500 focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200">
        <label class="sr-only" for="product-category">Categoría</label>
        <select id="product-category" name="category" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4"><option value="">Todas las categorías</option>@foreach($categories as $item)<option value="{{ $item->name }}" @selected(mb_strtolower($category) === mb_strtolower($item->name))>{{ $item->name }} ({{ $item->product_count }})</option>@endforeach</select>
        <label class="sr-only" for="product-sort">Ordenar</label>
        <select id="product-sort" name="sort" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4">
            <option value="relevance" @selected($sort === 'relevance')>Relevancia</option>
            <option value="rating" @selected($sort === 'rating')>Más valorados</option>
            <option value="newest" @selected($sort === 'newest')>Más recientes</option>
            <option value="price_low" @selected($sort === 'price_low')>Precio: menor primero</option>
            <option value="price_high" @selected($sort === 'price_high')>Precio: mayor primero</option>
        </select>
        <button class="min-h-11 rounded-xl bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 font-extrabold text-white shadow-md transition hover:from-violet-800 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:ring-offset-2">Buscar</button>
    </form>
    @if($query !== '' || $category !== '')<div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-slate-500"><span>Filtros activos:</span>@if($query !== '')<span class="rounded-full bg-violet-100 px-3 py-1 font-semibold text-violet-950">“{{ $query }}”</span>@endif @if($category !== '')<span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">{{ $category }}</span>@endif<a href="{{ route('marketplace.products') }}" class="font-semibold text-slate-700 hover:text-blue-600">Limpiar</a></div>@endif
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            <a href="{{ route('marketplace.product',[$product->marketplace_tenant_slug,$product->getKey()]) }}" class="group overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-fuchsia-200 hover:shadow-xl hover:shadow-violet-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <div class="aspect-[4/3] overflow-hidden bg-gradient-to-br from-violet-50 via-white to-orange-50 p-3">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">@else<div class="grid h-full place-items-center text-sm font-semibold text-slate-600">Sin imagen</div>@endif</div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-wide text-violet-800">{{ $product->category?->name ?: 'Producto' }}</p><h2 class="mt-1 line-clamp-2 font-extrabold text-slate-950 group-hover:text-violet-800">{{ $product->name }}</h2><p class="mt-2 line-clamp-2 text-sm font-medium text-slate-700">{{ $product->description }}</p><div class="mt-4 flex items-center justify-between gap-3"><b>{{ '$'.number_format((float)$product->price,2) }}</b><span class="text-sm text-amber-500">★ {{ number_format((float)($product->rating ?? 0),1) }}</span></div></div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 px-6 py-16 text-center text-slate-500">No encontramos productos con esos criterios.</div>
        @endforelse
    </div>
</section>
@endsection
