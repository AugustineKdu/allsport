<x-app-layout>
    @php
        $currentTeam = null;
        try {
            $currentTeam = auth()->user()->currentTeam();
        } catch (\Exception $e) {
            $currentTeam = null;
        }
    @endphp

     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                    안녕하세요{{ auth()->user()->nickname ? ', ' . auth()->user()->nickname . '님' : '' }}! 👋
                </h1>
                <p class="text-lg text-gray-600">스포츠를 즐기고 새로운 친구들을 만나보세요</p>
            </div>

            <!-- Onboarding Alert -->
            @if(!auth()->user()->onboarding_done || session('onboarding_reminder'))
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-amber-400 text-xl">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700 font-medium">
                                프로필 설정을 완료하면 더 많은 기능을 이용할 수 있습니다.
                            </p>
                            <p class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-amber-600 hover:text-amber-500 font-medium underline">
                                    지금 설정하기 →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- My Team Section -->
                    @if($currentTeam && $currentTeam->slug)
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">내 팀</h2>
                                <a href="{{ route('teams.show', $currentTeam->slug) }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    자세히 보기 →
                                </a>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $currentTeam->team_name }}</h3>
                                <p class="text-gray-600">
                                    📍 {{ $currentTeam->city }} {{ $currentTeam->district }} •
                                    🏃 {{ $currentTeam->sport }}
                                </p>
                            </div>

                            <div class="grid grid-cols-4 gap-4 text-center">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-green-600">{{ $currentTeam->wins }}</div>
                                    <div class="text-sm text-gray-600">승</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $currentTeam->draws }}</div>
                                    <div class="text-sm text-gray-600">무</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-red-600">{{ $currentTeam->losses }}</div>
                                    <div class="text-sm text-gray-600">패</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-blue-600">{{ $currentTeam->points }}</div>
                                    <div class="text-sm text-gray-600">포인트</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">아직 팀에 소속되지 않았습니다</h3>
                            <p class="text-gray-600 mb-6">팀에 가입하거나 새로운 팀을 만들어보세요!</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="{{ route('teams.index') }}"
                                   class="bg-gray-200 text-black px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                                    팀 찾기
                                </a>
                                @if(!auth()->user()->ownedTeams()->exists())
                                    <a href="{{ route('teams.create') }}"
                                       class="bg-gray-200 text-black px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                                        팀 만들기
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">빠른 메뉴</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <a href="{{ route('teams.index') }}"
                               class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 transition-all group">
                                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">👥</div>
                                <span class="text-base font-semibold text-black">팀 찾기</span>
                            </a>
                            <a href="{{ route('matches.index') }}"
                               class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 transition-all group">
                                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">⚽</div>
                                <span class="text-base font-semibold text-black">경기 일정</span>
                            </a>
                            <a href="{{ route('rankings.index') }}"
                               class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 transition-all group">
                                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🏆</div>
                                <span class="text-base font-semibold text-black">랭킹</span>
                            </a>
                            <a href="{{ route('mypage') }}"
                               class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 transition-all group">
                                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">👤</div>
                                <span class="text-base font-semibold text-black">마이페이지</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Stats & Info -->
                <div class="space-y-6">
                    <!-- Team Stats -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">통계</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">팀 멤버</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $currentTeam ? $currentTeam->approvedMembers->count() : 0 }}명
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">포인트</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $currentTeam ? $currentTeam->points : 0 }}점
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">선호 스포츠</span>
                                <span class="font-semibold text-gray-900">
                                    {{ auth()->user()->selected_sport ?? '미설정' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">활동 지역</span>
                                <span class="font-semibold text-gray-900 text-sm">
                                    {{ auth()->user()->city ?? '미설정' }} {{ auth()->user()->district ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">내 정보</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">닉네임</label>
                                <p class="font-medium text-gray-900">{{ auth()->user()->nickname ?? '미설정' }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">이메일</label>
                                <p class="font-medium text-gray-900 text-sm">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('profile.edit') }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    프로필 수정 →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
