<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full mb-4">
                    <span class="text-4xl">🏆</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">AllSports 팀 랭킹</h1>
                <p class="text-lg text-gray-600 mb-4">포인트 기준으로 순위를 확인하고 경쟁하세요!</p>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 max-w-2xl mx-auto">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold text-blue-600">💡 랭킹 시스템:</span>
                        승리 시 +3점, 무승부 시 +1점, 패배 시 0점으로 계산됩니다.
                        더 많은 경기에 참여하고 승리하여 상위권에 도전해보세요!
                    </p>
                </div>
            </div>

            <!-- Scope Tabs -->
            @if(isset($scope) && isset($sport))
                <div class="bg-white rounded-lg shadow-sm border p-2 mb-6">
                    <nav class="grid grid-cols-3 gap-2">
                        <a href="{{ route('rankings.index', ['scope' => 'district', 'sport' => $sport, 'city' => $city ?? '', 'district' => $district ?? '']) }}"
                           class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ $scope === 'district' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            📍 구/군
                        </a>
                        <a href="{{ route('rankings.index', ['scope' => 'city', 'sport' => $sport, 'city' => $city ?? '']) }}"
                           class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ $scope === 'city' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            🏙️ 시/도
                        </a>
                        <a href="{{ route('rankings.index', ['scope' => 'all', 'sport' => $sport]) }}"
                           class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ $scope === 'all' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            🌍 전국
                        </a>
                    </nav>
                </div>

                <div class="text-center mb-6">
                    <div class="inline-flex items-center bg-white rounded-full px-6 py-3 shadow-sm border">
                        <span class="text-2xl mr-3">
                            @if($sport == '축구') ⚽ @elseif($sport == '풋살') 🥅 @elseif($sport == '농구') 🏀 @elseif($sport == '배드민턴') 🏸 @elseif($sport == '탁구') 🏓 @elseif($sport == '테니스') 🎾 @elseif($sport == '야구') ⚾ @else 🏃 @endif
                        </span>
                        <span class="text-lg font-semibold text-gray-900">{{ $sport }} 랭킹</span>
                        <span class="text-gray-500 mx-2">•</span>
                        <span class="text-gray-600">
                            @if($scope === 'district')
                                {{ $city ?? '' }} {{ $district ?? '' }}
                            @elseif($scope === 'city')
                                {{ $city ?? '' }}
                            @else
                                전국
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- Rankings List -->
            <div class="space-y-4">
                @forelse($teams as $index => $team)
                    @php
                        $rank = ($teams->firstItem() ?? 1) + $index;
                        $isMyTeam = auth()->user()->currentTeam() && auth()->user()->currentTeam()->id === $team->id;
                    @endphp
                    <div class="bg-white rounded-lg shadow-lg border p-6 hover:shadow-xl transition-all duration-300 {{ $isMyTeam ? 'ring-2 ring-blue-500 bg-blue-50' : '' }} {{ $rank <= 3 ? 'transform hover:scale-105' : '' }}">
                        <div class="flex items-center justify-between">
                            <!-- Rank and Team Info -->
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold shadow-lg
                                    @if($rank === 1) bg-gradient-to-br from-yellow-400 to-yellow-600 text-white
                                    @elseif($rank === 2) bg-gradient-to-br from-gray-300 to-gray-500 text-white
                                    @elseif($rank === 3) bg-gradient-to-br from-orange-400 to-orange-600 text-white
                                    @else bg-gradient-to-br from-blue-100 to-blue-200 text-blue-800
                                    @endif">
                                    @if($rank === 1) 🥇
                                    @elseif($rank === 2) 🥈
                                    @elseif($rank === 3) 🥉
                                    @else {{ $rank }}
                                    @endif
                                </div>
                                <div>
                                    @if($team->slug)
                                        <a href="{{ route('teams.show', $team->slug) }}" class="text-gray-900 text-lg font-bold hover:text-gray-700 transition-colors">
                                            {{ $team->team_name }}
                                        </a>
                                    @else
                                        <span class="text-gray-900 text-lg font-bold">
                                            {{ $team->team_name }}
                                        </span>
                                    @endif
                                    @if($isMyTeam)
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-3 py-1 rounded-full text-xs font-bold mt-2 inline-block shadow-md">
                                            ⭐ 내 팀
                                        </div>
                                    @endif
                                    <p class="text-gray-600 text-sm mt-1">
                                        📍 @if(isset($scope))
                                            @if($scope === 'all')
                                                {{ $team->city }} {{ $team->district }}
                                            @elseif($scope === 'city')
                                                {{ $team->district }}
                                            @else
                                                {{ $team->district }}
                                            @endif
                                        @else
                                            {{ $team->city }} {{ $team->district }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Points -->
                            <div class="text-right">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-lg shadow-md">
                                    <div class="text-2xl font-bold">{{ $team->points }}점</div>
                                    <div class="text-green-100 text-sm">
                                        {{ $team->wins }}승 {{ $team->draws }}무 {{ $team->losses }}패
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Team Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
                            <div class="text-center bg-gray-50 rounded-lg p-3">
                                <div class="text-gray-600 text-sm font-medium">총 경기</div>
                                <div class="text-gray-900 text-xl font-bold">{{ $team->wins + $team->draws + $team->losses }}경기</div>
                            </div>
                            <div class="text-center bg-blue-50 rounded-lg p-3">
                                <div class="text-blue-600 text-sm font-medium">승률</div>
                                @php
                                    $totalGames = $team->wins + $team->draws + $team->losses;
                                    $winRate = $totalGames > 0 ? round(($team->wins / $totalGames) * 100, 1) : 0;
                                @endphp
                                <div class="text-blue-900 text-xl font-bold">{{ $winRate }}%</div>
                            </div>
                            <div class="text-center bg-green-50 rounded-lg p-3">
                                <div class="text-green-600 text-sm font-medium">멤버 수</div>
                                <div class="text-green-900 text-xl font-bold">{{ $team->approvedMembers->count() }}명</div>
                            </div>
                            <div class="text-center bg-purple-50 rounded-lg p-3">
                                <div class="text-purple-600 text-sm font-medium">평균 득점</div>
                                @php
                                    $avgScore = $totalGames > 0 ? round($team->points / $totalGames, 1) : 0;
                                @endphp
                                <div class="text-purple-900 text-xl font-bold">{{ $avgScore }}점</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                        <div class="text-6xl mb-4">🏆</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">랭킹 데이터가 없습니다</h3>
                        <p class="text-gray-600">경기 결과가 등록되면 랭킹이 표시됩니다</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($teams->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $teams->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
