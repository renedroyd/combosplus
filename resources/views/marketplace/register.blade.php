@extends('layouts.marketplace')
@section('title','Registra tu tienda — CombosPlus')
@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
<div class="rounded-3xl border border-blue-100 bg-blue-50 p-8 sm:p-12">
<p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Para negocios</p>
<h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">Registra tu tienda en CombosPlus.</h1>
<p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Estamos preparando un alta simple: cuenta, datos básicos del negocio, identidad visual y catálogo inicial. La complejidad de la multi-tenancy queda detrás de la plataforma.</p>
<div class="mt-8 grid gap-4 sm:grid-cols-3"><div class="rounded-2xl bg-white p-5"><b>1. Cuenta</b><p class="mt-2 text-sm text-slate-500">Crea tu identidad de acceso.</p></div><div class="rounded-2xl bg-white p-5"><b>2. Tienda</b><p class="mt-2 text-sm text-slate-500">Nombre, descripción y presentación.</p></div><div class="rounded-2xl bg-white p-5"><b>3. Catálogo</b><p class="mt-2 text-sm text-slate-500">Publica tus primeros productos.</p></div></div>
<div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('marketplace.home') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700">Volver al Marketplace</a><a href="{{ route('marketplace.stores') }}" class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Explorar tiendas</a></div>
</div></section>
@endsection