<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">팀 랭킹</h1>
                <p class="text-lg text-gray-600">포인트 기준으로 순위를 확인하세요</p>
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

                <div class="text-center text-gray-600 mb-6">
                    @if($sport == '축구') ⚽ @elseif($sport == '풋살') 🥅 @elseif($sport == '농구') 🏀 @elseif($sport == '배드민턴') 🏸 @elseif($sport == '탁구') 🏓 @elseif($sport == '테니스') 🎾 @elseif($sport == '야구') ⚾ @else 🏃 @endif
                    {{ $sport }} 랭킹 -
                    @if($scope === 'district')
                        {{ $city ?? '' }} {{ $district ?? '' }}
                    @elseif($scope === 'city')
                        {{ $city ?? '' }}
                    @else
                        전국
                    @endif
                </div>
            @endif

            <!-- Rankings List -->
            <div class="space-y-4">
                @forelse($teams as $index => $team)
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow {{ auth()->user()->currentTeam() && auth()->user()->currentTeam()->id === $team->id ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <!-- Rank and Team Info -->
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold
                                    @if(($teams->firstItem() ?? 1) + $index === 1) bg-yellow-100 text-yellow-800
                                    @elseif(($teams->firstItem() ?? 1) + $index === 2) bg-gray-100 text-gray-800
                                    @elseif(($teams->firstItem() ?? 1) + $index === 3) bg-orange-100 text-orange-800
                                    @else bg-gray-50 text-gray-700
                                    @endif">
                                    @if(($teams->firstItem() ?? 1) + $index === 1) 🥇
                                    @elseif(($teams->firstItem() ?? 1) + $index === 2) 🥈
                                    @elseif(($teams->firstItem() ?? 1) + $index === 3) 🥉
                                    @else {{ ($teams->firstItem() ?? 1) + $index }}
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
                                    @if(auth()->user()->currentTeam() && auth()->user()->currentTeam()->id === $team->id)
                                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mt-1 inline-block">
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
                                <div class="text-gray-900 text-2xl font-bold">{{ $team->points }}점</div>
                                <div class="text-gray-500 text-sm">
                                    {{ $team->wins }}승 {{ $team->draws }}무 {{ $team->losses }}패
                                </div>
                            </div>
                        </div>

                        <!-- Team Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="text-gray-600 text-sm">총 경기</div>
                                <div class="text-gray-900 text-lg font-semibold">{{ $team->wins + $team->draws + $team->losses }}경기</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm">승률</div>
                                @php
                                    $totalGames = $team->wins + $team->draws + $team->losses;
                                    $winRate = $totalGames > 0 ? round(($team->wins / $totalGames) * 100, 1) : 0;
                                @endphp
                                <div class="text-gray-900 text-lg font-semibold">{{ $winRate }}%</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm">멤버 수</div>
                                <div class="text-gray-900 text-lg font-semibold">{{ $team->approvedMembers->count() }}명</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm">평균 득점</div>
                                @php
                                    $avgScore = $totalGames > 0 ? round($team->points / $totalGames, 1) : 0;
                                @endphp
                                <div class="text-gray-900 text-lg font-semibold">{{ $avgScore }}점</div>
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
