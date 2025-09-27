<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if (session('warning'))
        <div class="mb-4 font-medium text-sm text-orange-600">
            {{ session('warning') }}
        </div>
    @endif

    <div class="text-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">로그인</h2>
        <p class="text-sm text-gray-600">계정에 로그인하세요</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">이메일</label>
            <input id="email"
                   class="form-input @error('email') border-red-500 @enderror"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="이메일을 입력하세요">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label">비밀번호</label>
            <input id="password"
                   class="form-input @error('password') border-red-500 @enderror"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="비밀번호를 입력하세요">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mb-6">
            <input id="remember_me"
                   type="checkbox"
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                   name="remember">
            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                로그인 상태 유지
            </label>
        </div>

        <div class="flex flex-col space-y-3">
            <button type="submit" class="btn-primary w-full">
                로그인
            </button>

            <div class="text-center">
                <a href="{{ route('register') }}"
                   class="text-sm text-blue-600 hover:text-blue-500">
                    계정이 없으신가요? 회원가입
                </a>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-gray-600 hover:text-gray-500">
                        비밀번호를 잊으셨나요?
                    </a>
                </div>
            @endif
        </div>
    </form>
</x-guest-layout>
