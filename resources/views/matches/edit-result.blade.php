<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">경기 결과 입력</h1>
                <p class="text-gray-600">경기 결과를 입력하여 팀 포인트를 업데이트하세요</p>
            </div>

            <!-- Match Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">경기 정보</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">홈팀</span>
                        <p class="font-semibold text-lg">{{ $match->home_team_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">원정팀</span>
                        <p class="font-semibold text-lg">{{ $match->away_team_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">경기 날짜</span>
                        <p class="font-semibold">{{ $match->match_date->format('Y년 m월 d일') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">경기 시간</span>
                        <p class="font-semibold">{{ $match->match_time->format('H:i') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-sm text-gray-500">스포츠</span>
                        <p class="font-semibold">{{ $match->sport }}</p>
                    </div>
                </div>
            </div>

            <!-- Result Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('matches.update-result', $match) }}">
                    @csrf
                    @method('PATCH')

                    <!-- Scores -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">경기 점수</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="home_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $match->home_team_name }} 점수
                                </label>
                                <input type="number"
                                       id="home_score"
                                       name="home_score"
                                       value="{{ old('home_score', $match->home_score) }}"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       required>
                                @error('home_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="away_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $match->away_team_name }} 점수
                                </label>
                                <input type="number"
                                       id="away_score"
                                       name="away_score"
                                       value="{{ old('away_score', $match->away_score) }}"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       required>
                                @error('away_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">경기 상태</label>
                        <select id="status"
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="진행중" {{ old('status', $match->status) == '진행중' ? 'selected' : '' }}>진행중</option>
                            <option value="완료" {{ old('status', $match->status) == '완료' ? 'selected' : '' }}>완료</option>
                            <option value="취소" {{ old('status', $match->status) == '취소' ? 'selected' : '' }}>취소</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">경기 메모 (선택사항)</label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="경기에 대한 추가 정보나 특이사항을 입력하세요">{{ old('notes', $match->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('matches.show', $match) }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            취소
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            결과 저장
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="text-blue-400 text-xl">ℹ️</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">경기 결과 입력 안내</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>경기 상태를 "완료"로 설정하면 자동으로 팀 포인트가 업데이트됩니다.</li>
                                <li>승리: 3점, 무승부: 1점, 패배: 0점</li>
                                <li>경기 결과는 입력 후 수정할 수 없으니 신중하게 입력해주세요.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
