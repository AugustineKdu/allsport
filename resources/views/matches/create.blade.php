<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">경기 생성</h1>
                <p class="text-gray-600">새로운 경기를 생성하여 다른 팀과 경기할 수 있습니다.</p>
            </div>

            <!-- Current Team Info -->
            <div class="bg-blue-50 rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-blue-900 mb-2">홈팀</h2>
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-lg">{{ substr($currentTeam->team_name, 0, 2) }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $currentTeam->team_name }}</h3>
                        <p class="text-gray-600">{{ $currentTeam->city }} {{ $currentTeam->district }} • {{ $currentTeam->sport }}</p>
                    </div>
                </div>
            </div>

            <!-- Match Creation Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('matches.store') }}">
                    @csrf

                    <!-- Away Team Selection -->
                    <div class="mb-6">
                        <label for="away_team_id" class="block text-sm font-medium text-gray-700 mb-2">
                            상대팀 선택 <span class="text-red-500">*</span>
                        </label>
                        <select name="away_team_id" id="away_team_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">상대팀을 선택하세요</option>
                            @foreach($availableTeams as $team)
                                <option value="{{ $team->id }}">
                                    {{ $team->team_name }} ({{ $team->city }} {{ $team->district }})
                                </option>
                            @endforeach
                        </select>
                        @error('away_team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Match Date -->
                    <div class="mb-6">
                        <label for="match_date" class="block text-sm font-medium text-gray-700 mb-2">
                            경기 날짜 <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="match_date" id="match_date" required
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('match_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Match Time -->
                    <div class="mb-6">
                        <label for="match_time" class="block text-sm font-medium text-gray-700 mb-2">
                            경기 시간 <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="match_time" id="match_time" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('match_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Venue -->
                    <div class="mb-6">
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                            경기장 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="venue" id="venue" required
                               placeholder="예: 송파구 체육관"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('venue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-8">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            메모 (선택사항)
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="경기에 대한 추가 정보나 요청사항을 입력하세요."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('matches.index') }}"
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            취소
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            경기 생성
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>