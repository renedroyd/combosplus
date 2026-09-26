@extends('layouts.marketplace')
@section('title','Productos — CombosPlus')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Marketplace</p>
    <h1 class="mt-2 text-4xl font-semibold">Explora productos</h1>
    <p class="mt-3 text-slate-600">Encuentra productos de tiendas publicadas y compara por precio, valoración o novedad.</p>
    <form method="GET" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="sr-only" for="product-search">Buscar productos</label>
        <input id="product-search" name="q" value="{{ $query }}" placeholder="Buscar producto..." class="min-h-11 rounded-xl border border-slate-300 bg-white px-4">
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
        <button class="min-h-11 rounded-xl bg-blue-600 px-5 font-semibold text-white hover:bg-blue-500">Buscar</button>
    </form>
    @if($query !== '' || $category !== '')<div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-slate-500"><span>Filtros activos:</span>@if($query !== '')<span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">“{{ $query }}”</span>@endif @if($category !== '')<span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">{{ $category }}</span>@endif<a href="{{ route('marketplace.products') }}" class="font-semibold text-slate-700 hover:text-blue-600">Limpiar</a></div>@endif
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            <a href="{{ route('marketplace.product',[$product->marketplace_tenant_id,$product->getKey()]) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="aspect-[4/3] bg-slate-100">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">@else<div class="grid h-full place-items-center text-sm text-slate-400">Sin imagen</div>@endif</div>
                <div class="p-5"><p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $product->category?->name ?: 'Producto' }}</p><h2 class="mt-1 line-clamp-2 font-semibold group-hover:text-blue-600">{{ $product->name }}</h2><p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $product->description }}</p><div class="mt-4 flex items-center justify-between gap-3"><b>{{ '$'.number_format((float)$product->price,2) }}</b><span class="text-sm text-amber-500">★ {{ number_format((float)($product->rating ?? 0),1) }}</span></div></div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 px-6 py-16 text-center text-slate-500">No encontramos productos con esos criterios.</div>
        @endforelse
    </div>
</section>
@endsection
