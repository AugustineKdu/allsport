<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            👤 프로필 정보 수정
        </h2>

        <p class="mt-1 text-sm text-gray-600 mb-6">
            닉네임, 지역, 선호 스포츠, 전화번호를 수정할 수 있습니다.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- 닉네임 -->
        <div>
            <label for="nickname" class="block text-lg font-bold text-gray-900 mb-3">
                닉네임 <span class="text-red-500 text-xl">*</span>
            </label>
            <input type="text" name="nickname" id="nickname"
                   class="w-full rounded-lg border-3 border-gray-300 shadow-lg px-5 py-4 text-lg font-semibold focus:border-blue-500 focus:ring focus:ring-blue-200 hover:border-blue-400 transition-colors"
                   value="{{ old('nickname', $user->nickname) }}"
                   required autofocus>
            <x-input-error class="mt-2" :messages="$errors->get('nickname')" />
        </div>

        <!-- 전화번호 -->
        <div>
            <label for="phone" class="block text-lg font-bold text-gray-900 mb-3">
                📞 전화번호 <span class="text-red-500 text-xl">*</span>
            </label>
            <input type="tel" name="phone" id="phone"
                   class="w-full rounded-lg border-3 border-gray-300 shadow-lg px-5 py-4 text-lg font-semibold focus:border-blue-500 focus:ring focus:ring-blue-200 hover:border-blue-400 transition-colors"
                   value="{{ old('phone', $user->phone) }}"
                   placeholder="예: 010-1234-5678"
                   required>
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- 지역 선택 -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-3">📍 활동 지역</h3>
            <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4">
                @foreach($regions as $region)
                    <label class="block cursor-pointer">
                        <input type="radio" name="district" value="{{ $region->district }}" class="sr-only region-radio" {{ old('district', $user->district) == $region->district ? 'checked' : '' }}>
                        <div class="region-option bg-white hover:bg-blue-50 active:bg-blue-100 border-2 border-gray-200 hover:border-blue-300 rounded-lg p-3 text-center transition-all duration-200">
                            <div class="text-2xl mb-2">🏢</div>
                            <div class="text-gray-900 font-bold text-sm mb-1">{{ $region->district }}</div>
                            <div class="text-gray-600 text-xs">{{ $region->city }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <input type="hidden" name="city" value="{{ old('city', $user->city) }}" id="city_hidden">
            <x-input-error class="mt-2" :messages="$errors->get('district')" />
        </div>

        <!-- 선호 스포츠 -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-3">⚽ 선호 스포츠</h3>
            <div class="space-y-3">
                @foreach($sports as $sport)
                    @if($sport->is_active)
                        <button type="button"
                                class="profile-sport-option w-full flex items-center p-4 rounded-lg border-2 border-gray-200 hover:border-green-300 hover:bg-green-50 hover:shadow-lg transition-all text-left group"
                                data-sport="{{ $sport->sport_name }}">
                            <span class="text-3xl mr-4 group-hover:scale-110 transition-transform duration-200">{{ $sport->icon }}</span>
                            <span class="flex flex-col">
                                <span class="text-lg font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ $sport->sport_name }}</span>
                                @if($sport->status)
                                    <span class="text-sm text-green-600 group-hover:text-green-700 transition-colors">{{ $sport->status }}</span>
                                @endif
                            </span>
                        </button>
                    @else
                        <div class="flex items-center p-3 rounded-lg border-2 border-gray-200 bg-gray-100 opacity-60">
                            <span class="text-2xl mr-3 grayscale">{{ $sport->icon }}</span>
                            <span class="flex flex-col">
                                <span class="text-base font-semibold text-gray-500">
                                    {{ $sport->sport_name }}
                                    <span class="ml-2 text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full">베타</span>
                                </span>
                                <span class="text-sm text-orange-600">{{ $sport->status }}</span>
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Hidden radio inputs for profile sports -->
            @foreach($sports as $sport)
                @if($sport->is_active)
                    <input type="radio" name="selected_sport" value="{{ $sport->sport_name }}"
                           class="hidden" {{ old('selected_sport', $user->selected_sport) == $sport->sport_name ? 'checked' : '' }}>
                @endif
            @endforeach
            <x-input-error class="mt-2" :messages="$errors->get('selected_sport')" />
        </div>

        <!-- 저장 버튼 -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                    class="bg-gray-200 text-black px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                저장하기
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600 font-medium">
                    ✅ 프로필이 성공적으로 업데이트되었습니다!
                </p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cityHidden = document.getElementById('city_hidden');
        const currentDistrict = "{{ old('district', $user->district) }}";

        // Region selection
        const regionRadios = document.querySelectorAll('.region-radio');
        regionRadios.forEach(radio => {
            const option = radio.parentElement.querySelector('.region-option');

            radio.addEventListener('change', function() {
                // Remove selected class from all region options
                document.querySelectorAll('.region-option').forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-100');
                    opt.classList.add('border-gray-200', 'bg-white');
                });

                // Add selected class to current option
                if (this.checked) {
                    option.classList.remove('border-gray-200', 'bg-white');
                    option.classList.add('border-blue-500', 'bg-blue-100');

                    // Update hidden city field with the city of selected district
                    const cityText = option.querySelector('.text-gray-600').textContent;
                    cityHidden.value = cityText;
                }
            });

            // Apply initial styling if already selected
            if (radio.checked) {
                option.classList.remove('border-gray-200', 'bg-white');
                option.classList.add('border-blue-500', 'bg-blue-100');

                // Set initial city value
                const cityText = option.querySelector('.text-gray-600').textContent;
                cityHidden.value = cityText;
            }

            option.addEventListener('click', function() {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        });

        // Handle sport selection with button clicks
        const profileSportOptions = document.querySelectorAll('.profile-sport-option');
        const profileSportRadios = document.querySelectorAll('input[name="selected_sport"]');

        function updateProfileSportSelection() {
            // Reset all button styles
            profileSportOptions.forEach(button => {
                button.classList.remove('border-green-500', 'bg-green-100');
                button.classList.add('border-gray-200', 'bg-white');
            });

            // Apply selected style to the checked radio's corresponding button
            profileSportRadios.forEach(radio => {
                if (radio.checked) {
                    const selectedButton = document.querySelector(`.profile-sport-option[data-sport="${radio.value}"]`);
                    if (selectedButton) {
                        selectedButton.classList.remove('border-gray-200', 'bg-white');
                        selectedButton.classList.add('border-green-500', 'bg-green-100');
                    }
                }
            });
        }

        // Add click handlers to sport option buttons
        profileSportOptions.forEach(button => {
            button.addEventListener('click', function() {
                const sportValue = this.getAttribute('data-sport');

                // Uncheck all radios
                profileSportRadios.forEach(radio => {
                    radio.checked = false;
                });

                // Check the corresponding radio
                const correspondingRadio = document.querySelector(`input[name="selected_sport"][value="${sportValue}"]`);
                if (correspondingRadio) {
                    correspondingRadio.checked = true;
                }

                updateProfileSportSelection();

                console.log('Profile sport selected:', sportValue);
            });
        });

        // Set initial state
        updateProfileSportSelection();
    });
</script>
@endpush
