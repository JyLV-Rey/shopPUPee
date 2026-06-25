@if($errors->any())
    <div class="alert alert-error shadow-sm mb-6 flex-col items-start gap-1">
        @foreach($errors->all() as $error)
            <span class="text-sm">• {{ $error }}</span>
        @endforeach
    </div>
@endif

<div class="text-center mb-8">
    <h2 class="card-title text-3xl font-bold justify-center text-primary mb-2">Become a Seller</h2>
    <p class="text-base-content/70">Fill out this application to start selling your products on ShopPUPee.</p>
</div>

<form action="{{ route('account.create.seller') }}" method="POST" class="space-y-8">
    @csrf
    
    <div>
        <h3 class="text-xl font-semibold mb-4 border-b pb-2">Store Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Store Name</span>
                </label>
                <input type="text" name="seller_name" value="{{ old('seller_name') }}" placeholder="Your Shop Name" class="input input-bordered w-full" required />
            </div>
            
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Valid ID Image URL</span>
                </label>
                <input type="url" name="valid_id_url" value="{{ old('valid_id_url') }}" placeholder="https://example.com/id-image.jpg" class="input input-bordered w-full" required />
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Provide a link to a clear photo of your valid ID</span>
                </label>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xl font-semibold mb-4 border-b pb-2">Store Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Street</span>
                </label>
                <input type="text" name="street" value="{{ old('street') }}" placeholder="e.g. 123 Main St" class="input input-bordered w-full" required />
            </div>
            
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Barangay</span>
                </label>
                <input type="text" name="barangay" value="{{ old('barangay') }}" placeholder="Barangay" class="input input-bordered w-full" required />
            </div>
            
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">City/Municipality</span>
                </label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="City" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Province</span>
                </label>
                <input type="text" name="province" value="{{ old('province') }}" placeholder="Province" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Region</span>
                </label>
                <input type="text" name="region" value="{{ old('region') }}" placeholder="Region" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">Postal Code</span>
                </label>
                <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="e.g. 1016" class="input input-bordered w-full" required />
            </div>
            
            <div class="form-control w-full md:col-span-2">
                <label class="label">
                    <span class="label-text font-medium">Unit/Floor (Optional)</span>
                </label>
                <input type="text" name="unit_floor" value="{{ old('unit_floor') }}" placeholder="e.g. Unit 3A" class="input input-bordered w-full" />
            </div>

            <div class="form-control w-full md:col-span-2">
                <label class="label">
                    <span class="label-text font-medium">Additional Notes (Optional)</span>
                </label>
                <textarea name="additional_notes" class="textarea textarea-bordered w-full" placeholder="Landmarks or delivery instructions">{{ old('additional_notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-control mt-8">
        <button type="submit" class="btn btn-primary w-full btn-lg">Submit Application</button>
        <p class="text-center text-sm mt-3 text-base-content/70">By submitting, you agree to our terms and conditions for sellers.</p>
    </div>
</form>
