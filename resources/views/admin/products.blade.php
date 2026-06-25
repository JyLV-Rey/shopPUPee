@extends('common.index')

@section('title', 'Admin — Products')

@section('content')
<div class="flex flex-col lg:flex-row min-h-[calc(100vh-4rem)]">
    <x-admin_sidebar />

    <div class="flex-1 p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold tracking-tight">Manage Products</h1>
            <span class="badge badge-lg">{{ $products->total() }} total</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-base-200 shadow-sm">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-base-content/50">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Seller</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-xs text-base-content/50">#{{ $product->product_id }}</td>
                        <td class="font-medium max-w-xs truncate">{{ $product->name }}</td>
                        <td><span class="badge badge-outline badge-sm">{{ $product->category }}</span></td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-sm">{{ $product->seller?->seller_name ?? '—' }}</td>
                        <td class="text-center">{{ $product->quantity ?? '—' }}</td>
                        <td class="text-center">
                            @if($product->is_deleted)
                                <span class="badge badge-error badge-sm">Deleted</span>
                            @else
                                <span class="badge badge-success badge-sm">Active</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.product.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $product->is_deleted ? 'btn-success' : 'btn-error' }}">
                                    {{ $product->is_deleted ? 'Restore' : 'Disable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-base-content/50">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </div>
</div>
@endsection
