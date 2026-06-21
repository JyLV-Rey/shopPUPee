@extends('common.index')

@section('title', 'View Product')

@section('content')
    <div class="container mx-auto p-4">
        <div class="card lg:card-side bg-base-100 shadow-xl">
            <figure>
                <img src= "{{ $product->images->first()->image_url }}" alt="Product Image" class="max-w-md" />
            </figure>

            <div class="card-body">
                <h2 class="card-title text-3xl"> {{ $product->name }} </h2>

                <p class="text-sm text-gray-500">Sold by: {{ $product->seller->seller_name }} </p>

                <p class="text-2xl text-primary font-bold">₱ {{ $product->price }}</p>

                <div class="divider"></div>

                <p> {{ $product->description }}</p>

                <div class="card-actions justify-end mt-4">
                    <button class="btn btn-primary">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
@endsection
