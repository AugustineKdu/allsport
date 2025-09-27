<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            팀 만들기
        </h2>
    </x-slot>

    <div class="py-6 pb-32 lg:pb-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('teams.store') }}">
                        @csrf

                        <!-- Team Name -->
                        <div class="mb-6">
                            <label for="team_name" class="block text-lg font-bold text-gray-900 mb-3">
                                팀 이름 <span class="text-red-500 text-xl">*</span>
                            </label>
                            <input type="text" name="team_name" id="team_name"
                                   value="{{ old('team_name') }}"
                                   class="w-full rounded-lg border-3 border-gray-300 shadow-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-4 px-5 text-lg font-semibold hover:border-blue-400 transition-colors"
                                   placeholder="예: 강남 유나이티드 FC"
                                   required>
                            @error('team_name')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-base text-gray-600 font-medium">
                                💡 팀 이름은 지역과 스포츠 내에서 고유해야 합니다.
                            </p>
                        </div>

                        <!-- Region Selection -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 팀 지역 설정</h3>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-800">
                                    💡 <strong>팁:</strong> 팀이 활동할 지역을 선택하세요. 같은 지역의 팀들과 경기할 수 있습니다.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="city" class="block text-lg font-bold text-gray-900 mb-3">
                                        🏙️ 시/도 <span class="text-red-500 text-xl">*</span>
                                    </label>
                                    <select name="city" id="city"
                                            class="w-full rounded-lg border-3 border-gray-300 shadow-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-4 px-5 text-lg font-semibold hover:border-blue-400 transition-colors"
                                            required>
                                        <option value="">시/도를 선택하세요</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}" {{ old('city', auth()->user()->city) == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="district" class="block text-lg font-bold text-gray-900 mb-3">
                                        🏘️ 구/군 <span class="text-red-500 text-xl">*</span>
                                    </label>
                                    <select name="district" id="district"
                                            class="w-full rounded-lg border-3 border-gray-300 shadow-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-4 px-5 text-lg font-semibold hover:border-blue-400 transition-colors"
                                            required disabled>
                                        <option value="">먼저 시/도를 선택하세요</option>
                                    </select>
                                    @error('district')
                                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div id="region-status" class="mt-3 text-sm text-gray-600 hidden">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    지역 선택 완료
                                </span>
                            </div>
                        </div>

                        <!-- Sport Selection -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚽ 스포츠 종목 선택</h3>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-green-800">
                                    💡 <strong>팁:</strong> 현재 축구와 풋살만 이용 가능합니다. 다른 스포츠는 베타 서비스로 추후 추가 예정입니다.
                                </p>
                            </div>

                            <div class="space-y-4">
                                @foreach($sports as $sport)
                                    @if($sport->is_active)
                                        <!-- 활성 스포츠 - 간단한 버튼 형태 -->
                                        <button type="button"
                                                class="sport-option w-full p-6 rounded-lg border-2 border-gray-300 bg-white hover:border-green-400 hover:bg-green-50 hover:shadow-lg cursor-pointer transition-all text-left group"
                                                data-sport="{{ $sport->sport_name }}">
                                            <div class="flex items-center">
                                                <span class="text-4xl mr-4 group-hover:scale-110 transition-transform duration-200">{{ $sport->icon }}</span>
                                                <div>
                                                    <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-green-700 transition-colors">{{ $sport->sport_name }}</h3>
                                                    @if($sport->status)
                                                        <p class="text-sm text-green-600 font-medium group-hover:text-green-700 transition-colors">{{ $sport->status }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    @else
                                        <!-- 베타 스포츠 (비활성화) -->
                                        <div class="w-full p-6 rounded-lg border-2 border-gray-200 bg-gray-100 opacity-60 cursor-not-allowed">
                                            <div class="flex items-center">
                                                <span class="text-4xl mr-4 grayscale">{{ $sport->icon }}</span>
                                                <div>
                                                    <h3 class="text-xl font-bold text-gray-500 mb-1">
                                                        {{ $sport->sport_name }}
                                                        <span class="ml-2 text-sm bg-orange-100 text-orange-600 px-3 py-1 rounded-full">
                                                            베타
                                                        </span>
                                                    </h3>
                                                    <p class="text-sm text-orange-600 font-medium">{{ $sport->status }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Hidden radio inputs -->
                            @foreach($sports as $sport)
                                @if($sport->is_active)
                                    <input type="radio" name="sport" value="{{ $sport->sport_name }}"
                                           id="sport_{{ $loop->index }}" class="hidden"
                                           {{ old('sport', auth()->user()->selected_sport) == $sport->sport_name ? 'checked' : '' }}
                                           required>
                                @endif
                            @endforeach

                            <div id="sport-status" class="mt-3 text-sm text-gray-600 hidden">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    스포츠 선택 완료
                                </span>
                            </div>

                            @error('sport')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Important Notice -->
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">
                                팀 생성 전 확인사항
                            </h3>
                            <div class="text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>팀을 생성하면 자동으로 팀의 소유자가 됩니다.</li>
                                    <li>팀 이름은 나중에 변경할 수 있습니다.</li>
                                    <li>같은 지역과 스포츠에 중복된 팀 이름은 사용할 수 없습니다.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('teams.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-semibold text-black hover:bg-gray-100">
                                취소
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-200 border border-gray-400 rounded-md text-sm font-semibold text-black hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                팀 만들기
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const currentDistrict = "{{ old('district', auth()->user()->district) }}";

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
                        .then(response => {
                            console.log('API Response status:', response.status);
                            return response.json();
                        })
                        .then(districts => {
                            console.log('Districts loaded:', districts);
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
            const sportOptions = document.querySelectorAll('.sport-option');
            const sportRadios = document.querySelectorAll('input[name="sport"]');
            const sportStatus = document.getElementById('sport-status');

            function updateSportSelection() {
                // Reset all button styles
                sportOptions.forEach(button => {
                    button.classList.remove('border-green-500', 'bg-green-100', 'ring-2', 'ring-green-500', 'shadow-lg');
                    button.classList.add('border-gray-300', 'bg-white');
                });

                // Check which sport is selected
                let selectedSport = null;
                sportRadios.forEach(radio => {
                    if (radio.checked) {
                        selectedSport = radio.value;
                        sportStatus.classList.remove('hidden');
                    }
                });

                // Apply selected style to the corresponding button
                if (selectedSport) {
                    const selectedButton = document.querySelector(`[data-sport="${selectedSport}"]`);
                    if (selectedButton) {
                        selectedButton.classList.remove('border-gray-300', 'bg-white');
                        selectedButton.classList.add('border-green-500', 'bg-green-100', 'ring-2', 'ring-green-500', 'shadow-lg');
                    }
                } else {
                    sportStatus.classList.add('hidden');
                }
            }

            // Add click handlers to sport option buttons
            sportOptions.forEach(button => {
                button.addEventListener('click', function() {
                    const sportValue = this.getAttribute('data-sport');

                    // Uncheck all radios
                    sportRadios.forEach(radio => {
                        radio.checked = false;
                    });

                    // Check the corresponding radio
                    const correspondingRadio = document.querySelector(`input[name="sport"][value="${sportValue}"]`);
                    if (correspondingRadio) {
                        correspondingRadio.checked = true;
                    }

                    updateSportSelection();

                    console.log('Sport selected:', sportValue);
                });
            });

            // Set initial state
            updateSportSelection();

            // Debug: Log available sports
            console.log('Available sport options:', sportOptions.length);
            console.log('Available sport radios:', sportRadios.length);

            // Update region status
            const regionStatus = document.getElementById('region-status');
            function updateRegionStatus() {
                const city = citySelect.value;
                const district = districtSelect.value;

                if (city && district) {
                    regionStatus.classList.remove('hidden');
                } else {
                    regionStatus.classList.add('hidden');
                }
            }

            // Add event listener for district change
            districtSelect.addEventListener('change', updateRegionStatus);

            // Initial check
            updateRegionStatus();
        });
    </script>
    @endpush
</x-app-layout>
