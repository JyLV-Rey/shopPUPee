@extends('common.index')

@section('title', 'Create Product')

@section('content')
    <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-3xl font-bold mb-6">Create a New Product</h1>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('product.create') }}" method="POST">
                    @csrf
                    <!-- 1. PRODUCT NAME INPUT -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Product Name</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="e.g. Mechanical Keyboard" class="input input-bordered w-full" required />
                    </div>

                    <!-- 2. CATEGORY DROPDOWN -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Category</span>
                        </label>
                        <select name="category" class="select select-bordered w-full" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach ($categories as $category)
                                <!-- Keep the dropdown selected if validation fails -->
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea name="description" rows="5" placeholder="Describe your product..."
                            class="textarea textarea-bordered w-full" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- 4. PRICE INPUT -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Price (₱)</span>
                        </label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            placeholder="0.00" class="input input-bordered w-full" required />
                    </div>

                    <!-- 5. QUANTITY INPUT -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Stock Quantity</span>
                        </label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}"
                            placeholder="e.g. 50" class="input input-bordered w-full" required />
                    </div>

                    <!-- 6. IMAGE URL INPUT -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Image URL</span>
                        </label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}"
                            placeholder="https://example.com/image.jpg" class="input input-bordered w-full" />
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Publish Product</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
