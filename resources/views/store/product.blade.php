@extends('layouts.app')

@section('content')
<main class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
    @if($product->description)
        <p class="mt-4 text-gray-600">{{ $product->description }}</p>
    @endif
    <p class="mt-6 text-2xl font-semibold">${{ number_format($product->offer_price ?? $product->price, 2) }}</p>
    <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6">
        @csrf
        <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white">Añadir al carrito</button>
    </form>
</main>
@endsection
