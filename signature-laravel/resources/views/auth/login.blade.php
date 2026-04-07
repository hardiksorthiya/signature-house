<x-guest-layout>
    @if (session('status'))
        <div class="mb-3 rounded-lg px-4 py-2.5 text-sm text-green-800 bg-green-50 border border-green-200" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <h1 class="text-xl font-bold tracking-tight" style="color: #1f2937;">Welcome back</h1>
        <p class="mt-1 text-sm" style="color: #6b7280;">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        @if ($errors->any())
            <div class="guest-alert guest-alert-error" role="alert">
                <span class="guest-alert-icon" aria-hidden="true">&times;</span>
                <div>
                    <p class="guest-alert-title">Incorrect details</p>
                    <p class="guest-alert-message">{{ $errors->first('phone') ?: $errors->first() }}</p>
                </div>
            </div>
        @endif

        <div class="mb-3">
            <label for="phone" class="block text-sm font-semibold mb-1" style="color: #374151;">Phone Number / Email</label>
            <input id="phone"
                   type="text"
                   name="phone"
                   value="{{ old('phone') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="Enter phone number or email"
                   class="guest-input @error('phone') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="block text-sm font-semibold mb-1" style="color: #374151;">Password</label>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="Enter your password"
                   class="guest-input @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mb-3">
            <label for="remember_me" class="inline-flex items-center cursor-pointer gap-2">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                <span class="text-sm" style="color: #4b5563;">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-semibold transition hover:underline"
                   style="color: var(--login-primary);">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="mt-3">
            <button type="submit" class="guest-btn-login">
                Log in
            </button>
        </div>
    </form>
</x-guest-layout>
