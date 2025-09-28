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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="city" class="block text-lg font-bold text-gray-900 mb-3">
                        🏙️ 시/도 <span class="text-red-500 text-xl">*</span>
                    </label>
                    <select name="city" id="city"
                            class="w-full rounded-lg border-3 border-gray-300 shadow-lg px-5 py-4 text-lg font-semibold focus:border-blue-500 focus:ring focus:ring-blue-200 hover:border-blue-400 transition-colors"
                            required>
                        <option value="">시/도를 선택하세요</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city', $user->city) == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>

                <div>
                    <label for="district" class="block text-lg font-bold text-gray-900 mb-3">
                        🏘️ 구/군 <span class="text-red-500 text-xl">*</span>
                    </label>
                    <select name="district" id="district"
                            class="w-full rounded-lg border-3 border-gray-300 shadow-lg px-5 py-4 text-lg font-semibold focus:border-blue-500 focus:ring focus:ring-blue-200 hover:border-blue-400 transition-colors"
                            disabled>
                        <option value="">먼저 시/도를 선택하세요</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('district')" />
                </div>
            </div>
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
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const currentDistrict = "{{ old('district', $user->district) }}";

            // Load districts when city is pre-selected
            if (citySelect.value) {
                loadDistricts(citySelect.value);
            }

            citySelect.addEventListener('change', function() {
                const city = this.value;
                loadDistricts(city);
            });

            function loadDistricts(city) {
                if (city) {
                    districtSelect.innerHTML = '<option value="">로딩 중...</option>';
                    districtSelect.disabled = true;

                    fetch(`/api/teams/regions/${city}/districts`)
                        .then(response => response.json())
                        .then(districts => {
                            districtSelect.innerHTML = '<option value="">구/군을 선택하세요</option>';
                            districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district;
                                option.textContent = district;
                                if (district === currentDistrict) {
                                    option.selected = true;
                                }
                                districtSelect.appendChild(option);
                            });
                            districtSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching districts:', error);
                            districtSelect.innerHTML = '<option value="">오류가 발생했습니다</option>';
                        });
                } else {
                    districtSelect.innerHTML = '<option value="">먼저 시/도를 선택하세요</option>';
                    districtSelect.disabled = true;
                }
            }

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
