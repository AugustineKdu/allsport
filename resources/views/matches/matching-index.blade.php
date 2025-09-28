<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎯 매칭 찾기
        </h2>
    </x-slot>

    <div class="py-6 pb-32 lg:pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 매칭 필터</h3>
                <form method="GET" action="{{ route('matches.matching.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="matching_type" class="block text-sm font-medium text-gray-700 mb-2">매칭 범위</label>
                        <select name="matching_type" id="matching_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">전체</option>
                            <option value="local" {{ request('matching_type') == 'local' ? 'selected' : '' }}>📍 지역 매칭</option>
                            <option value="national" {{ request('matching_type') == 'national' ? 'selected' : '' }}>🌍 전국 매칭</option>
                        </select>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">지역</label>
                        <select name="city" id="city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">전체 지역</option>
                            @foreach(\App\Models\Region::active()->select('city')->distinct()->orderBy('city')->pluck('city') as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            검색
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Team Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">내 팀 정보</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-blue-700">
                    <div><strong>팀명:</strong> {{ $currentTeam->team_name }}</div>
                    <div><strong>스포츠:</strong> {{ $currentTeam->sport }}</div>
                    <div><strong>지역:</strong> {{ $currentTeam->city }} {{ $currentTeam->district }}</div>
                </div>
                <p class="text-sm text-blue-600 mt-2">
                    💡 같은 스포츠 종목의 매칭만 표시됩니다.
                </p>
            </div>

            <!-- Matches List -->
            <div class="space-y-6">
                @forelse($matches as $match)
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <!-- Match Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex items-center space-x-2">
                                        @if($match->matching_type === 'national')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">🌍 전국</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">📍 지역</span>
                                        @endif
                                        @if($match->sport == '축구') ⚽ @elseif($match->sport == '풋살') 🥅 @elseif($match->sport == '농구') 🏀 @elseif($match->sport == '배드민턴') 🏸 @elseif($match->sport == '탁구') 🏓 @elseif($match->sport == '테니스') 🎾 @elseif($match->sport == '야구') ⚾ @else 🏃 @endif
                                        <span class="text-sm text-gray-600">{{ $match->sport }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        신청자: {{ $match->applications->count() }}명
                                        @if($match->max_applicants)
                                            / {{ $match->max_applicants }}명
                                        @endif
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $match->home_team_name }} vs 상대팀 모집
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="text-sm text-gray-600">📅 경기 일시</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $match->match_date->format('Y년 m월 d일') }} {{ $match->match_time->format('H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">📍 경기 지역</div>
                                        <div class="font-semibold text-gray-900">
                                            @if($match->city && $match->district)
                                                {{ $match->city }} {{ $match->district }}
                                            @else
                                                전국
                                            @endif
                                        </div>
                                    </div>
                                    @if($match->venue)
                                        <div>
                                            <div class="text-sm text-gray-600">🏟️ 경기장</div>
                                            <div class="font-semibold text-gray-900">{{ $match->venue }}</div>
                                        </div>
                                    @endif
                                    @if($match->matching_deadline)
                                        <div>
                                            <div class="text-sm text-gray-600">⏰ 신청 마감</div>
                                            <div class="font-semibold text-gray-900">
                                                {{ $match->matching_deadline->format('m월 d일 H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($match->match_description)
                                    <div class="mb-4">
                                        <div class="text-sm text-gray-600 mb-1">📝 경기 설명</div>
                                        <p class="text-gray-900">{{ $match->match_description }}</p>
                                    </div>
                                @endif

                                <!-- Contact Info -->
                                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                    <div class="text-sm text-gray-600 mb-2">📞 연락처 정보</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                        <div><strong>담당자:</strong> {{ $match->contact_person }}</div>
                                        <div><strong>연락처:</strong>
                                            <a href="tel:{{ $match->contact_phone }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $match->contact_phone }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="lg:ml-6 mt-4 lg:mt-0">
                                @if($match->matching_deadline && now() > $match->matching_deadline)
                                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg text-center">
                                        <div class="font-semibold">신청 마감</div>
                                        <div class="text-sm">마감일이 지났습니다</div>
                                    </div>
                                @elseif($match->max_applicants && $match->applications->count() >= $match->max_applicants)
                                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg text-center">
                                        <div class="font-semibold">신청 마감</div>
                                        <div class="text-sm">신청자가 가득 찼습니다</div>
                                    </div>
                                @else
                                    <button onclick="openApplicationModal({{ $match->id }})"
                                            class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                        매칭 신청하기
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                        <div class="text-6xl mb-4">🎯</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">매칭이 없습니다</h3>
                        <p class="text-gray-600 mb-4">현재 조건에 맞는 매칭이 없습니다.</p>
                        <a href="{{ route('matches.matching.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                            매칭 만들기
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($matches->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $matches->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">매칭 신청</h3>

                <form id="applicationForm" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">연락 담당자</label>
                            <input type="text" name="contact_person" id="contact_person"
                                   value="{{ auth()->user()->name }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">연락처</label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   value="{{ auth()->user()->phone }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="010-1234-5678"
                                   required>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">메시지 (선택사항)</label>
                            <textarea name="message" id="message" rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="팀 소개나 특별한 요청사항이 있다면 작성해주세요"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeApplicationModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            취소
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-semibold text-white hover:bg-blue-700">
                            신청하기
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openApplicationModal(matchId) {
            const modal = document.getElementById('applicationModal');
            const form = document.getElementById('applicationForm');
            form.action = `/matches/${matchId}/apply-for-match`;
            modal.classList.remove('hidden');
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
