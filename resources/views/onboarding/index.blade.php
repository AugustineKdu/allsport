<x-app-layout>
    <!-- Mobile Layout -->
    <div class="sm:hidden min-h-screen bg-gradient-to-br from-green-400 via-green-500 to-green-600">
        <div class="px-4 py-8">
            <!-- Header -->
            <div class="text-center text-white mb-8">
                <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">⚽</span>
                </div>
                <h1 class="text-3xl font-bold mb-2">환영합니다!</h1>
                <p class="text-green-100 text-lg">프로필을 설정하고 스포츠를 시작해보세요</p>
            </div>

            <form action="{{ route('onboarding.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Step 1: 닉네임 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-600 font-bold text-sm">1</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">닉네임 설정</h2>
                    </div>

                    <label for="nickname" class="block text-white/90 text-sm font-medium mb-3">
                        사용하실 닉네임을 입력하세요
                    </label>
                    <input id="nickname"
                           name="nickname"
                           type="text"
                           required
                           class="w-full px-4 py-4 bg-white rounded-2xl border-0 text-gray-800 placeholder-gray-400 text-lg font-medium shadow-sm focus:ring-2 focus:ring-white/50 focus:outline-none"
                           placeholder="예: 축구왕김철수"
                           value="{{ old('nickname') }}">
                    @error('nickname')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2: 지역 선택 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-600 font-bold text-sm">2</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">활동 지역</h2>
                    </div>

                    <label class="block text-white/90 text-sm font-medium mb-4">
                        주로 활동하실 지역을 선택하세요
                    </label>

                    <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                        @foreach($regions as $region)
                            <label class="block cursor-pointer">
                                <input type="radio" name="district" value="{{ $region->district }}" class="sr-only district-radio" {{ old('district') == $region->district ? 'checked' : '' }}>
                                <div class="district-option bg-white/20 hover:bg-white/30 active:bg-white/40 backdrop-blur-sm rounded-xl p-3 border-2 border-white/30 text-center transition-all duration-200">
                                    <div class="text-2xl mb-2">🏢</div>
                                    <div class="text-white font-bold text-sm mb-1">{{ $region->district }}</div>
                                    <div class="text-white/80 text-xs">{{ $region->city }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('district')
                        <p class="mt-3 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2.5: 전화번호 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-600 font-bold text-sm">3</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">연락처</h2>
                    </div>

                    <label for="phone" class="block text-white/90 text-sm font-medium mb-3">
                        연락 가능한 전화번호를 입력하세요 (매칭 시 사용)
                    </label>
                    <input id="phone"
                           name="phone"
                           type="tel"
                           required
                           class="w-full px-4 py-4 bg-white rounded-2xl border-0 text-gray-800 placeholder-gray-400 text-lg font-medium shadow-sm focus:ring-2 focus:ring-white/50 focus:outline-none"
                           placeholder="예: 010-1234-5678"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 4: 스포츠 선택 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-600 font-bold text-sm">4</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">선호 스포츠</h2>
                    </div>

                    <label class="block text-white/90 text-sm font-medium mb-4">
                        어떤 스포츠를 즐기시나요?
                    </label>

                    <div class="space-y-3">
                        @foreach($sports as $sport)
                            <label class="block cursor-pointer">
                                <input type="radio" name="selected_sport" value="{{ $sport->sport_name }}" class="sr-only sport-radio" {{ old('selected_sport') == $sport->sport_name ? 'checked' : '' }}>
                                <div class="sport-option bg-white/20 hover:bg-white/30 active:bg-white/40 backdrop-blur-sm rounded-2xl p-4 border-2 border-white/30 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="text-3xl mr-4">{{ $sport->icon }}</div>
                                        <div class="flex-1">
                                            <div class="text-white font-bold text-lg">{{ $sport->sport_name }}</div>
                                            <div class="text-white/80 text-sm">{{ $sport->status }}</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selected_sport')
                        <p class="mt-3 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="space-y-4 pt-4">
                    <button type="submit" class="w-full py-4 bg-white text-green-600 font-bold text-lg rounded-2xl hover:bg-gray-50 transition-all shadow-lg">
                        시작하기 🚀
                    </button>
                    <a href="{{ route('home') }}" class="block w-full py-3 text-center text-white/90 hover:text-white text-sm font-medium transition-colors">
                        나중에 설정하기
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop & Tablet Layout -->
    <div class="hidden sm:block min-h-screen bg-gradient-to-br from-green-400 via-green-500 to-green-600 flex items-center justify-center py-12">
        <div class="max-w-md w-full mx-4">
            <!-- Header -->
            <div class="text-center text-white mb-8">
                <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl">⚽</span>
                </div>
                <h1 class="text-4xl font-bold mb-3">환영합니다!</h1>
                <p class="text-green-100 text-xl">프로필을 설정하고 스포츠를 시작해보세요</p>
            </div>

            <form action="{{ route('onboarding.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Step 1: 닉네임 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">1</span>
                        </div>
                        <h2 class="text-2xl font-bold text-white">닉네임 설정</h2>
                    </div>

                    <label for="nickname_desktop" class="block text-white/90 font-medium mb-4">
                        사용하실 닉네임을 입력하세요
                    </label>
                    <input id="nickname_desktop"
                           name="nickname"
                           type="text"
                           required
                           class="w-full px-5 py-4 bg-white rounded-2xl border-0 text-gray-800 placeholder-gray-400 text-lg font-medium shadow-sm focus:ring-2 focus:ring-white/50 focus:outline-none"
                           placeholder="예: 축구왕김철수"
                           value="{{ old('nickname') }}">
                    @error('nickname')
                        <p class="mt-3 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2: 지역 선택 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">2</span>
                        </div>
                        <h2 class="text-2xl font-bold text-white">활동 지역</h2>
                    </div>

                    <label class="block text-white/90 font-medium mb-6">
                        주로 활동하실 지역을 선택하세요
                    </label>

                    <div class="grid grid-cols-3 gap-4 max-h-80 overflow-y-auto">
                        @foreach($regions as $region)
                            <label class="block cursor-pointer">
                                <input type="radio" name="district" value="{{ $region->district }}" class="sr-only desktop-district-radio" {{ old('district') == $region->district ? 'checked' : '' }}>
                                <div class="bg-white/20 hover:bg-white/30 active:bg-white/40 backdrop-blur-sm rounded-xl p-4 border-2 border-white/30 text-center transition-all duration-200">
                                    <div class="text-3xl mb-3">🏢</div>
                                    <div class="text-white font-bold text-sm mb-1">{{ $region->district }}</div>
                                    <div class="text-white/80 text-xs">{{ $region->city }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('district')
                        <p class="mt-4 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2.5: 전화번호 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">3</span>
                        </div>
                        <h2 class="text-2xl font-bold text-white">연락처</h2>
                    </div>

                    <label for="phone_desktop" class="block text-white/90 font-medium mb-4">
                        연락 가능한 전화번호를 입력하세요 (매칭 시 사용)
                    </label>
                    <input id="phone_desktop"
                           name="phone"
                           type="tel"
                           required
                           class="w-full px-5 py-4 bg-white rounded-2xl border-0 text-gray-800 placeholder-gray-400 text-lg font-medium shadow-sm focus:ring-2 focus:ring-white/50 focus:outline-none"
                           placeholder="예: 010-1234-5678"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-3 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 4: 스포츠 선택 -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">4</span>
                        </div>
                        <h2 class="text-2xl font-bold text-white">선호 스포츠</h2>
                    </div>

                    <label class="block text-white/90 font-medium mb-6">
                        어떤 스포츠를 즐기시나요?
                    </label>

                    <div class="space-y-4">
                        @foreach($sports as $sport)
                            <label class="block cursor-pointer">
                                <input type="radio" name="selected_sport" value="{{ $sport->sport_name }}" class="sr-only desktop-sport-radio" {{ old('selected_sport') == $sport->sport_name ? 'checked' : '' }}>
                                <div class="bg-white/20 hover:bg-white/30 active:bg-white/40 backdrop-blur-sm rounded-2xl p-6 border-2 border-white/30 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="text-4xl mr-6">{{ $sport->icon }}</div>
                                        <div class="flex-1">
                                            <div class="text-white font-bold text-xl">{{ $sport->sport_name }}</div>
                                            <div class="text-white/80 text-sm mt-1">{{ $sport->status }}</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selected_sport')
                        <p class="mt-4 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="space-y-4">
                    <button type="submit" class="w-full py-4 bg-white text-green-600 font-bold text-lg rounded-2xl hover:bg-gray-50 transition-all shadow-lg">
                        시작하기 🚀
                    </button>
                    <a href="{{ route('home') }}" class="block w-full py-3 text-center text-white/90 hover:text-white font-medium transition-colors">
                        나중에 설정하기
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile district selection
            const districtRadios = document.querySelectorAll('.district-radio');
            districtRadios.forEach(radio => {
                const option = radio.parentElement.querySelector('.district-option');

                radio.addEventListener('change', function() {
                    // Remove selected class from all district options
                    document.querySelectorAll('.district-option').forEach(opt => {
                        opt.classList.remove('border-white', 'bg-white/30');
                        opt.classList.add('border-white/30');
                    });

                    // Add selected class to current option
                    if (this.checked) {
                        option.classList.remove('border-white/30');
                        option.classList.add('border-white', 'bg-white/30');
                    }
                });

                // Apply initial styling if already selected
                if (radio.checked) {
                    option.classList.remove('border-white/30');
                    option.classList.add('border-white', 'bg-white/30');
                }

                option.addEventListener('click', function() {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                });
            });

            // Mobile sport selection
            const sportRadios = document.querySelectorAll('.sport-radio');
            sportRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all sport options
                    document.querySelectorAll('.sport-option').forEach(option => {
                        option.classList.remove('border-white', 'bg-white/30');
                        option.classList.add('border-white/30');
                    });

                    // Add selected class to current option
                    if (this.checked) {
                        const option = this.parentElement.querySelector('.sport-option');
                        option.classList.remove('border-white/30');
                        option.classList.add('border-white', 'bg-white/30');
                    }
                });

                // Apply initial styling if already selected
                if (radio.checked) {
                    const option = radio.parentElement.querySelector('.sport-option');
                    option.classList.remove('border-white/30');
                    option.classList.add('border-white', 'bg-white/30');
                }
            });

            // Desktop district selection
            const desktopDistrictRadios = document.querySelectorAll('.desktop-district-radio');
            desktopDistrictRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all desktop district options
                    desktopDistrictRadios.forEach(r => {
                        const label = r.closest('label');
                        label.querySelector('div').classList.remove('border-white', 'bg-white/30');
                        label.querySelector('div').classList.add('border-white/30');
                    });

                    // Add selected class to current option
                    if (this.checked) {
                        const label = this.closest('label');
                        label.querySelector('div').classList.remove('border-white/30');
                        label.querySelector('div').classList.add('border-white', 'bg-white/30');
                    }
                });

                // Apply initial styling if already selected
                if (radio.checked) {
                    const label = radio.closest('label');
                    label.querySelector('div').classList.remove('border-white/30');
                    label.querySelector('div').classList.add('border-white', 'bg-white/30');
                }

                // Handle click on option
                const label = radio.closest('label');
                label.addEventListener('click', function() {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                });
            });

            // Desktop sport selection
            const desktopSportRadios = document.querySelectorAll('.desktop-sport-radio');
            desktopSportRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all labels
                    desktopSportRadios.forEach(r => {
                        const label = r.closest('label');
                        label.querySelector('div').classList.remove('border-white', 'bg-white/30');
                        label.querySelector('div').classList.add('border-white/30');
                    });

                    // Add selected class to current label
                    if (this.checked) {
                        const label = this.closest('label');
                        label.querySelector('div').classList.remove('border-white/30');
                        label.querySelector('div').classList.add('border-white', 'bg-white/30');
                    }
                });

                // Apply initial styling if already selected
                if (radio.checked) {
                    const label = radio.closest('label');
                    label.querySelector('div').classList.remove('border-white/30');
                    label.querySelector('div').classList.add('border-white', 'bg-white/30');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
