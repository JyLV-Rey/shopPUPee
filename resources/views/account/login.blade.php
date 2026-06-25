@extends('common.index')

@section('title', 'Login - ShopPUPee')

@section('content')
    <div class="flex items-center justify-center min-h-[calc(100vh-100px)] px-4 py-12">
        <div class="card w-full max-w-md bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl font-bold justify-center mb-6 text-primary">Login to ShopPUPee</h2>

                @if ($redirect)
                    <div class="alert alert-warning shadow-sm mb-4">
                        <span>Please log in to continue</span>
                    </div>
                @endif

                @if ($accountCreated)
                    <div class="alert alert-success shadow-sm mb-4">
                        <span>Account created successfully!</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error shadow-sm mb-4 text-white">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success shadow-sm mb-4">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('account.login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Email Address</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email"
                            class="input input-bordered w-full" required />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="Enter your password"
                            class="input input-bordered w-full" required />
                    </div>

                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary w-full">Login</button>
                    </div>
                </form>

                <div class="divider text-sm text-base-content/60 mt-6">OR</div>

                <div class="text-center">
                    <p class="text-sm">No account yet? <a href="{{ route('account.create') }}"
                            class="link link-primary font-semibold">Sign up here</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
