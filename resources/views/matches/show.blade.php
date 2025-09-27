<x-app-layout>
    <div class="py-12 pb-32 lg:pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">경기 상세정보</h1>
                    <a href="{{ route('matches.index') }}"
                       class="bg-gray-100 text-black px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-semibold border border-gray-300">
                        ← 목록으로
                    </a>
                </div>

                <!-- Status Badge -->
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($match->status === '예정') bg-blue-100 text-blue-800
                    @elseif($match->status === '진행중') bg-yellow-100 text-yellow-800
                    @elseif($match->status === '완료') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @if($match->status === '예정') 📅 예정
                    @elseif($match->status === '진행중') ⚡ 진행중
                    @elseif($match->status === '완료') ✅ 완료
                    @else ❌ 취소
                    @endif
                </div>
            </div>

            <!-- Match Details Card -->
            <div class="bg-white rounded-lg shadow-lg border overflow-hidden">
                <!-- Match Header -->
                <div class="bg-gradient-to-r from-blue-50 to-green-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $match->sport }} 경기</h2>
                            <p class="text-gray-600">{{ $match->city }} {{ $match->district }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $match->match_date->format('Y.m.d') }}
                            </div>
                            <div class="text-gray-600">
                                {{ $match->match_time->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teams -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Home Team -->
                        <div class="text-center">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">🏠 홈팀</h3>
                                @if($match->homeTeam)
                                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                        <h4 class="text-xl font-bold text-blue-900 mb-2">{{ $match->home_team_name }}</h4>
                                        <p class="text-blue-700 text-sm">{{ $match->homeTeam->city }} {{ $match->homeTeam->district }}</p>
                                        @if(auth()->user()->currentTeam() && auth()->user()->currentTeam()->id === $match->homeTeam->id)
                                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mt-2">
                                                ⭐ 내 팀
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <p class="text-gray-500">팀 정보 없음</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Away Team -->
                        <div class="text-center">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">✈️ 원정팀</h3>
                                @if($match->awayTeam)
                                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                        <h4 class="text-xl font-bold text-green-900 mb-2">{{ $match->away_team_name }}</h4>
                                        <p class="text-green-700 text-sm">{{ $match->awayTeam->city }} {{ $match->awayTeam->district }}</p>
                                        @if(auth()->user()->currentTeam() && auth()->user()->currentTeam()->id === $match->awayTeam->id)
                                            <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mt-2">
                                                ⭐ 내 팀
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <p class="text-gray-500">원정팀 모집중</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Score (if completed) -->
                    @if($match->status === '완료' && $match->home_score !== null && $match->away_score !== null)
                        <div class="mt-8 text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">경기 결과</h3>
                            <div class="bg-gray-100 rounded-lg p-6">
                                <div class="text-4xl font-bold text-gray-900">
                                    {{ $match->home_score }} : {{ $match->away_score }}
                                </div>
                                @if($match->finalized_at)
                                    <p class="text-gray-600 mt-2">
                                        종료: {{ $match->finalized_at->format('Y.m.d H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Match Application (if scheduled and user can apply) -->
                    @if($match->status === '예정')
                        @php
                            $currentTeam = auth()->user()->currentTeam();
                            $canApply = $currentTeam &&
                                       $currentTeam->sport === $match->sport &&
                                       $currentTeam->city === $match->city &&
                                       $currentTeam->district === $match->district &&
                                       $currentTeam->id !== $match->home_team_id &&
                                       $currentTeam->id !== $match->away_team_id &&
                                       !$match->away_team_id;
                        @endphp

                        @if($canApply)
                            <div class="mt-8 text-center">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">경기 신청</h3>
                                <form action="{{ route('matches.apply', $match->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="bg-gray-200 text-black px-8 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400 text-lg">
                                        ⚽ 경기 신청하기
                                    </button>
                                </form>
                                <p class="text-gray-600 text-sm mt-2">
                                    원정팀으로 참여하게 됩니다
                                </p>
                            </div>
                        @elseif($currentTeam && $currentTeam->sport === $match->sport && $currentTeam->city === $match->city && $currentTeam->district === $match->district)
                            <div class="mt-8 text-center">
                                <div class="bg-gray-100 rounded-lg p-4">
                                    <p class="text-gray-600">
                                        @if($currentTeam->id === $match->home_team_id || $currentTeam->id === $match->away_team_id)
                                            이미 참여하는 경기입니다
                                        @elseif($match->away_team_id)
                                            이미 팀이 모두 확정되었습니다
                                        @else
                                            경기 신청 조건을 확인해주세요
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Match Info -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-semibold text-gray-700">경기 등록자:</span>
                            <span class="text-gray-600">{{ $match->creator->nickname ?? '알 수 없음' }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">등록일:</span>
                            <span class="text-gray-600">{{ $match->created_at->format('Y.m.d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
