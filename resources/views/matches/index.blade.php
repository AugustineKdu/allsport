<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">경기 일정</h1>
                <p class="text-lg text-gray-600">모든 경기 일정과 결과를 확인하세요</p>
            </div>

            <!-- Status Tabs -->
            <div class="bg-white rounded-lg shadow-sm border p-2 mb-6">
                <nav class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <a href="{{ route('matches.index') }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        📋 전체
                    </a>
                    <a href="{{ route('matches.index', ['status' => '예정']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '예정' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        📅 예정
                    </a>
                    <a href="{{ route('matches.index', ['status' => '진행중']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '진행중' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        ⏱️ 진행중
                    </a>
                    <a href="{{ route('matches.index', ['status' => '완료']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '완료' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        ✅ 완료
                    </a>
                </nav>
            </div>

            <!-- Matches List -->
            <div class="space-y-4">
                @forelse($matches as $match)
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div class="flex items-center space-x-3 mb-2 sm:mb-0">
                                <span class="px-3 py-1 rounded-lg text-sm font-medium
                                    @if($match->status === '예정') bg-blue-100 text-blue-800
                                    @elseif($match->status === '진행중') bg-green-100 text-green-800
                                    @elseif($match->status === '완료') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($match->status === '예정') 📅 {{ $match->status }}
                                    @elseif($match->status === '진행중') ⏱️ {{ $match->status }}
                                    @elseif($match->status === '완료') ✅ {{ $match->status }}
                                    @else ❌ {{ $match->status }}
                                    @endif
                                </span>
                                <span class="text-gray-600">
                                    🗓️ {{ $match->match_date->format('m월 d일') }}
                                    @if($match->match_time)
                                        🕐 {{ $match->match_time->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-gray-600">
                                📍 {{ $match->city }} {{ $match->district }} ·
                                @if($match->sport == '축구') ⚽
                                @elseif($match->sport == '풋살') 🥅
                                @elseif($match->sport == '농구') 🏀
                                @elseif($match->sport == '배드민턴') 🏸
                                @elseif($match->sport == '탁구') 🏓
                                @elseif($match->sport == '테니스') 🎾
                                @elseif($match->sport == '야구') ⚾
                                @else 🏃
                                @endif
                                {{ $match->sport }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1 text-center sm:text-right">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end sm:space-x-4">
                                    <div class="mb-2 sm:mb-0">
                                        <h3 class="text-gray-900 text-lg sm:text-xl font-bold mb-1">{{ $match->home_team_name }}</h3>
                                        <p class="text-gray-500 text-sm">🏠 홈</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mx-6 sm:mx-8">
                                @if($match->status === '완료')
                                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                                        <div class="text-gray-900 text-2xl sm:text-3xl font-bold">
                                            {{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-gray-400">
                                        <div class="text-2xl sm:text-3xl font-bold">VS</div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                                    <div class="mb-2 sm:mb-0">
                                        <h3 class="text-gray-900 text-lg sm:text-xl font-bold mb-1">{{ $match->away_team_name }}</h3>
                                        <p class="text-gray-500 text-sm">✈️ 원정</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($match->status === '완료' && $match->finalized_at)
                            <div class="mt-4 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm">
                                ⏰ 경기 종료: {{ $match->finalized_at->format('m.d H:i') }}
                            </div>
                        @elseif($match->status === '예정')
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    @if(auth()->user()->currentTeam())
                                        @php
                                            $currentTeam = auth()->user()->currentTeam();
                                            $canApply = $currentTeam &&
                                                       $currentTeam->sport === $match->sport &&
                                                       $currentTeam->city === $match->city &&
                                                       $currentTeam->district === $match->district &&
                                                       $currentTeam->id !== $match->home_team_id &&
                                                       $currentTeam->id !== $match->away_team_id;
                                        @endphp

                                        @if($canApply)
                                            <form action="{{ route('matches.apply', $match->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full bg-gray-200 text-black px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                                                    ⚽ 경기 신청
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-center font-medium border border-gray-300">
                                                @if(!$currentTeam)
                                                    팀이 없어서 신청할 수 없습니다
                                                @elseif($currentTeam->sport !== $match->sport)
                                                    다른 스포츠 종목입니다
                                                @elseif($currentTeam->city !== $match->city || $currentTeam->district !== $match->district)
                                                    다른 지역입니다
                                                @else
                                                    이미 참여하는 경기입니다
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-center font-medium border border-gray-300">
                                            팀 가입 후 경기 신청이 가능합니다
                                        </div>
                                    @endif

                                    <a href="{{ route('matches.show', $match->id) }}"
                                       class="bg-gray-100 text-black px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-semibold border border-gray-300 text-center">
                                        상세보기
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            @if(request('status'))
                                {{ request('status') }} 상태의 경기가 없습니다
                            @else
                                등록된 경기가 없습니다
                            @endif
                        </h3>
                        <p class="text-gray-600">새로운 경기가 등록되면 여기에 표시됩니다</p>
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
</x-app-layout>
