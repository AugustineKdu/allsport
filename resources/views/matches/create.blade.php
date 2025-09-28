<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            경기 만들기
        </h2>
    </x-slot>

    <div class="py-6 pb-32 lg:pb-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- 팀 정보 표시 -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">🏆 팀 정보</h3>
                        <div class="text-blue-700">
                            <p><strong>팀명:</strong> {{ $currentTeam->team_name }}</p>
                            <p><strong>지역:</strong> {{ $currentTeam->city }} {{ $currentTeam->district }}</p>
                            <p><strong>종목:</strong> {{ $currentTeam->sport }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('matches.store') }}">
                        @csrf

                        <!-- 스포츠 종목 (자동 설정) -->
                        <div class="mb-6">
                            <label class="block text-lg font-bold text-gray-900 mb-3">
                                ⚽ 스포츠 종목
                            </label>
                            <div class="p-4 bg-gray-100 rounded-lg border-2 border-gray-300">
                                <span class="text-xl font-semibold text-gray-700">{{ $currentTeam->sport }}</span>
                                <p class="text-sm text-gray-600 mt-1">팀의 스포츠 종목으로 자동 설정됩니다.</p>
                            </div>
                            <input type="hidden" name="sport" value="{{ $currentTeam->sport }}">
                        </div>

                        <!-- 지역 (자동 설정) -->
                        <div class="mb-6">
                            <label class="block text-lg font-bold text-gray-900 mb-3">
                                📍 경기 지역
                            </label>
                            <div class="p-4 bg-gray-100 rounded-lg border-2 border-gray-300">
                                <span class="text-xl font-semibold text-gray-700">{{ $currentTeam->city }} {{ $currentTeam->district }}</span>
                                <p class="text-sm text-gray-600 mt-1">팀의 지역으로 자동 설정됩니다.</p>
                            </div>
                            <input type="hidden" name="city" value="{{ $currentTeam->city }}">
                            <input type="hidden" name="district" value="{{ $currentTeam->district }}">
                        </div>

                        <!-- 경기 날짜 -->
                        <div class="mb-6">
                            <label for="match_date" class="block text-lg font-bold text-gray-900 mb-3">
                                📅 경기 날짜 <span class="text-red-500 text-xl">*</span>
                            </label>
                            <input type="date" name="match_date" id="match_date"
                                   value="{{ old('match_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full rounded-lg border-3 border-gray-300 shadow-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-4 px-5 text-lg font-semibold hover:border-blue-400 transition-colors"
                                   required>
                            @error('match_date')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-base text-gray-600 font-medium">
                                💡 내일 이후의 날짜를 선택해주세요.
                            </p>
                        </div>

                        <!-- 경기 시간 -->
                        <div class="mb-6">
                            <label for="match_time" class="block text-lg font-bold text-gray-900 mb-3">
                                🕐 경기 시간 <span class="text-red-500 text-xl">*</span>
                            </label>
                            <input type="time" name="match_time" id="match_time"
                                   value="{{ old('match_time') }}"
                                   class="w-full rounded-lg border-3 border-gray-300 shadow-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-4 px-5 text-lg font-semibold hover:border-blue-400 transition-colors"
                                   required>
                            @error('match_time')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-base text-gray-600 font-medium">
                                💡 경기 시작 시간을 설정해주세요.
                            </p>
                        </div>

                        <!-- 안내 메시지 -->
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">
                                경기 생성 안내
                            </h3>
                            <div class="text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>경기를 생성하면 홈팀으로 자동 설정됩니다.</li>
                                    <li>같은 지역의 다른 팀이 원정팀으로 참여할 수 있습니다.</li>
                                    <li>경기 생성 후 상대팀 매칭을 기다려야 합니다.</li>
                                    <li>경기 정보는 나중에 수정할 수 있습니다.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- 제출 버튼 -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('matches.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-semibold text-black hover:bg-gray-100">
                                취소
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 border border-blue-700 rounded-md text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                경기 만들기
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
