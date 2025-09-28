<x-guest-layout>
    <!-- 자동 배포 테스트 배너 -->
    <div class="bg-green-500 text-white p-3 rounded-lg mb-4 text-center animate-pulse">
        🚀 자동 배포 테스트 중! ({{ date('Y-m-d H:i:s') }})
    </div>
    
    <div class="text-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">회원가입</h2>
        <p class="text-sm text-gray-600">새 계정을 만드세요</p>
    </div>

    <form method="POST" action="{{ route('register') }}" autocomplete="on" novalidate>
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="form-label">이름</label>
            <input id="name"
                   class="form-input @error('name') border-red-500 @enderror"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="이름을 입력하세요">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">이메일</label>
            <input id="email"
                   class="form-input @error('email') border-red-500 @enderror"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
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
                   autocomplete="new-password"
                   placeholder="비밀번호를 입력하세요">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="form-label">비밀번호 확인</label>
            <input id="password_confirmation"
                   class="form-input @error('password_confirmation') border-red-500 @enderror"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="비밀번호를 다시 입력하세요">
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col space-y-3">
            <button type="submit" class="btn-primary w-full">
                회원가입
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}"
                   class="text-sm text-blue-600 hover:text-blue-500">
                    이미 계정이 있으신가요? 로그인
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
