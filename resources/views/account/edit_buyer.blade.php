@extends('common.index')
@section('title', 'Customer - Edit Profile')
@section('content')

<div class="max-w-2xl mx-auto px-4 py-10">

  @if (session('success'))
    <div class="alert alert-success mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-error mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
      <h1 class="card-title text-2xl mb-1">Edit Profile</h1>
      <p class="text-base-content/60 text-sm mb-6">Update your personal information below.</p>

      <form method="POST" action="{{ route('edit.buyer') }}">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="form-control">
            <label class="label" for="first_name">
              <span class="label-text font-medium">First Name</span>
            </label>
            <input
              type="text"
              id="first_name"
              name="first_name"
              value="{{ old('first_name', Auth::user()->first_name) }}"
              class="input input-bordered w-full"
              required
            />
          </div>

          <div class="form-control">
            <label class="label" for="last_name">
              <span class="label-text font-medium">Last Name</span>
            </label>
            <input
              type="text"
              id="last_name"
              name="last_name"
              value="{{ old('last_name', Auth::user()->last_name) }}"
              class="input input-bordered w-full"
              required
            />
          </div>
        </div>

        <div class="form-control mt-4">
          <label class="label" for="email">
            <span class="label-text font-medium">Email</span>
          </label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', Auth::user()->email) }}"
            class="input input-bordered w-full"
            required
          />
          <label class="label">
            <span class="label-text-alt text-warning">Changing your email may affect your login credentials.</span>
          </label>
        </div>

        <div class="form-control mt-4">
          <label class="label" for="phone">
            <span class="label-text font-medium">Phone</span>
          </label>
          <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', Auth::user()->phone) }}"
            class="input input-bordered w-full"
            placeholder="e.g. 09171234567"
          />
        </div>

        <div class="card-actions justify-end mt-8">
          <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
