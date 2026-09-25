@extends('layouts.marketplace')
@section('title','Tiendas — CombosPlus')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
<p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Marketplace</p><h1 class="mt-2 text-4xl font-semibold">Explora tiendas</h1><p class="mt-3 text-slate-600">Descubre negocios independientes y sus catálogos.</p>
<form method="GET" class="mt-8 flex max-w-xl gap-3"><input name="q" value="{{ $query }}" placeholder="Buscar por nombre..." class="min-h-11 flex-1 rounded-xl border border-slate-300 px-4"><button class="rounded-xl bg-blue-600 px-5 font-semibold text-white">Buscar</button></form>
<div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@forelse($stores as $store)<a href="{{ route('marketplace.store',$store) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-lg"><h2 class="text-xl font-semibold">{{ $store->name }}</h2><div class="mt-2 text-sm text-amber-500">★ {{ number_format((float)$store->rating,1) }} <span class="text-slate-400">({{ $store->review_count }})</span></div><p class="mt-4 text-sm leading-6 text-slate-500">{{ $store->description ?: 'Tienda en CombosPlus.' }}</p></a>@empty<p class="col-span-full py-16 text-center text-slate-500">No encontramos tiendas con esos criterios.</p>@endforelse</div>
</section>
@endsection