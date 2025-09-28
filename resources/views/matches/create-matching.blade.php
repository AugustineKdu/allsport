<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎯 매칭 만들기
        </h2>
    </x-slot>

    <div class="py-6 pb-32 lg:pb-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('matches.matching.store') }}">
                        @csrf

                        <!-- Team Info -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-bold text-blue-800 mb-2">홈팀 정보</h3>
                            <p class="text-base text-blue-700">
                                <strong>팀 이름:</strong> {{ $currentTeam->team_name }}
                            </p>
                            <p class="text-base text-blue-700">
                                <strong>스포츠:</strong> {{ $currentTeam->sport }}
                            </p>
                            <p class="text-base text-blue-700">
                                <strong>지역:</strong> {{ $currentTeam->city }} {{ $currentTeam->district }}
                            </p>
                        </div>

                        <!-- Matching Type -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🌍 매칭 범위</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative">
                                    <input type="radio" name="matching_type" value="local" class="sr-only" checked>
                                    <div class="matching-type-option p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">📍</div>
                                            <h4 class="font-semibold text-gray-900">지역 매칭</h4>
                                            <p class="text-sm text-gray-600">같은 지역 팀들과 매칭</p>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="matching_type" value="national" class="sr-only">
                                    <div class="matching-type-option p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">🌍</div>
                                            <h4 class="font-semibold text-gray-900">전국 매칭</h4>
                                            <p class="text-sm text-gray-600">전국 어디든 매칭 가능</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Region Selection (for local matching) -->
                        <div id="region-selection" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 지역 설정</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">시/도</label>
                                    <select name="city" id="city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">시/도를 선택하세요</option>
                                        @foreach($regions->groupBy('city')->keys() as $city)
                                            <option value="{{ $city }}" {{ old('city', $currentTeam->city) == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="district" class="block text-sm font-medium text-gray-700 mb-2">구/군</label>
                                    <select name="district" id="district" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">구/군을 선택하세요</option>
                                        @foreach($regions->where('city', old('city', $currentTeam->city)) as $region)
                                            <option value="{{ $region->district }}" {{ old('district', $currentTeam->district) == $region->district ? 'selected' : '' }}>
                                                {{ $region->district }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Match Date and Time -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 경기 일시 설정</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="match_date" class="block text-sm font-medium text-gray-700 mb-2">경기 날짜</label>
                                    <input type="date" name="match_date" id="match_date"
                                           value="{{ old('match_date') }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           required>
                                </div>
                                <div>
                                    <label for="match_time" class="block text-sm font-medium text-gray-700 mb-2">경기 시간</label>
                                    <input type="time" name="match_time" id="match_time"
                                           value="{{ old('match_time') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           required>
                                </div>
                            </div>
                        </div>

                        <!-- Match Details -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 경기 상세 정보</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="match_description" class="block text-sm font-medium text-gray-700 mb-2">경기 설명</label>
                                    <textarea name="match_description" id="match_description" rows="3"
                                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="경기에 대한 설명을 입력하세요 (예: 친선경기, 토너먼트 등)">{{ old('match_description') }}</textarea>
                                </div>
                                <div>
                                    <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">경기장 정보</label>
                                    <input type="text" name="venue" id="venue"
                                           value="{{ old('venue') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="경기장 이름 또는 주소">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📞 연락처 정보</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">연락 담당자</label>
                                    <input type="text" name="contact_person" id="contact_person"
                                           value="{{ old('contact_person', auth()->user()->name) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           required>
                                </div>
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">연락처</label>
                                    <input type="tel" name="contact_phone" id="contact_phone"
                                           value="{{ old('contact_phone', auth()->user()->phone) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="010-1234-5678"
                                           required>
                                </div>
                            </div>
                        </div>

                        <!-- Matching Settings -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚙️ 매칭 설정</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="matching_deadline" class="block text-sm font-medium text-gray-700 mb-2">신청 마감일</label>
                                    <input type="date" name="matching_deadline" id="matching_deadline"
                                           value="{{ old('matching_deadline') }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">비워두면 경기 당일까지 신청 가능</p>
                                </div>
                                <div>
                                    <label for="max_applicants" class="block text-sm font-medium text-gray-700 mb-2">최대 신청팀 수</label>
                                    <input type="number" name="max_applicants" id="max_applicants"
                                           value="{{ old('max_applicants', 10) }}"
                                           min="1" max="20"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">몇 팀까지 신청을 받을지 설정</p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="sport" value="{{ $currentTeam->sport }}">

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('matches.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-semibold text-black hover:bg-gray-100">
                                취소
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                매칭 만들기
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
            const matchingTypeRadios = document.querySelectorAll('input[name="matching_type"]');
            const regionSelection = document.getElementById('region-selection');
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');

            // Matching type change handler
            matchingTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'national') {
                        regionSelection.style.display = 'none';
                        citySelect.required = false;
                        districtSelect.required = false;
                    } else {
                        regionSelection.style.display = 'block';
                        citySelect.required = true;
                        districtSelect.required = true;
                    }
                });
            });

            // City change handler
            citySelect.addEventListener('change', function() {
                const city = this.value;
                if (city) {
                    fetch(`/api/teams/regions/${city}/districts`)
                        .then(response => response.json())
                        .then(districts => {
                            districtSelect.innerHTML = '<option value="">구/군을 선택하세요</option>';
                            districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district;
                                option.textContent = district;
                                districtSelect.appendChild(option);
                            });
                        });
                } else {
                    districtSelect.innerHTML = '<option value="">구/군을 선택하세요</option>';
                }
            });

            // Visual feedback for matching type selection
            document.querySelectorAll('.matching-type-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    document.querySelectorAll('.matching-type-option').forEach(opt => {
                        opt.classList.remove('border-blue-500', 'bg-blue-50');
                        opt.classList.add('border-gray-300');
                    });

                    // Add active class to clicked option
                    this.classList.remove('border-gray-300');
                    this.classList.add('border-blue-500', 'bg-blue-50');
                });
            });

            // Set initial state
            document.querySelector('input[name="matching_type"][value="local"]').checked = true;
            document.querySelector('.matching-type-option').classList.add('border-blue-500', 'bg-blue-50');
        });
    </script>
    @endpush
</x-app-layout>
