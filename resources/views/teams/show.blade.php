<x-app-layout>
    <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
        <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">
                        @if($team->sport == '축구') ⚽
                        @elseif($team->sport == '풋살') 🥅
                        @elseif($team->sport == '농구') 🏀
                        @elseif($team->sport == '배드민턴') 🏸
                        @elseif($team->sport == '탁구') 🏓
                        @elseif($team->sport == '테니스') 🎾
                        @elseif($team->sport == '야구') ⚾
                        @else 🏃
                        @endif
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">{{ $team->team_name }}</h1>
                @if(auth()->user()->id === $team->owner_user_id)
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg inline-block">
                        <span class="font-medium">👑 내가 소유한 팀</span>
                    </div>
                @elseif($membership && $membership->status === 'approved')
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg inline-block">
                        <span class="font-medium">✅ 소속 팀</span>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                @if(auth()->user()->id === $team->owner_user_id)
                    <a href="{{ route('teams.manage', $team->slug) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors relative flex items-center justify-center">
                        <span class="text-xl mr-2">⚙️</span>
                        <span class="font-semibold">팀 관리</span>
                        @if($pendingCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('matches.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <span class="text-xl mr-2">⚽</span>
                        <span class="font-semibold">경기 일정</span>
                    </a>
                @endif
                <a href="{{ route('teams.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center">
                    <span class="text-xl mr-2">🔍</span>
                    <span class="font-semibold">다른 팀 찾기</span>
                </a>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Team Info -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">📋 팀 정보</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">📍 지역</span>
                            <span class="text-gray-900 font-semibold">{{ $team->city }} {{ $team->district }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">🏃 종목</span>
                            <span class="text-gray-900 font-semibold">{{ $team->sport }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">👑 팀장</span>
                            <span class="text-gray-900 font-semibold">{{ $team->owner->nickname }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">📅 창단일</span>
                            <span class="text-gray-900 font-semibold">{{ $team->created_at->format('Y년 m월 d일') }}</span>
                        </div>
                        @if($team->join_code)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">🔐 가입 코드</span>
                                <span class="text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $team->join_code }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Stats -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">🏆 성적</h3>
                    <div class="text-center">
                        <div class="text-gray-900 text-2xl font-bold mb-2">{{ $team->wins }}승 {{ $team->draws }}무 {{ $team->losses }}패</div>
                        <div class="text-gray-600 text-lg mb-4">{{ $team->points }}점</div>
                        @php
                            $totalGames = $team->wins + $team->draws + $team->losses;
                            $winRate = $totalGames > 0 ? round(($team->wins / $totalGames) * 100, 1) : 0;
                        @endphp
                        <div class="text-gray-500">승률 {{ $winRate }}%</div>
                    </div>
                </div>

                <!-- Member Stats -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">👥 멤버</h3>
                    <div class="text-center">
                        <div class="text-gray-900 text-2xl font-bold mb-2">{{ $team->approvedMembers->count() }}명</div>
                        <div class="text-green-600">온라인 {{ $onlineMembers->count() }}명</div>
                    </div>
                </div>
            </div>

            <!-- Join Application Form -->
            @if(!$membership || $membership->status !== 'approved')
                @if(!auth()->user()->currentTeam())
                    @if($membership && $membership->status === 'pending')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                            <div class="text-center">
                                <div class="text-4xl mb-3">⏳</div>
                                <p class="text-gray-900 font-semibold">가입 신청 대기 중입니다</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">💬 가입 신청</h3>
                            <form action="{{ route('teams.apply', $team->slug) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="message" class="block text-gray-700 font-medium mb-2">
                                        가입 신청 메시지 (선택사항)
                                    </label>
                                    <textarea name="message" id="message" rows="3"
                                              class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="간단한 자기소개나 가입 동기를 적어주세요."></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full py-3 bg-gray-200 text-black font-bold rounded-lg hover:bg-gray-300 transition-colors border border-gray-400">
                                    가입 신청하기 🚀
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            @endif

            <!-- Matches Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Upcoming Matches -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">📅 예정된 경기</h3>
                    @if($upcomingMatches->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingMatches as $match)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-600 text-sm">
                                            {{ $match->match_date->format('m월 d일') }}
                                            {{ $match->match_time ? $match->match_time->format('H:i') : '' }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                            예정
                                        </span>
                                    </div>
                                    <div class="text-gray-900 font-semibold">
                                        @if($match->home_team_id === $team->id)
                                            <span class="text-blue-600">{{ $match->home_team_name }}</span>
                                            <span class="text-gray-500 mx-2">vs</span>
                                            <span>{{ $match->away_team_name }}</span>
                                            <span class="text-xs text-gray-500 ml-1">(홈)</span>
                                        @else
                                            <span>{{ $match->home_team_name }}</span>
                                            <span class="text-gray-500 mx-2">vs</span>
                                            <span class="text-blue-600">{{ $match->away_team_name }}</span>
                                            <span class="text-xs text-gray-500 ml-1">(원정)</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-2">📅</div>
                            <p class="text-gray-600">예정된 경기가 없습니다</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Matches -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">🏆 최근 경기 결과</h3>
                    @if($recentMatches->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentMatches as $match)
                                @php
                                    $isHome = $match->home_team_id === $team->id;
                                    $teamScore = $isHome ? $match->home_score : $match->away_score;
                                    $opponentScore = $isHome ? $match->away_score : $match->home_score;
                                    $result = $teamScore > $opponentScore ? '승' : ($teamScore < $opponentScore ? '패' : '무');
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-600 text-sm">
                                            {{ $match->match_date->format('m월 d일') }}
                                        </span>
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            @if($result === '승') bg-green-100 text-green-800
                                            @elseif($result === '패') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $result }}
                                        </span>
                                    </div>
                                    <div class="text-gray-900">
                                        @if($isHome)
                                            <span class="font-semibold">{{ $match->home_team_name }}</span>
                                            <span class="mx-2 font-bold text-lg">{{ $match->home_score }} : {{ $match->away_score }}</span>
                                            <span>{{ $match->away_team_name }}</span>
                                        @else
                                            <span>{{ $match->home_team_name }}</span>
                                            <span class="mx-2 font-bold text-lg">{{ $match->home_score }} : {{ $match->away_score }}</span>
                                            <span class="font-semibold">{{ $match->away_team_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-2">🏆</div>
                            <p class="text-gray-600">최근 경기 기록이 없습니다</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">👥 팀 멤버 ({{ $team->approvedMembers->count() }}명)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($team->approvedMembers as $member)
                        <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-700 font-bold">
                                    {{ mb_substr($member->user->nickname, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <span class="text-gray-900 font-semibold">{{ $member->user->nickname }}</span>
                                    @if($member->role === 'owner')
                                        <span class="ml-2 text-yellow-500">👑</span>
                                    @endif
                                    @if($member->isOnline())
                                        <span class="ml-2 w-2 h-2 bg-green-400 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-gray-500 text-xs">
                                    가입일: {{ $member->joined_at->format('Y.m.d') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
