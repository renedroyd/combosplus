@extends('layouts.marketplace')
@section('title',$tenant->name.' — Tienda en Nexo')
@section('content')
<section class="relative isolate overflow-hidden bg-gradient-to-br from-violet-950 via-violet-800 to-fuchsia-700 text-white">
    <div aria-hidden="true" class="absolute -right-10 -top-20 h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
    <div aria-hidden="true" class="absolute -bottom-24 left-1/4 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
        <a href="{{ route('marketplace.stores') }}" class="inline-flex rounded-full border border-white/40 bg-slate-950/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-amber-300">← Todas las tiendas</a>
        <div class="mt-8 flex flex-col gap-6 sm:flex-row sm:items-center">
            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-3xl bg-gradient-to-br from-amber-300 to-orange-400 text-4xl font-black text-slate-950 shadow-xl">{{ mb_strtoupper(mb_substr($tenant->name,0,1)) }}</div>
            <div><p class="text-sm font-black uppercase tracking-[.2em] text-amber-300">Bienvenido a la tienda</p><h1 class="mt-2 break-words text-4xl font-black tracking-tight text-white sm:text-5xl">{{ $tenant->name }}</h1><div class="mt-3 text-sm font-semibold text-white"><span class="text-amber-300">★ {{ number_format((float)$tenant->rating,1) }}</span> · {{ $tenant->review_count }} opiniones</div></div>
        </div>
        <p class="mt-5 max-w-2xl text-base font-medium leading-7 text-white sm:text-lg">{{ $tenant->description ?: 'Descubre el catálogo de esta tienda.' }}</p>
        <a href="#catalogo-tienda" class="mt-6 inline-flex min-h-12 items-center rounded-xl bg-amber-300 px-5 py-3 font-black text-slate-950 shadow-lg transition hover:-translate-y-0.5 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-white">Explorar catálogo <span class="ml-2" aria-hidden="true">↓</span></a>
    </div>
</section>
<section id="catalogo-tienda" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[.2em] text-fuchsia-800">Selección de la tienda</p><h2 class="mt-2 text-3xl font-black text-slate-950">Catálogo de productos</h2><p class="mt-2 text-sm font-medium text-slate-700">Explora los productos publicados por este comercio.</p></div><a href="{{ route('marketplace.stores') }}" class="text-sm font-black text-violet-800 underline underline-offset-4 hover:text-fuchsia-800">Descubrir otras tiendas →</a></div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            <a href="{{ route('marketplace.product',[$tenant,$product->getKey()]) }}" class="group overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-fuchsia-200 hover:shadow-xl hover:shadow-violet-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <div class="aspect-[4/3] overflow-hidden bg-gradient-to-br from-violet-50 via-white to-orange-50 p-3">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105" loading="lazy">@else<div class="grid h-full place-items-center text-5xl" aria-hidden="true">🛍️</div>@endif</div>
                <div class="p-5"><h3 class="font-extrabold text-slate-950 group-hover:text-violet-800">{{ $product->name }}</h3><div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-sm"><b class="text-lg font-black text-slate-950">{{ '$'.number_format((float)$product->price,2) }}</b><span class="rounded-full bg-amber-100 px-2.5 py-1 font-bold text-amber-950">★ {{ number_format((float)($product->rating ?? 0),1) }}</span></div><span class="mt-4 inline-flex text-sm font-black text-fuchsia-800">Ver producto ↗</span></div>
            </a>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-violet-300 bg-violet-50 px-6 py-16 text-center"><p class="text-4xl" aria-hidden="true">📦</p><h3 class="mt-4 text-xl font-black text-slate-950">Esta tienda todavía no tiene productos publicados.</h3><p class="mt-2 font-medium text-slate-700">Vuelve pronto para descubrir sus novedades.</p><a href="{{ route('marketplace.stores') }}" class="mt-5 inline-flex rounded-xl bg-violet-700 px-5 py-3 font-black text-white hover:bg-violet-800">Explorar otras tiendas</a></div>
        @endforelse
    </div>
    <div class="mt-16 max-w-3xl"><p class="text-xs font-black uppercase tracking-[.2em] text-violet-800">Experiencias reales</p><h2 class="mt-2 text-3xl font-black text-slate-950">Opiniones de clientes</h2><div class="mt-6 space-y-4">
        @forelse($reviews as $review)
            <article class="rounded-2xl border border-violet-100 bg-white p-5 shadow-sm"><div class="flex flex-wrap items-center justify-between gap-4"><span class="font-bold text-amber-600">★★★★★ <span class="text-sm text-slate-700">{{ $review->rating }}/5</span></span>@if($review->verified_purchase)<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-900">Compra verificada</span>@endif</div><p class="mt-3 leading-7 text-slate-800">{{ $review->comment }}</p><p class="mt-3 text-sm font-medium text-slate-600">{{ $review->user?->name ?: 'Cliente' }} · {{ $review->created_at?->diffForHumans() }}</p></article>
        @empty
            <p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 font-medium text-slate-700">Todavía no hay opiniones publicadas.</p>
        @endforelse
    </div></div>
</section>
@endsection