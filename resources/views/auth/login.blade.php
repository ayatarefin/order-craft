<x-guest-layout>
    <!-- Title Section -->
    <div class="text-center mb-5">
        <h2 class="text-xl font-bold text-gray-800">Sign In</h2>
        <p class="text-sm text-gray-600 mt-1">Welcome back to OrderCraft</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" class="form-label" />
            <x-text-input id="email" class="form-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>
        
        <!-- Password -->
        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" class="form-label" />
            
            <div class="password-toggle-wrapper">
                <x-text-input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
                
                <button type="button" id="password-toggle" class="password-toggle-btn" tabindex="-1">
                    <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>
            
            <button type="submit" class="login-btn">
                {{ __('Sign In') }}
            </button>
        </div>
    </form>
</x-guest-layout>