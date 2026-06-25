@extends('common.index')
@section('title', 'Add Address')
@section('content')

    <div class="max-w-2xl mx-auto px-4 py-10">

        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card bg-base-100 shadow-lg border border-base-200">
            <div class="card-body">
                <h1 class="card-title text-2xl mb-1">Add New Address</h1>
                <p class="text-base-content/60 text-sm mb-6">Enter a new shipping address.</p>

                <form method="POST" action="{{ route('address.add') }}">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Street --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label" for="street">
                                <span class="label-text font-medium">Street <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="street" name="street" value="{{ old('street') }}"
                                class="input input-bordered w-full" required />
                        </div>

                        <div class="form-control">
                            <label class="label" for="city">
                                <span class="label-text font-medium">City <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
                                class="input input-bordered w-full" required />
                        </div>

                        <div class="form-control">
                            <label class="label" for="province">
                                <span class="label-text font-medium">Province <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="province" name="province" value="{{ old('province') }}"
                                class="input input-bordered w-full" required />
                        </div>

                        <div class="form-control">
                            <label class="label" for="barangay">
                                <span class="label-text font-medium">Barangay <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="barangay" name="barangay" value="{{ old('barangay') }}"
                                class="input input-bordered w-full" required />
                        </div>

                        <div class="form-control">
                            <label class="label" for="region">
                                <span class="label-text font-medium">Region <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="region" name="region" value="{{ old('region') }}"
                                class="input input-bordered w-full" required />
                        </div>

                        <div class="form-control">
                            <label class="label" for="postal_code">
                                <span class="label-text font-medium">Postal Code</span>
                            </label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                class="input input-bordered w-full" placeholder="Optional" />
                        </div>

                        <div class="form-control">
                            <label class="label" for="unit_floor">
                                <span class="label-text font-medium">Unit / Floor</span>
                            </label>
                            <input type="text" id="unit_floor" name="unit_floor" value="{{ old('unit_floor') }}"
                                class="input input-bordered w-full" placeholder="Optional" />
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label" for="additional_notes">
                                <span class="label-text font-medium">Additional Notes</span>
                            </label>
                            <textarea id="additional_notes" name="additional_notes" class="textarea textarea-bordered w-full" rows="3"
                                placeholder="Landmarks, delivery instructions, etc.">{{ old('additional_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="card-actions justify-end mt-8">
                        <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
